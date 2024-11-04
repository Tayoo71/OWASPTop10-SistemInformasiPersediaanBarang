<?php

namespace App\Http\Requests\Pengaturan\KelompokUser;

use Illuminate\Foundation\Http\FormRequest;

class ViewKelompokUSerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sort_by' => 'nullable|in:id,name',
            'direction' => 'nullable|in:asc,desc',
            'search' => 'nullable|string|max:255',
            'edit' => 'nullable|exists:roles,id',
            'delete' => 'nullable|exists:roles,id',
        ];
    }
}
