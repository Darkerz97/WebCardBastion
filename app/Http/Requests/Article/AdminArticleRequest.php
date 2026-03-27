<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $articleId = $this->route('article')?->id ?? $this->route('article');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('articles', 'slug')->ignore($articleId)],
            'excerpt' => ['nullable', 'string', 'max:600'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'remove_cover_image' => ['nullable', 'boolean'],
            'is_published' => ['required', 'boolean'],
            'allow_comments' => ['required', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
