<?php

namespace App\Http\Controllers\Rfx\DocumentSections\GetList;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class Response implements Arrayable, JsonSerializable
{
    private bool $success;
    private string $message;
    private mixed $data;

    public function __construct(array $result)
    {
        $this->success = $result['success'];
        $this->message = $result['message'];
        $this->data = $result['data'];
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
