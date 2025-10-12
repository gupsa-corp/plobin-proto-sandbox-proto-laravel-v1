<?php

namespace App\Http\Controllers\Rfx\DocumentBlocks\GetList;

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
            'block_type' => 'nullable|string|in:title,paragraph,table,list,other',
            'confidence_min' => 'nullable|numeric|min:0|max:1',
            'limit' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'page.integer' => '페이지 번호는 정수여야 합니다',
            'page.min' => '페이지 번호는 1 이상이어야 합니다',
            'block_type.in' => '블록 타입은 title, paragraph, table, list, other 중 하나여야 합니다',
            'confidence_min.numeric' => '최소 신뢰도는 숫자여야 합니다',
            'confidence_min.min' => '최소 신뢰도는 0 이상이어야 합니다',
            'confidence_min.max' => '최소 신뢰도는 1 이하여야 합니다',
            'limit.integer' => '항목 수는 정수여야 합니다',
            'limit.min' => '항목 수는 1 이상이어야 합니다',
            'limit.max' => '항목 수는 100 이하여야 합니다',
        ];
    }
}
