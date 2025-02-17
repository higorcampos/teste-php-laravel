<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

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
        if ($file->getSize() > 300 * 1024 * 1024) {
            throw new \Exception('File size must not exceed 300MB.');
        }

        if ($file->getClientMimeType() !== 'application/json') {
            throw new \Exception('Only JSON files are allowed.');
        }

        $path = $file->store('uploads', 'public');

        return $path;
    }
}