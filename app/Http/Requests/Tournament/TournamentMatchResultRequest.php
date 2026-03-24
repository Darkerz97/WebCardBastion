<?php

namespace App\Http\Requests\Tournament;

use Illuminate\Foundation\Http\FormRequest;

class TournamentMatchResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'player_one_score' => ['required', 'integer', 'min:0', 'max:9'],
            'player_two_score' => ['required', 'integer', 'min:0', 'max:9'],
        ];
    }
}
