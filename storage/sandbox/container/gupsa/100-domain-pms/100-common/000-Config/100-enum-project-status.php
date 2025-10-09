<?php

/**
 * 프로젝트 상태 Enum
 * PMS 도메인에서 사용하는 프로젝트 상태 상수 정의
 */
class ProjectStatus
{
    public const PENDING = 'pending';
    public const IN_PROGRESS = 'in_progress';
    public const ON_HOLD = 'on_hold';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    /**
     * 모든 상태 목록 반환
     */
    public static function all(): array
    {
        return [
            self::PENDING,
            self::IN_PROGRESS,
            self::ON_HOLD,
            self::COMPLETED,
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
            self::IN_PROGRESS => '진행 중',
            self::ON_HOLD => '보류',
            self::COMPLETED => '완료',
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
            self::PENDING => 'bg-gray-100 text-gray-800',
            self::IN_PROGRESS => 'bg-blue-100 text-blue-800',
            self::ON_HOLD => 'bg-yellow-100 text-yellow-800',
            self::COMPLETED => 'bg-green-100 text-green-800',
            self::CANCELLED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}