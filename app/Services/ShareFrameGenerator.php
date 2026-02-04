<?php

namespace App\Services;

use App\Models\CompetitionSubmission;
use App\Models\CompetitionShareFrameTemplate;
use App\Models\SubmissionShareFrame;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class ShareFrameGenerator
{
    protected $gdAvailable;
    protected $imagickAvailable;

    public function __construct()
    {
        $this->gdAvailable = extension_loaded('gd');
        $this->imagickAvailable = extension_loaded('imagick');

        if (!$this->gdAvailable && !$this->imagickAvailable) {
            throw new Exception('Neither GD nor Imagick extension is available. Cannot generate share frames.');
        }
    }

    /**
     * Generate all share frame formats for a submission
     */
    public function generateAllFormats(CompetitionSubmission $submission, CompetitionShareFrameTemplate $template): SubmissionShareFrame
    {
        // Get or create share frame record
        $shareFrame = $submission->shareFrame()->firstOrCreate([
            'competition_submission_id' => $submission->id,
        ], [
            'template_id' => $template->id,
        ]);

        // Detect original image orientation
        $imageInfo = $this->getImageInfo($submission->image_path);
        $shareFrame->update([
            'original_width' => $imageInfo['width'],
            'original_height' => $imageInfo['height'],
            'original_orientation' => $imageInfo['orientation'],
        ]);

        // Generate QR code first (reused across all formats)
        $qrPath = $this->generateQRCode($submission);
        $shareFrame->qr_code_path = $qrPath;

        // Generate each format
        $formats = [
            'story' => ['width' => 1080, 'height' => 1920],  // 9:16
            'post' => ['width' => 1080, 'height' => 1080],   // 1:1
            'portrait' => ['width' => 1080, 'height' => 1350], // 4:5
            'landscape' => ['width' => 1200, 'height' => 675], // 16:9
        ];

        foreach ($formats as $formatName => $dimensions) {
            $framePath = $this->generateFrame(
                $submission,
                $template,
                $qrPath,
                $dimensions['width'],
                $dimensions['height'],
                $formatName
            );

            $shareFrame->{$formatName . '_frame_path'} = $framePath;
        }

        $shareFrame->incrementGeneration();
        $shareFrame->save();

        return $shareFrame;
    }

    /**
     * Generate a single frame format
     */
    protected function generateFrame(
        CompetitionSubmission $submission,
        CompetitionShareFrameTemplate $template,
        string $qrPath,
        int $outputWidth,
        int $outputHeight,
        string $formatName
    ): string {
        if ($this->gdAvailable) {
            return $this->generateFrameWithGD($submission, $template, $qrPath, $outputWidth, $outputHeight, $formatName);
        }

        return $this->generateFrameWithImagick($submission, $template, $qrPath, $outputWidth, $outputHeight, $formatName);
    }

    /**
     * Generate frame using GD library
     */
    protected function generateFrameWithGD(
        CompetitionSubmission $submission,
        CompetitionShareFrameTemplate $template,
        string $qrPath,
        int $width,
        int $height,
        string $formatName
    ): string {
        // Create output canvas
        $canvas = imagecreatetruecolor($width, $height);

        // Fill background
        $bgColor = $this->hexToRgb($template->background_color);
        $backgroundColor = imagecolorallocate($canvas, $bgColor['r'], $bgColor['g'], $bgColor['b']);
        imagefill($canvas, 0, 0, $backgroundColor);

        // Load and fit original image
        $originalPath = Storage::path($submission->image_path);
        $originalImage = $this->loadImageGD($originalPath);

        if ($originalImage) {
            $this->fitImageOnCanvas($canvas, $originalImage, $template, $width, $height);
            imagedestroy($originalImage);
        }

        // Add text overlay gradient if enabled
        if ($template->add_text_overlay_gradient) {
            $this->addGradientOverlay($canvas, $width, $height);
        }

        // Add text elements
        $this->addTextElements($canvas, $submission, $template, $width, $height);

        // Add QR code
        if ($template->show_qr_code && $qrPath) {
            $this->addQRCodeToCanvas($canvas, $qrPath, $template->qr_position, $width, $height);
        }

        // Add watermark
        if ($template->show_watermark) {
            $this->addWatermark($canvas, $template->watermark_position, $width, $height);
        }

        // Save output
        $outputPath = $this->getOutputPath($submission->id, $formatName);
        $fullPath = Storage::path($outputPath);
        
        // Ensure directory exists
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Save with compression
        imagejpeg($canvas, $fullPath, 90);
        imagedestroy($canvas);

        return $outputPath;
    }

    /**
     * Load image using GD
     */
    protected function loadImageGD(string $path)
    {
        $imageType = exif_imagetype($path);

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($path);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($path);
            case IMAGETYPE_WEBP:
                return imagecreatefromwebp($path);
            default:
                return false;
        }
    }

    /**
     * Fit image on canvas with proper aspect ratio
     */
    protected function fitImageOnCanvas($canvas, $image, CompetitionShareFrameTemplate $template, int $canvasWidth, int $canvasHeight): void
    {
        $imgWidth = imagesx($image);
        $imgHeight = imagesy($image);

        // Calculate safe area (with padding)
        $safeWidth = $canvasWidth - ($template->padding_left + $template->padding_right);
        $safeHeight = $canvasHeight - ($template->padding_top + $template->padding_bottom);

        // Calculate scaling
        $strategy = $template->image_fit_strategy;

        if ($strategy === 'contain') {
            // Fit entire image within safe area
            $scale = min($safeWidth / $imgWidth, $safeHeight / $imgHeight);
        } else {
            // Cover entire safe area
            $scale = max($safeWidth / $imgWidth, $safeHeight / $imgHeight);
        }

        $scaledWidth = (int)($imgWidth * $scale);
        $scaledHeight = (int)($imgHeight * $scale);

        // Center the image
        $destX = $template->padding_left + (int)(($safeWidth - $scaledWidth) / 2);
        $destY = $template->padding_top + (int)(($safeHeight - $scaledHeight) / 2);

        // Resample image onto canvas
        imagecopyresampled(
            $canvas,
            $image,
            $destX,
            $destY,
            0,
            0,
            $scaledWidth,
            $scaledHeight,
            $imgWidth,
            $imgHeight
        );
    }

    /**
     * Add gradient overlay for text readability
     */
    protected function addGradientOverlay($canvas, int $width, int $height): void
    {
        // Create semi-transparent black gradient from bottom
        $gradientHeight = (int)($height * 0.4);
        $gradientStart = $height - $gradientHeight;

        for ($y = $gradientStart; $y < $height; $y++) {
            $alpha = (int)(($y - $gradientStart) / $gradientHeight * 80);
            $color = imagecolorallocatealpha($canvas, 0, 0, 0, 127 - $alpha);
            imagefilledrectangle($canvas, 0, $y, $width, $y + 1, $color);
        }
    }

    /**
     * Add all text elements to canvas
     */
    protected function addTextElements($canvas, CompetitionSubmission $submission, CompetitionShareFrameTemplate $template, int $width, int $height): void
    {
        $textColor = $this->hexToRgb($template->text_color);
        $textColorAlloc = imagecolorallocate($canvas, $textColor['r'], $textColor['g'], $textColor['b']);
        $accentColor = $this->hexToRgb($template->accent_color);
        $accentColorAlloc = imagecolorallocate($canvas, $accentColor['r'], $accentColor['g'], $accentColor['b']);

        $y = $height - 200; // Start from bottom

        // CTA Message (main text)
        if ($template->cta_message) {
            $lines = explode("\n", $template->cta_message);
            foreach (array_reverse($lines) as $line) {
                $this->drawCenteredText($canvas, $line, $textColorAlloc, 32, $y, $width);
                $y -= 45;
            }
            $y -= 20;
        }

        // Photographer name
        if ($template->show_photographer_name) {
            $photographerName = $submission->photographer->name ?? 'Unknown Photographer';
            $this->drawCenteredText($canvas, 'by ' . $photographerName, $accentColorAlloc, 24, $y, $width);
            $y -= 35;
        }

        // Submission title
        if ($template->show_submission_title && $submission->title) {
            $this->drawCenteredText($canvas, '"' . Str::limit($submission->title, 40) . '"', $textColorAlloc, 20, $y, $width);
            $y -= 30;
        }

        // Competition name (at top)
        if ($template->show_competition_name) {
            $competitionName = $submission->competition->title ?? 'Photography Competition';
            $this->drawCenteredText($canvas, strtoupper($competitionName), $accentColorAlloc, 24, 60, $width);
        }
    }

    /**
     * Draw centered text
     */
    protected function drawCenteredText($canvas, string $text, $color, int $fontSize, int $y, int $width): void
    {
        // Use built-in font (GD limitation - for production, use TTF fonts)
        $font = 5; // Built-in font size 5
        $textWidth = imagefontwidth($font) * strlen($text);
        $x = (int)(($width - $textWidth) / 2);
        
        imagestring($canvas, $font, $x, $y, $text, $color);
    }

    /**
     * Add QR code to canvas
     */
    protected function addQRCodeToCanvas($canvas, string $qrPath, string $position, int $width, int $height): void
    {
        $qrFullPath = Storage::path($qrPath);
        if (!file_exists($qrFullPath)) {
            return;
        }

        $qr = imagecreatefrompng($qrFullPath);
        if (!$qr) {
            return;
        }

        $qrSize = 150;
        $margin = 40;

        [$x, $y] = $this->getPositionCoordinates($position, $width, $height, $qrSize, $margin);

        // Add white background for QR code
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, $x - 10, $y - 10, $x + $qrSize + 10, $y + $qrSize + 10, $white);

        imagecopyresampled($canvas, $qr, $x, $y, 0, 0, $qrSize, $qrSize, imagesx($qr), imagesy($qr));
        imagedestroy($qr);
    }

    /**
     * Add watermark to canvas
     */
    protected function addWatermark($canvas, string $position, int $width, int $height): void
    {
        $text = 'Photographer SB';
        $color = imagecolorallocatealpha($canvas, 255, 255, 255, 80);
        
        $font = 3;
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        $margin = 20;

        [$x, $y] = $this->getPositionCoordinates($position, $width, $height, $textWidth, $margin);

        imagestring($canvas, $font, $x, $y, $text, $color);
    }

    /**
     * Get position coordinates based on position string
     */
    protected function getPositionCoordinates(string $position, int $width, int $height, int $elementSize, int $margin): array
    {
        switch ($position) {
            case 'top-left':
                return [$margin, $margin];
            case 'top-right':
                return [$width - $elementSize - $margin, $margin];
            case 'bottom-left':
                return [$margin, $height - $elementSize - $margin];
            case 'bottom-right':
            default:
                return [$width - $elementSize - $margin, $height - $elementSize - $margin];
        }
    }

    /**
     * Generate frame using Imagick (fallback)
     */
    protected function generateFrameWithImagick(
        CompetitionSubmission $submission,
        CompetitionShareFrameTemplate $template,
        string $qrPath,
        int $width,
        int $height,
        string $formatName
    ): string {
        // Imagick implementation (simplified version)
        $canvas = new \Imagick();
        $canvas->newImage($width, $height, new \ImagickPixel($template->background_color));
        $canvas->setImageFormat('jpeg');

        // Load original image
        $originalPath = Storage::path($submission->image_path);
        $originalImage = new \Imagick($originalPath);

        // Fit image (simplified - use contain strategy)
        $safeWidth = $width - ($template->padding_left + $template->padding_right);
        $safeHeight = $height - ($template->padding_top + $template->padding_bottom);
        
        $originalImage->thumbnailImage($safeWidth, $safeHeight, true);
        
        $destX = $template->padding_left + (int)(($safeWidth - $originalImage->getImageWidth()) / 2);
        $destY = $template->padding_top + (int)(($safeHeight - $originalImage->getImageHeight()) / 2);
        
        $canvas->compositeImage($originalImage, \Imagick::COMPOSITE_OVER, $destX, $destY);
        $originalImage->destroy();

        // Save output
        $outputPath = $this->getOutputPath($submission->id, $formatName);
        $fullPath = Storage::path($outputPath);
        
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $canvas->setImageCompressionQuality(90);
        $canvas->writeImage($fullPath);
        $canvas->destroy();

        return $outputPath;
    }

    /**
     * Generate QR code for submission
     */
    protected function generateQRCode(CompetitionSubmission $submission): string
    {
        $qrGenerator = new QRCodeService();
        return $qrGenerator->generateForSubmission($submission);
    }

    /**
     * Get image info
     */
    protected function getImageInfo(string $path): array
    {
        $fullPath = Storage::path($path);
        
        if ($this->gdAvailable) {
            $size = getimagesize($fullPath);
            $width = $size[0];
            $height = $size[1];
        } else {
            $imagick = new \Imagick($fullPath);
            $width = $imagick->getImageWidth();
            $height = $imagick->getImageHeight();
            $imagick->destroy();
        }

        $orientation = 'square';
        if ($height > $width * 1.2) {
            $orientation = 'portrait';
        } elseif ($width > $height * 1.2) {
            $orientation = 'landscape';
        }

        return [
            'width' => $width,
            'height' => $height,
            'orientation' => $orientation,
        ];
    }

    /**
     * Get output path for generated frame
     */
    protected function getOutputPath(int $submissionId, string $formatName): string
    {
        $hash = Str::random(16);
        return "share-frames/{$submissionId}/{$formatName}_{$hash}.jpg";
    }

    /**
     * Convert hex color to RGB
     */
    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }
}
