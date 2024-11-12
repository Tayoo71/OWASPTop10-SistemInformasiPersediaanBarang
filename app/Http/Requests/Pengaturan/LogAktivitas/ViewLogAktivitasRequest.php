<?php

namespace App\Http\Requests\Pengaturan\LogAktivitas;

use Illuminate\Foundation\Http\FormRequest;

class ViewLogAktivitasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('log_aktivitas.akses');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'direction' => 'nullable|in:asc,desc',
            'start' => 'nullable|date_format:d/m/Y|before_or_equal:end',
            'end' => 'nullable|date_format:d/m/Y|after_or_equal:start',
            'search' => 'nullable|string|max:255',
        ];
    }
}
