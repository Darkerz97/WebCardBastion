<?php

namespace App\Http\Requests\Tournament;

use App\Models\Tournament;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tournamentId = $this->route('tournament')?->id ?? $this->route('tournament');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('tournaments', 'slug')->ignore($tournamentId)],
            'description' => ['nullable', 'string'],
            'format' => ['required', 'string', 'max:100'],
            'status' => ['required', Rule::in([
                Tournament::STATUS_DRAFT,
                Tournament::STATUS_REGISTRATION_OPEN,
                Tournament::STATUS_IN_PROGRESS,
                Tournament::STATUS_COMPLETED,
            ])],
            'entry_fee' => ['nullable', 'numeric', 'min:0'],
            'max_players' => ['nullable', 'integer', 'min:2'],
            'rounds_count' => ['required', 'integer', 'min:1', 'max:12'],
            'starts_at' => ['nullable', 'date'],
            'registration_closes_at' => ['nullable', 'date'],
            'published' => ['required', 'boolean'],
        ];
    }
}
