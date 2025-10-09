<?php

namespace App\Services\FileUpload\GetFileTypeDisplay;

class Service
{
    public function execute(string $mimeType): string
    {
        if (strpos($mimeType, 'image/') === 0) return '이미지';
        if (strpos($mimeType, 'video/') === 0) return '비디오';
        if (strpos($mimeType, 'audio/') === 0) return '오디오';
        if ($mimeType === 'application/pdf') return 'PDF';
        if (strpos($mimeType, 'application/msword') === 0 || 
            strpos($mimeType, 'application/vnd.openxmlformats-officedocument.wordprocessingml') === 0) {
            return 'Word 문서';
        }
        if (strpos($mimeType, 'application/vnd.ms-excel') === 0 || 
            strpos($mimeType, 'application/vnd.openxmlformats-officedocument.spreadsheetml') === 0) {
            return 'Excel 문서';
        }
        if (strpos($mimeType, 'text/') === 0) return '텍스트';
        
        return '기타';
    }
}