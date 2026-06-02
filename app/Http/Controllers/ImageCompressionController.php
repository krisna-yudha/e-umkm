<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tinify\Tinify;

class ImageCompressionController extends Controller
{
    /**
     * Compress image using TinyIMG API
     */
    public function compressImage(Request $request)
    {
        try {
            // Validate the image - max 10MB to allow pre-compression
            $validated = $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240'
            ]);

            $image = $request->file('image');
            
            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada file gambar yang dikirim'
                ], 400);
            }

            // Get API key from environment
            $apiKey = env('TINIFY_API_KEY');
            $imageData = file_get_contents($image->getRealPath());
            $originalSize = strlen($imageData);

            // If file > 2MB, do pre-compression via fallback first
            if ($originalSize > 2097152) { // 2MB in bytes
                Log::info('Large image detected, pre-compressing before TinyIMG', [
                    'original_size' => $originalSize
                ]);
                
                $preCompressed = $this->preCompressImage($image);
                if ($preCompressed['success']) {
                    $imageData = $preCompressed['data'];
                    Log::info('Pre-compression done', [
                        'size_after_pre' => strlen($imageData),
                        'reduction' => $originalSize - strlen($imageData)
                    ]);
                }
            }

            // Try Tinify if API key configured
            if ($apiKey) {
                try {
                    \Tinify\Tinify::setKey($apiKey);
                    $source = \Tinify\fromBuffer($imageData);
                    $output = $source->toBuffer();

                    $base64Image = 'data:image/jpeg;base64,' . base64_encode($output);

                    Log::info('Image compressed successfully with TinyIMG', [
                        'original_size' => $originalSize,
                        'compressed_size' => strlen($output),
                        'compression_ratio' => round((1 - strlen($output) / $originalSize) * 100, 2)
                    ]);

                    return response()->json([
                        'success' => true,
                        'compressedImage' => $base64Image,
                        'originalSize' => $originalSize,
                        'compressedSize' => strlen($output),
                        'message' => 'Gambar berhasil dikompres dengan TinyIMG'
                    ]);
                } catch (\Tinify\Exception $e) {
                    Log::warning('TinyIMG compression failed, using fallback', [
                        'error' => $e->getMessage()
                    ]);
                    // Fall through to fallback compression
                }
            }

            // Fallback compression
            Log::info('Using fallback compression method');
            return $this->fallbackCompress($image, $originalSize);

        } catch (\Exception $e) {
            Log::error('Image compression error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengompres gambar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pre-compress large images (> 2MB) before sending to TinyIMG
     */
    private function preCompressImage($imageFile)
    {
        try {
            $imagePath = $imageFile->getRealPath();
            $mimeType = $imageFile->getMimeType();

            // Create image from file
            $image = $this->createImageFromFile($imagePath, $mimeType);
            if (!$image) {
                return ['success' => false];
            }

            // Get original dimensions
            $width = imagesx($image);
            $height = imagesy($image);

            // Calculate new dimensions (max 1200px on longest side)
            $maxSize = 1200;
            $scale = min($maxSize / $width, $maxSize / $height, 1);
            
            if ($scale < 1) {
                $newWidth = (int)($width * $scale);
                $newHeight = (int)($height * $scale);

                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                
                imagedestroy($image);
                $image = $resized;
            }

            // Compress as JPEG with quality 82
            $tempPath = tempnam(sys_get_temp_dir(), 'precomp_');
            imagejpeg($image, $tempPath, 82);
            
            $compressedData = file_get_contents($tempPath);
            unlink($tempPath);
            imagedestroy($image);

            return [
                'success' => true,
                'data' => $compressedData
            ];

        } catch (\Exception $e) {
            Log::warning('Pre-compression failed', ['error' => $e->getMessage()]);
            return ['success' => false];
        }
    }

    /**
     * Create image resource from file
     */
    private function createImageFromFile($filePath, $mimeType)
    {
        try {
            switch ($mimeType) {
                case 'image/jpeg':
                    return \imagecreatefromjpeg($filePath);
                case 'image/png':
                    $img = \imagecreatefrompng($filePath);
                    \imagealphablending($img, false);
                    \imagesavealpha($img, true);
                    return $img;
                case 'image/gif':
                    return \imagecreatefromgif($filePath);
                case 'image/webp':
                    return \imagecreatefromwebp($filePath);
                default:
                    return null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to create image from file', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Fallback compression if API key not available
     * Uses simple JPEG conversion for basic compression
     */
    private function fallbackCompress($imageFile, $originalSize = null)
    {
        try {
            $imagePath = $imageFile->getRealPath();
            $mimeType = $imageFile->getMimeType();

            // Create image from file
            $image = $this->createImageFromFile($imagePath, $mimeType);
            if (!$image) {
                throw new \Exception('Failed to create image from file');
            }

            // Create temporary file for compressed image
            $tempPath = tempnam(sys_get_temp_dir(), 'img_');
            
            // Compress as JPEG with quality 85
            imagejpeg($image, $tempPath, 85);
            
            // Read compressed image
            $compressedData = file_get_contents($tempPath);
            unlink($tempPath);
            imagedestroy($image);

            if ($originalSize === null) {
                $originalSize = strlen(file_get_contents($imagePath));
            }
            
            $base64Image = 'data:image/jpeg;base64,' . base64_encode($compressedData);

            Log::info('Image compressed with fallback method', [
                'original_size' => $originalSize,
                'compressed_size' => strlen($compressedData),
                'compression_ratio' => round((1 - strlen($compressedData) / $originalSize) * 100, 2)
            ]);

            return response()->json([
                'success' => true,
                'compressedImage' => $base64Image,
                'originalSize' => $originalSize,
                'compressedSize' => strlen($compressedData),
                'message' => 'Gambar berhasil dikompres (metode alternatif)'
            ]);

        } catch (\Exception $e) {
            Log::error('Fallback compression error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengompres gambar: ' . $e->getMessage()
            ], 500);
        }
    }
}
