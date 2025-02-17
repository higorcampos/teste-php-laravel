<?php

namespace App\Http\Controllers;

use App\Jobs\ImportDocumentsJob;
use Illuminate\Http\Request;
use App\Services\UploadService;

class DocumentController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function upload(Request $request): \Illuminate\Http\RedirectResponse
    {
        $file = $request->file('file');
        $path = $this->uploadService->uploadFile($file);

        $data = json_decode(file_get_contents($file), true);

        ImportDocumentsJob::dispatch($data);

        return back()->with('success', 'Arquivo enviado com sucesso. Caminho: ' . $path);
    }
}