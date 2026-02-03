<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class PhotoMetadataService
{
    /**
     * Extract EXIF metadata from uploaded photo
     */
    public function extractMetadata(UploadedFile $file): array
    {
        $metadata = [];
        
        // Ensure EXIF extension is loaded
        if (!function_exists('exif_read_data')) {
            Log::warning('EXIF extension not available');
            return $metadata;
        }
        
        try {
            $exif = @exif_read_data($file->getRealPath(), 0, true);
            
            if ($exif === false) {
                return $metadata;
            }
            
            // Camera Make & Model
            if (isset($exif['IFD0']['Make'])) {
                $metadata['camera_make'] = trim($exif['IFD0']['Make']);
            }
            if (isset($exif['IFD0']['Model'])) {
                $metadata['camera_model'] = trim($exif['IFD0']['Model']);
            }
            
            // Date Taken
            if (isset($exif['EXIF']['DateTimeOriginal'])) {
                $metadata['date_taken'] = date('Y-m-d', strtotime($exif['EXIF']['DateTimeOriginal']));
            } elseif (isset($exif['IFD0']['DateTime'])) {
                $metadata['date_taken'] = date('Y-m-d', strtotime($exif['IFD0']['DateTime']));
            }
            
            // Camera Settings
            $settings = [];
            
            // ISO
            if (isset($exif['EXIF']['ISOSpeedRatings'])) {
                $settings['iso'] = $exif['EXIF']['ISOSpeedRatings'];
            }
            
            // Aperture (f-stop)
            if (isset($exif['EXIF']['FNumber'])) {
                $fNumber = $this->parseRational($exif['EXIF']['FNumber']);
                $settings['aperture'] = 'f/' . number_format($fNumber, 1);
            } elseif (isset($exif['EXIF']['ApertureValue'])) {
                $aperture = $this->parseRational($exif['EXIF']['ApertureValue']);
                $fNumber = pow(2, $aperture / 2);
                $settings['aperture'] = 'f/' . number_format($fNumber, 1);
            }
            
            // Shutter Speed
            if (isset($exif['EXIF']['ExposureTime'])) {
                $exposureTime = $exif['EXIF']['ExposureTime'];
                if (strpos($exposureTime, '/') !== false) {
                    $parts = explode('/', $exposureTime);
                    if ($parts[0] == '1') {
                        $settings['shutter_speed'] = $exposureTime . 's';
                    } else {
                        $decimal = $parts[0] / $parts[1];
                        $settings['shutter_speed'] = number_format($decimal, 2) . 's';
                    }
                } else {
                    $settings['shutter_speed'] = $exposureTime . 's';
                }
            }
            
            // Focal Length
            if (isset($exif['EXIF']['FocalLength'])) {
                $focalLength = $this->parseRational($exif['EXIF']['FocalLength']);
                $settings['focal_length'] = round($focalLength) . 'mm';
            }
            
            // Flash
            if (isset($exif['EXIF']['Flash'])) {
                $flashValue = $exif['EXIF']['Flash'];
                $settings['flash'] = ($flashValue & 1) ? 'Yes' : 'No';
            }
            
            // Build camera settings string
            if (!empty($settings)) {
                $settingsParts = array_filter([
                    $settings['aperture'] ?? null,
                    $settings['shutter_speed'] ?? null,
                    isset($settings['iso']) ? 'ISO ' . $settings['iso'] : null,
                    $settings['focal_length'] ?? null,
                ]);
                
                if (!empty($settingsParts)) {
                    $metadata['camera_settings'] = implode(', ', $settingsParts);
                }
            }
            
            // GPS Location
            if (isset($exif['GPS']) && !empty($exif['GPS'])) {
                $location = $this->extractGPSLocation($exif['GPS']);
                if ($location) {
                    $metadata['location'] = $location['location_string'];
                    $metadata['latitude'] = $location['latitude'];
                    $metadata['longitude'] = $location['longitude'];
                }
            }
            
        } catch (\Exception $e) {
            Log::warning('EXIF extraction failed: ' . $e->getMessage());
        }
        
        return $metadata;
    }
    
    /**
     * Parse rational EXIF value (e.g., "50/1" -> 50)
     */
    private function parseRational(string $value): float
    {
        if (strpos($value, '/') !== false) {
            $parts = explode('/', $value);
            if (count($parts) === 2 && $parts[1] != 0) {
                return $parts[0] / $parts[1];
            }
        }
        return (float) $value;
    }
    
    /**
     * Extract GPS coordinates from EXIF data
     */
    private function extractGPSLocation(array $gps): ?array
    {
        if (!isset($gps['GPSLatitude']) || !isset($gps['GPSLongitude'])) {
            return null;
        }
        
        try {
            $lat = $this->getGPSCoordinate($gps['GPSLatitude'], $gps['GPSLatitudeRef'] ?? 'N');
            $lon = $this->getGPSCoordinate($gps['GPSLongitude'], $gps['GPSLongitudeRef'] ?? 'E');
            
            return [
                'latitude' => round($lat, 6),
                'longitude' => round($lon, 6),
                'location_string' => round($lat, 6) . ', ' . round($lon, 6)
            ];
        } catch (\Exception $e) {
            Log::warning('GPS extraction failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Convert GPS coordinate to decimal degrees
     */
    private function getGPSCoordinate(array $coordinate, string $ref): float
    {
        $degrees = $this->parseRational($coordinate[0]);
        $minutes = $this->parseRational($coordinate[1]);
        $seconds = $this->parseRational($coordinate[2]);
        
        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);
        
        if ($ref === 'S' || $ref === 'W') {
            $decimal *= -1;
        }
        
        return $decimal;
    }
}
