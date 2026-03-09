<?php

namespace App\Services;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateGenerator
{
    public function generateCertificate(Certificate $certificate): Certificate
    {
        try {
            $qrPath = $this->generateQRCode($certificate);
            $certificate->verification_qr_path = $qrPath;

            $pdfPath = $this->generatePDF($certificate);
            $certificate->pdf_path = $pdfPath;
            $certificate->certificate_path = $pdfPath;

            $pngPath = $this->generatePNG($certificate);
            $certificate->png_path = $pngPath;

            $sharePaths = $this->generateShareImages($certificate, $pngPath);
            $certificate->share_image_paths = $sharePaths;
            $certificate->share_message = 'Proud to receive this certificate from Photographer SB';

            $certificate->save();

            $certificate->logs()->create([
                'user_id' => Auth::id(),
                'action_type' => 'generated',
                'entity_type' => 'certificate',
                'entity_id' => $certificate->id,
                'message' => 'Certificate artifact generated',
            ]);

            return $certificate;
        } catch (\Throwable $e) {
            Log::error('Certificate generation failed', [
                'certificate_id' => $certificate->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function generateQRCode(Certificate $certificate): string
    {
        $verifyUrl = route('certificate.verify', $certificate->certificate_code);

        $qr = QrCode::format('png')
            ->size(260)
            ->errorCorrection('H')
            ->generate($verifyUrl);

        $filename = sprintf('qr-code-%s.png', $certificate->certificate_code);
        $path = "certificates/qr-codes/{$filename}";

        Storage::disk('public')->put($path, $qr);

        return $path;
    }

    protected function generatePDF(Certificate $certificate): string
    {
        $template = $certificate->template;
        $html = $this->buildCertificateHTML($certificate, $template);
        $orientation = $this->resolveOrientation($template?->width, $template?->height);
        $pdf = Pdf::loadHTML($html)->setPaper('a4', $orientation);

        $filename = sprintf('cert-%s.pdf', $certificate->certificate_code);
        $pdfPath = $this->resolveBaseDirectory($certificate) . "/{$filename}";
        Storage::disk('public')->put($pdfPath, $pdf->output());

        return $pdfPath;
    }

    protected function generatePNG(Certificate $certificate): string
    {
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('GD extension is required to generate PNG certificate previews.');
        }

        $template = $certificate->template;
        $orientation = $this->resolveOrientation($template?->width, $template?->height);

        $width = $orientation === 'portrait' ? 1200 : 1600;
        $height = $orientation === 'portrait' ? 1600 : 1200;

        $image = imagecreatetruecolor($width, $height);

        [$bgR, $bgG, $bgB] = $this->hexToRgb($template?->background_color ?? '#ffffff');
        [$accentR, $accentG, $accentB] = $this->hexToRgb($template?->accent_color ?? '#8e0e3f');
        [$textR, $textG, $textB] = $this->hexToRgb($template?->text_color ?? '#111827');

        $bg = imagecolorallocate($image, $bgR, $bgG, $bgB);
        $accent = imagecolorallocate($image, $accentR, $accentG, $accentB);
        $text = imagecolorallocate($image, $textR, $textG, $textB);

        imagefill($image, 0, 0, $bg);
        imagerectangle($image, 35, 35, $width - 35, $height - 35, $accent);
        imagerectangle($image, 45, 45, $width - 45, $height - 45, $accent);

        $title = $certificate->template?->title ?? 'Certificate';
        $name = $certificate->getParticipantName();
        $source = $this->resolveSourceTitle($certificate);
        $award = $this->resolveAwardTitle($certificate);
        $date = ($certificate->issued_at ?? $certificate->issue_date ?? now())->format('F j, Y');

        imagestring($image, 5, (int) ($width * 0.38), 140, strtoupper($title), $accent);
        imagestring($image, 4, (int) ($width * 0.3), 300, 'This certifies that', $text);
        imagestring($image, 5, (int) ($width * 0.32), 360, $name, $accent);
        imagestring($image, 4, (int) ($width * 0.18), 460, 'for ' . $award . ' in ' . $source, $text);
        imagestring($image, 3, (int) ($width * 0.36), 560, 'Issued on ' . $date, $text);
        imagestring($image, 3, (int) ($width * 0.30), 620, 'Certificate: ' . $certificate->certificate_code, $text);
        imagestring($image, 2, (int) ($width * 0.30), 700, 'Verify: ' . route('certificate.verify', $certificate->certificate_code), $text);

        $filename = sprintf('cert-%s.png', $certificate->certificate_code);
        $path = $this->resolveBaseDirectory($certificate) . "/{$filename}";

        $fullPath = Storage::disk('public')->path($path);
        @mkdir(dirname($fullPath), 0755, true);
        imagepng($image, $fullPath, 8);
        imagedestroy($image);

        return $path;
    }

    protected function generateShareImages(Certificate $certificate, string $basePngPath): array
    {
        if (!extension_loaded('gd')) {
            return [];
        }

        $sourceImagePath = Storage::disk('public')->path($basePngPath);
        if (!is_file($sourceImagePath)) {
            return [];
        }

        $sourceImage = @imagecreatefrompng($sourceImagePath);
        if (!$sourceImage) {
            return [];
        }

        $sizes = [
            'instagram_post' => [1080, 1080],
            'instagram_story' => [1080, 1920],
            'facebook_share' => [1200, 630],
            'linkedin_share' => [1200, 627],
        ];

        $paths = [];
        foreach ($sizes as $key => [$width, $height]) {
            $canvas = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($canvas, 255, 255, 255);
            $text = imagecolorallocate($canvas, 17, 24, 39);
            imagefill($canvas, 0, 0, $white);

            imagecopyresampled($canvas, $sourceImage, 60, 60, 0, 0, $width - 120, (int) ($height * 0.58), imagesx($sourceImage), imagesy($sourceImage));

            imagestring($canvas, 5, 60, (int) ($height * 0.66), $certificate->getParticipantName(), $text);
            imagestring($canvas, 4, 60, (int) ($height * 0.72), $this->resolveAwardTitle($certificate), $text);
            imagestring($canvas, 4, 60, (int) ($height * 0.78), $this->resolveSourceTitle($certificate), $text);
            imagestring($canvas, 3, 60, (int) ($height * 0.86), config('app.name', 'Photographer SB'), $text);
            imagestring($canvas, 3, 60, (int) ($height * 0.90), 'Proud to receive this certificate from Photographer SB', $text);

            $filename = sprintf('share-%s-%s.png', $certificate->certificate_code, $key);
            $path = $this->resolveBaseDirectory($certificate) . "/share/{$filename}";
            $fullPath = Storage::disk('public')->path($path);
            @mkdir(dirname($fullPath), 0755, true);
            imagepng($canvas, $fullPath, 8);
            imagedestroy($canvas);

            $paths[$key] = $path;
        }

        imagedestroy($sourceImage);

        return $paths;
    }

    protected function buildCertificateHTML(Certificate $certificate, $template): string
    {
        $sourceTitle = $certificate->event?->title
            ?? $certificate->competition?->title
            ?? $certificate->competition?->name
            ?? 'Achievement';

        $competitionTitle = $certificate->competition?->title ?? $certificate->competition?->name ?? '';
        $eventTitle = $certificate->event?->title ?? '';
        $awardTitle = $this->resolveAwardTitle($certificate);

        $issuedAt = $certificate->issued_at ?? $certificate->issue_date ?? now();
        $templateContent = $template?->template_content ?: 'This is to certify that <strong>{{name}}</strong> successfully participated in <strong>{{event_name}}</strong> on {{date}}. Certificate Code: {{certificate_code}}';

        $content = str_replace(
            ['{{name}}', '{{event_name}}', '{{competition_name}}', '{{award_title}}', '{{date}}', '{{certificate_code}}', '{{certificate_id}}', '{{platform_name}}', '{{event}}'],
            [
                e($certificate->getParticipantName()),
                e($eventTitle ?: $sourceTitle),
                e($competitionTitle ?: $sourceTitle),
                e($awardTitle),
                e($issuedAt->format('F j, Y')),
                e($certificate->certificate_code),
                e($certificate->certificate_code),
                e(config('app.name', 'Photographer SB')),
                e($sourceTitle),
            ],
            $templateContent
        );

        $title = e($template?->title ?? 'Certificate of Achievement');
        $bg = e($template?->background_color ?? '#ffffff');
        $accent = e($template?->accent_color ?? '#8e0e3f');
        $text = e($template?->text_color ?? '#111827');
        $font = e($template?->font_family ?? $template?->title_font ?? 'serif');
        $fontSize = (int) ($template?->font_size ?? 42);
        $orientation = $this->resolveOrientation($template?->width, $template?->height);
        $pageSize = $orientation === 'portrait' ? 'A4 portrait' : 'A4 landscape';

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: {$pageSize}; margin: 0; }
        body { margin: 0; font-family: Arial, sans-serif; background: {$bg}; color: {$text}; }
        .wrap { width: 100%; height: 100vh; box-sizing: border-box; padding: 32px; }
        .card { width: 100%; height: 100%; border: 4px solid {$accent}; padding: 42px; box-sizing: border-box; text-align: center; }
        .title { font-family: {$font}; color: {$accent}; font-size: {$fontSize}px; margin-top: 20px; margin-bottom: 12px; }
        .line { width: 180px; height: 3px; margin: 8px auto 28px; background: {$accent}; }
        .content { font-size: 18px; line-height: 1.7; }
        .footer { margin-top: 42px; font-size: 14px; color: #4b5563; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <div class="title">{$title}</div>
            <div class="line"></div>
            <div class="content">{$content}</div>
            <div class="footer">Certificate Code: {$certificate->certificate_code}</div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    protected function resolveBaseDirectory(Certificate $certificate): string
    {
        $category = match ($certificate->source_type) {
            'event', 'workshop' => 'events',
            'competition' => 'competitions',
            'award' => 'awards',
            default => $certificate->competition_id ? 'competitions' : ($certificate->event_id ? 'events' : 'awards'),
        };

        return "certificates/{$category}";
    }

    protected function resolveAwardTitle(Certificate $certificate): string
    {
        return $certificate->template?->title ?? 'Participation';
    }

    protected function resolveSourceTitle(Certificate $certificate): string
    {
        return $certificate->event?->title
            ?? $certificate->competition?->title
            ?? $certificate->competition?->name
            ?? 'Photographer SB';
    }

    protected function resolveOrientation($width, $height): string
    {
        $w = (float) ($width ?? 297);
        $h = (float) ($height ?? 210);

        return $h > $w ? 'portrait' : 'landscape';
    }

    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Generate certificate code
     */
    public function generateCode(): string
    {
        $year = now()->year;
        $sequence = Certificate::whereYear('created_at', $year)->count() + 1;

        return sprintf('SB-CERT-%d-%05d', $year, $sequence);
    }
}
