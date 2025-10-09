<?php

/**
 * PMS 도메인 API 엔드포인트 Enum
 * PMS 도메인에서 사용하는 API 경로 상수 정의
 */
class PmsApiEndpoints
{
    // 컨테이너명과 도메인명
    public const CONTAINER = 'gupsa';
    public const DOMAIN = 'pms';

    // 기본 API 경로
    public const BASE_PATH = '/api/sandbox';

    /**
     * 프로젝트 관련 API 엔드포인트
     */
    public static function projectsList(): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/projects';
    }

    public static function projectsCreate(): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/projects';
    }

    public static function projectsShow(int $id): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/projects/' . $id;
    }

    public static function projectsUpdate(int $id): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/projects/' . $id;
    }

    public static function projectsDelete(int $id): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/projects/' . $id;
    }

    /**
     * 통계 관련 API 엔드포인트
     */
    public static function statistics(): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/statistics';
    }

    /**
     * 전체 기본 경로 반환
     */
    public static function basePath(): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN;
    }
}