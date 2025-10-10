<?php

namespace App\Http\Controllers\Rfx\Upload;

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
                'mimes:pdf,jpg,jpeg,png,tiff,bmp',
                'max:10240' // 10MB
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => '파일을 선택해주세요.',
            'file.file' => '유효한 파일을 업로드해주세요.',
            'file.mimes' => '지원되는 파일 형식은 PDF, JPG, JPEG, PNG, TIFF, BMP입니다.',
            'file.max' => '파일 크기는 10MB를 초과할 수 없습니다.'
        ];
    }
}