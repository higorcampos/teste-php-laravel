<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'contents',
    ];

    protected $table = 'documents';

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($document) {
            $document->validateContentLength();
            $document->validateTitleForCategory();
        });
    }

    protected function validateContentLength()
    {
        if (strlen($this->contents) > 65535) {
            throw ValidationException::withMessages(['contents' => 'Content must not exceed 65535 characters.']);
        }
    }

    protected function validateTitleForCategory()
    {
        if ($this->category->name === 'Remessa' && !str_contains($this->title, 'semestre')) {
            throw ValidationException::withMessages(['title' => 'Title must contain "semestre" for category "Remessa".']);
        }

        if ($this->category->name === 'Remessa Parcial' && !preg_match('/Janeiro|Fevereiro|Março|Abril|Maio|Junho|Julho|Agosto|Setembro|Outubro|Novembro|Dezembro/', $this->title)) {
            throw ValidationException::withMessages(['title' => 'Title must contain a month name for category "Remessa Parcial".']);
        }
    }
}