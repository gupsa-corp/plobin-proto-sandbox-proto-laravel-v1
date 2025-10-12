<?php

namespace App\Http\Controllers\Rfx\DocumentSections\GetList;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'page.integer' => '페이지 번호는 정수여야 합니다',
            'page.min' => '페이지 번호는 1 이상이어야 합니다',
        ];
    }
}
