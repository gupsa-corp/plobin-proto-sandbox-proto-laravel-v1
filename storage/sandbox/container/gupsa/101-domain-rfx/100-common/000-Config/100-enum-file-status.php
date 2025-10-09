<?php

/**
 * RFX 파일 상태 Enum
 * RFX 도메인에서 사용하는 파일 상태 상수 정의
 */
class FileStatus
{
    public const PENDING = 'pending';
    public const PROCESSING = 'processing';
    public const COMPLETED = 'completed';
    public const FAILED = 'failed';
    public const CANCELLED = 'cancelled';

    /**
     * 모든 상태 목록 반환
     */
    public static function all(): array
    {
        return [
            self::PENDING,
            self::PROCESSING,
            self::COMPLETED,
            self::FAILED,
            self::CANCELLED,
        ];
    }

    /**
     * 상태 한글 표시명 매핑
     */
    public static function getDisplayName(string $status): string
    {
        return match ($status) {
            self::PENDING => '대기',
            self::PROCESSING => '처리 중',
            self::COMPLETED => '완료',
            self::FAILED => '실패',
            self::CANCELLED => '취소',
            default => $status,
        };
    }

    /**
     * 상태별 CSS 클래스 매핑
     */
    public static function getCssClass(string $status): string
    {
        return match ($status) {
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::PROCESSING => 'bg-blue-100 text-blue-800',
            self::COMPLETED => 'bg-green-100 text-green-800',
            self::FAILED => 'bg-red-100 text-red-800',
            self::CANCELLED => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}