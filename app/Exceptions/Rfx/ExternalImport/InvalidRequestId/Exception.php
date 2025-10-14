<?php

namespace App\Exceptions\Rfx\ExternalImport\InvalidRequestId;

class Exception extends \Exception
{
    public function __construct(string $message = "유효하지 않은 request_id입니다.", int $code = 422)
    {
        parent::__construct($message, $code);
    }
}
