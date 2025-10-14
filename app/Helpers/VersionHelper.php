<?php

namespace App\Helpers;

use Carbon\Carbon;

class VersionHelper
{
    /**
     * YmdHisu 타임스탬프를 'Y-m-d H:i:s' 형식으로 변환
     *
     * @param string $timestamp YmdHisu 형식 (예: 20250115143025123456)
     * @return string 'Y-m-d H:i:s' 형식 (예: 2025-01-15 14:30:25)
     */
    public static function formatVersion(string $timestamp): string
    {
        try {
            // YmdHisu 형식: 20250115143025123456 (년월일시분초마이크로초)
            // 앞 14자리만 사용 (년월일시분초)
            $dateTimeStr = substr($timestamp, 0, 14);

            return Carbon::createFromFormat('YmdHis', $dateTimeStr)
                ->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            // 파싱 실패 시 원본 반환
            return $timestamp;
        }
    }

    /**
     * 현재 시각을 YmdHisu 형식으로 반환
     *
     * @return string YmdHisu 형식 (예: 20250115143025123456)
     */
    public static function generateTimestamp(): string
    {
        return now()->format('YmdHisu');
    }
}
