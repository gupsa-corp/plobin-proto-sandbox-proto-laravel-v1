<?php

/**
 * RFX 도메인 API 엔드포인트 Enum
 * RFX 도메인에서 사용하는 API 경로 상수 정의
 */
class RfxApiEndpoints
{
    // 컨테이너명과 도메인명
    public const CONTAINER = 'gupsa';
    public const DOMAIN = 'rfx';

    // 기본 API 경로
    public const BASE_PATH = '/api/sandbox';

    /**
     * 파일 업로드 관련 API 엔드포인트
     */
    public static function filesList(): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/files';
    }

    public static function filesUpload(): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/files/upload';
    }

    public static function filesShow(int $id): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/files/' . $id;
    }

    public static function filesDelete(int $id): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/files/' . $id;
    }

    /**
     * 분석 요청 관련 API 엔드포인트
     */
    public static function analysisRequestsList(): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/analysis-requests';
    }

    public static function analysisRequestsCreate(): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/analysis-requests';
    }

    public static function analysisRequestsShow(int $id): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/analysis-requests/' . $id;
    }

    public static function analysisRequestsUpdate(int $id): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/analysis-requests/' . $id;
    }

    /**
     * 문서 분석 관련 API 엔드포인트
     */
    public static function documentAnalysisList(): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/document-analysis';
    }

    public static function documentAnalysisShow(int $id): string
    {
        return self::BASE_PATH . '/' . self::CONTAINER . '/' . self::DOMAIN . '/document-analysis/' . $id;
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