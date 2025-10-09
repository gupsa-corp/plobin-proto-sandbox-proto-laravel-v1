<?php

/**
 * 프로젝트 우선순위 Enum
 * PMS 도메인에서 사용하는 프로젝트 우선순위 상수 정의
 */
class ProjectPriority
{
    public const LOW = 'low';
    public const MEDIUM = 'medium';
    public const HIGH = 'high';
    public const CRITICAL = 'critical';

    /**
     * 모든 우선순위 목록 반환
     */
    public static function all(): array
    {
        return [
            self::LOW,
            self::MEDIUM,
            self::HIGH,
            self::CRITICAL,
        ];
    }

    /**
     * 우선순위 한글 표시명 매핑
     */
    public static function getDisplayName(string $priority): string
    {
        return match ($priority) {
            self::LOW => '낮음',
            self::MEDIUM => '보통',
            self::HIGH => '높음',
            self::CRITICAL => '긴급',
            default => $priority,
        };
    }

    /**
     * 우선순위별 CSS 클래스 매핑
     */
    public static function getCssClass(string $priority): string
    {
        return match ($priority) {
            self::LOW => 'bg-green-100 text-green-800',
            self::MEDIUM => 'bg-yellow-100 text-yellow-800',
            self::HIGH => 'bg-orange-100 text-orange-800',
            self::CRITICAL => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * 우선순위별 정렬 순서 (높을수록 우선순위가 높음)
     */
    public static function getSortOrder(string $priority): int
    {
        return match ($priority) {
            self::LOW => 1,
            self::MEDIUM => 2,
            self::HIGH => 3,
            self::CRITICAL => 4,
            default => 0,
        };
    }
}