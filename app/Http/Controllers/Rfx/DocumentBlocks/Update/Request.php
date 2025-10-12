<?php

namespace App\Http\Controllers\Rfx\DocumentBlocks\Update;

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
            'text' => 'nullable|string',
            'block_type' => 'nullable|string|in:title,paragraph,table,list,other',
            'confidence' => 'nullable|numeric|min:0|max:1',
            'page' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'text.string' => '텍스트는 문자열이어야 합니다',
            'block_type.in' => '블록 타입은 title, paragraph, table, list, other 중 하나여야 합니다',
            'confidence.numeric' => '신뢰도는 숫자여야 합니다',
            'confidence.min' => '신뢰도는 0 이상이어야 합니다',
            'confidence.max' => '신뢰도는 1 이하여야 합니다',
            'page.integer' => '페이지 번호는 정수여야 합니다',
            'page.min' => '페이지 번호는 1 이상이어야 합니다',
        ];
    }
}
