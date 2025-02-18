<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportDocumentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $documentData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $documentData)
    {
        $this->documentData = $documentData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (empty($this->documentData['documentos'])) {
            throw new \Exception('Nenhum documento encontrado');
        }

        try {
            Log::info('Iniciando importação de documentos', ['document_data' => $this->documentData]);
            $documents = [];

            foreach ($this->documentData['documentos'] as $value) {
                $documents[] = [
                    'category_id' => $this->CreateOrUpdateCategory($value['categoria']),
                    'title' => $value['titulo'],
                    'contents' => $value['conteúdo'],
                ];
            }

            Document::insert($documents);
            Log::info('Documentos importados com sucesso', ['documents' => $documents]);
        } catch (\Exception $e) {
            Log::error('Erro ao importar documentos', ['error' => $e->getMessage()]);
        }
    }

    public function CreateOrUpdateCategory($category): int
    {
        try {
            Log::info('Tentando criar ou atualizar categoria', ['category' => $category]);
            $category = Category::firstOrCreate(['name' => $category]);
            Log::info('Categoria criada ou atualizada com sucesso', ['category_id' => $category->id]);
            return $category->id;
        } catch (\Exception $e) {
            Log::error('Erro ao criar ou atualizar categoria', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}