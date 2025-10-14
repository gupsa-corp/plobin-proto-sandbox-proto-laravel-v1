<?php

namespace App\Http\Controllers\Rfx\ExternalImport\ImportData;

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
            'request_id' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'request_id.required' => 'request_id는 필수입니다.',
            'request_id.string' => 'request_id는 문자열이어야 합니다.',
            'request_id.max' => 'request_id는 100자 이하여야 합니다.',
        ];
    }
}
