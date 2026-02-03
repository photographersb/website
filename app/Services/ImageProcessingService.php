<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageProcessingService
{
    protected $processor = null;
    protected $processorName = 'none';

    public function __construct()
    {
        $this->detectImageProcessor();
    }

    /**
     * Detect available image processing library
     */
    protected function detectImageProcessor(): void
    {
        if (extension_loaded('imagick')) {
            $this->processor = 'imagick';
            $this->processorName = 'ImageMagick';
        } elseif (extension_loaded('gd')) {
            $this->processor = 'gd';
            $this->processorName = 'GD Library';
        }
    }

    /**
     * Check if image processing is available
     */
    public function isAvailable(): bool
    {
        return $this->processor !== null;
    }

    /**
     * Get the name of the available processor
     */
    public function getProcessorName(): string
    {
        return $this->processorName;
    }

    /**
     * Process and save an uploaded image with error handling
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param array $options
     * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
     */
    public function processAndSave($file, string $directory, array $options = []): array
    {
        if (!$this->isAvailable()) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'Image processing is not available on this server. Please contact support or upload smaller images.'
            ];
        }

        try {
            // Default options
            $maxWidth = $options['max_width'] ?? 2048;
            $maxHeight = $options['max_height'] ?? 2048;
            $quality = $options['quality'] ?? 85;
            $format = $options['format'] ?? null;

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . ($format ?? $file->getClientOriginalExtension());
            $storagePath = $directory . '/' . $filename;

            // Process image based on available library
            if ($this->processor === 'imagick' && class_exists('Imagick')) {
                $processed = $this->processWithImagick($file->getRealPath(), $maxWidth, $maxHeight, $quality, $format);
            } elseif ($this->processor === 'gd') {
                $processed = $this->processWithGD($file->getRealPath(), $maxWidth, $maxHeight, $quality, $format);
            } else {
                // Fallback: just move the file without processing
                return $this->saveFallback($file, $directory);
            }

            // Save processed image
            Storage::disk('public')->put($storagePath, $processed);

            return [
                'success' => true,
                'path' => $storagePath,
                'url' => Storage::url($storagePath),
                'error' => null
            ];

        } catch (Exception $e) {
            Log::error('Image processing failed: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'directory' => $directory,
                'processor' => $this->processorName
            ]);

            // Try fallback: save original without processing
            try {
                return $this->saveFallback($file, $directory);
            } catch (Exception $fallbackError) {
                return [
                    'success' => false,
                    'path' => null,
                    'error' => 'Failed to process image. Please try uploading a smaller file or different format.'
                ];
            }
        }
    }

    /**
     * Process image using ImageMagick
     */
    protected function processWithImagick(string $filePath, int $maxWidth, int $maxHeight, int $quality, ?string $format): string
    {
        $imagick = new \Imagick($filePath);
        
        // Get original dimensions
        $width = $imagick->getImageWidth();
        $height = $imagick->getImageHeight();

        // Calculate new dimensions
        if ($width > $maxWidth || $height > $maxHeight) {
            $imagick->thumbnailImage($maxWidth, $maxHeight, true);
        }

        // Set quality and format
        $imagick->setImageCompressionQuality($quality);
        if ($format) {
            $imagick->setImageFormat($format);
        }

        // Strip metadata to reduce file size
        $imagick->stripImage();

        $processed = $imagick->getImageBlob();
        $imagick->clear();
        $imagick->destroy();

        return $processed;
    }

    /**
     * Process image using GD Library
     */
    protected function processWithGD(string $filePath, int $maxWidth, int $maxHeight, int $quality, ?string $format): string
    {
        // Detect image type
        $imageInfo = getimagesize($filePath);
        $mimeType = $imageInfo['mime'];

        // Create image resource from file
        switch ($mimeType) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($filePath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($filePath);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($filePath);
                break;
            case 'image/webp':
                $source = imagecreatefromwebp($filePath);
                break;
            default:
                throw new Exception('Unsupported image type: ' . $mimeType);
        }

        // Get original dimensions
        $width = imagesx($source);
        $height = imagesy($source);

        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);

        // Create new image
        $dest = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
            $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);
            imagefilledrectangle($dest, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize image
        imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Output to string
        ob_start();
        $outputFormat = $format ?? pathinfo($filePath, PATHINFO_EXTENSION);
        
        switch ($outputFormat) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($dest, null, $quality);
                break;
            case 'png':
                imagepng($dest, null, (int)(9 - ($quality / 100 * 9)));
                break;
            case 'webp':
                imagewebp($dest, null, $quality);
                break;
            default:
                imagejpeg($dest, null, $quality);
        }
        
        $processed = ob_get_clean();

        // Free memory
        imagedestroy($source);
        imagedestroy($dest);

        return $processed;
    }

    /**
     * Fallback: save file without processing
     */
    protected function saveFallback($file, string $directory): array
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, 'public');

        return [
            'success' => true,
            'path' => $path,
            'url' => Storage::url($path),
            'error' => null,
            'warning' => 'Image was saved without processing due to server limitations.'
        ];
    }

    /**
     * Validate image file before processing
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param array $rules
     * @return array ['valid' => bool, 'error' => string|null]
     */
    public function validateImage($file, array $rules = []): array
    {
        $maxSize = $rules['max_size'] ?? 10240; // 10MB default
        $minWidth = $rules['min_width'] ?? 800;
        $minHeight = $rules['min_height'] ?? 600;
        $allowedMimes = $rules['allowed_mimes'] ?? ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

        // Check file size
        if ($file->getSize() > ($maxSize * 1024)) {
            return [
                'valid' => false,
                'error' => "Image size must not exceed " . ($maxSize / 1024) . "MB."
            ];
        }

        // Check mime type
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return [
                'valid' => false,
                'error' => 'Image must be in JPEG, PNG, or WebP format.'
            ];
        }

        // Check dimensions
        try {
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo) {
                [$width, $height] = $imageInfo;
                
                if ($width < $minWidth || $height < $minHeight) {
                    return [
                        'valid' => false,
                        'error' => "Image dimensions must be at least {$minWidth}x{$minHeight} pixels."
                    ];
                }
            }
        } catch (Exception $e) {
            return [
                'valid' => false,
                'error' => 'Unable to read image file. Please ensure it is a valid image.'
            ];
        }

        return [
            'valid' => true,
            'error' => null
        ];
    }
}
