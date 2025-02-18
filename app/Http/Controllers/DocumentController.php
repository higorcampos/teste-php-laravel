<?php

namespace App\Http\Controllers;

use App\Jobs\ImportDocumentsJob;
use Illuminate\Http\Request;
use App\Services\UploadService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function upload(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:json|max:307200', // 300MB max
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('file');
            $path = $this->uploadService->uploadFile($file);

            $data = json_decode(file_get_contents($file), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON file.');
            }

            ImportDocumentsJob::dispatch($data);

            Log::info('File uploaded successfully', ['path' => $path]);

            return back()->with('success', 'Arquivo enviado com sucesso. Caminho: ' . $path);
        } catch (\Exception $e) {
            Log::error('File upload failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Falha no envio do arquivo: ' . $e->getMessage());
        }
    }
}