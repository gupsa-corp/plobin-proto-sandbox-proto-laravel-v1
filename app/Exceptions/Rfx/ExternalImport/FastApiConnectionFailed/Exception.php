<?php

namespace App\Exceptions\Rfx\ExternalImport\FastApiConnectionFailed;

class Exception extends \Exception
{
    public function __construct(string $message = "FastAPI 연결에 실패했습니다.", int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
