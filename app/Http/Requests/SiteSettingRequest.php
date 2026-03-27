<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'home_kicker' => ['nullable', 'string', 'max:255'],
            'home_headline' => ['required', 'string', 'max:255'],
            'home_description' => ['required', 'string', 'max:1500'],
            'catalog_heading' => ['required', 'string', 'max:255'],
            'catalog_description' => ['required', 'string', 'max:1500'],
            'benefit_one_title' => ['required', 'string', 'max:255'],
            'benefit_one_description' => ['required', 'string', 'max:1000'],
            'benefit_two_title' => ['required', 'string', 'max:255'],
            'benefit_two_description' => ['required', 'string', 'max:1000'],
            'benefit_three_title' => ['required', 'string', 'max:255'],
            'benefit_three_description' => ['required', 'string', 'max:1000'],
            'announcement_text' => ['nullable', 'string', 'max:500'],
        ];
    }
}
