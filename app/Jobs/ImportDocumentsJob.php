<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportDocumentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $documentData;
    /**
     * Create a new job instance.
     */
    public function __construct($documentData)
    {
        $this->documentData = $documentData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->documentData['documentos']) {
            throw new \Exception('Nenhum documento encontrado');
        }

        $documents = [];

        foreach ($this->documentData['documentos'] as $value) {
            $documents[] = [
                'category_id' => $this->CreateOrUpdateCategory($value['categoria']),
                'title' => $value['titulo'],
                'contents' => $value['conteúdo'],
            ];
        }

        Document::insert($documents);
    }

    public function CreateOrUpdateCategory($category): int
    {
        $category = Category::firstOrCreate(['name' => $category]);
        return $category->id;
    }
}