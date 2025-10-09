<?php

namespace App\Http\Controllers\Rfx\FileUpload;

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
            'file' => 'required|file|max:51200|mimes:pdf,doc,docx,txt,xlsx,xls,ppt,pptx,jpg,jpeg,png,gif',
            'description' => 'nullable|string|max:1000',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => '파일을 선택해주세요.',
            'file.file' => '유효한 파일을 업로드해주세요.',
            'file.max' => '파일 크기는 50MB를 초과할 수 없습니다.',
            'file.mimes' => '지원되지 않는 파일 형식입니다.',
            'description.max' => '설명은 1000자를 초과할 수 없습니다.',
            'tags.array' => '태그는 배열 형식이어야 합니다.',
            'tags.*.string' => '각 태그는 문자열이어야 합니다.',
            'tags.*.max' => '각 태그는 50자를 초과할 수 없습니다.'
        ];
    }
}