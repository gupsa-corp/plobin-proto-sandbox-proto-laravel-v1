<?php

namespace App\Http\Controllers\FileUpload\Create;

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
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:pdf,doc,docx,txt,jpg,jpeg,png'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => '파일을 선택해주세요.',
            'file.file' => '유효한 파일을 선택해주세요.',
            'file.max' => '파일 크기는 10MB를 초과할 수 없습니다.',
            'file.mimes' => '지원되지 않는 파일 형식입니다. (PDF, DOC, DOCX, TXT, JPG, JPEG, PNG만 허용)'
        ];
    }
}