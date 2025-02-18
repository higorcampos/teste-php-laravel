<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class UploadService
{
    /**
     * Upload a file.
     *
     * @param UploadedFile $file
     * @return string
     * @throws \Exception
     */
    public function uploadFile(UploadedFile $file): string
    {
        try {
            Log::info('Starting file upload', ['file_name' => $file->getClientOriginalName()]);

            if ($file->getSize() > 300 * 1024 * 1024) {
                throw new \Exception('File size must not exceed 300MB.');
            }

            if ($file->getClientMimeType() !== 'application/json') {
                throw new \Exception('Only JSON files are allowed.');
            }

            $path = $file->store('uploads', 'public');

            Log::info('File uploaded successfully', ['file_path' => $path]);

            return $path;
        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage(), ['file_name' => $file->getClientOriginalName()]);
            throw $e;
        }
    }
}