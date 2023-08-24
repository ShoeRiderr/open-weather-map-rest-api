<?php

namespace App\Http\Requests\OpenWeatherMap;

use Illuminate\Foundation\Http\FormRequest;

class GetCurrendWeatherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->id() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'lat' => ['required'],
            'lon' => ['required'],
            'exclude' => ['nullable', 'string'],
            'units' => ['nullable', 'string'],
            'lang' => ['nullable', 'string'],
        ];
    }
}
