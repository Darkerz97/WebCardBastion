<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class ArticleCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isGuest = ! $this->user();

        return [
            'guest_name' => [$isGuest ? 'required' : 'nullable', 'string', 'max:120'],
            'guest_email' => [$isGuest ? 'required' : 'nullable', 'email', 'max:255'],
            'body' => ['required', 'string', 'min:3', 'max:3000'],
        ];
    }
}
