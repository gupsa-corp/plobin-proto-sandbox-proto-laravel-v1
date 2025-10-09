<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class FileUploadManager extends Component
{
    use WithFileUploads;

    public $files = [];
    public $uploadedFiles = [];
    public $currentStep = 'upload'; // upload, progress, complete
    public $uploadProgress = [];
    public $overallProgress = 0;
    public $maxFileSize = 50; // MB
    public $maxFiles = 20;
    public $maxTotalSize = 500; // MB

    protected $rules = [
        'files.*' => 'file|max:51200', // 50MB in KB
    ];

    public function mount()
    {
        $this->reset();
    }

    public function updatedFiles()
    {
        $this->validate();

        // 파일 개수 제한 검사
        if (count($this->files) > $this->maxFiles) {
            session()->flash('error', "최대 {$this->maxFiles}개의 파일만 업로드할 수 있습니다.");
            return;
        }

        // 총 크기 제한 검사
        $totalSize = 0;
        foreach ($this->files as $file) {
            $totalSize += $file->getSize();
        }

        if ($totalSize > ($this->maxTotalSize * 1024 * 1024)) {
            session()->flash('error', "총 업로드 크기가 {$this->maxTotalSize}MB를 초과합니다.");
            return;
        }

        $this->currentStep = 'selected';
    }

    public function removeFile($index)
    {
        unset($this->files[$index]);
        $this->files = array_values($this->files);

        if (empty($this->files)) {
            $this->currentStep = 'upload';
        }
    }

    public function clearFiles()
    {
        $this->files = [];
        $this->currentStep = 'upload';
    }

    public function startUpload()
    {
        if (empty($this->files)) {
            return;
        }

        $this->currentStep = 'progress';
        $this->uploadFiles();
    }

    protected function uploadFiles()
    {
        foreach ($this->files as $index => $file) {
            try {
                // 실제 파일 업로드 로직 - 지정된 다운로드 폴더로 연결 (상대경로 사용)
                $downloadPath = __DIR__ . '/../../100-common/400-Storage/downloads';

                // 파일명 충돌 방지를 위한 고유 파일명 생성
                $extension = $file->getClientOriginalExtension();
                $basename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = date('Y-m-d_H-i-s') . '_' . uniqid() . '_' . $basename;
                if ($extension) {
                    $filename .= '.' . $extension;
                }

                // 디렉토리가 존재하지 않으면 생성
                if (!file_exists($downloadPath)) {
                    mkdir($downloadPath, 0755, true);
                }

                $file->move($downloadPath, $filename);
                $path = '/sandbox-files/downloads/' . $filename; // public 폴더 기준 경로

                // 데이터베이스에 파일 정보 저장 (PDO 직접 사용)
                $dbPath = __DIR__ . '/../../100-common/200-Database/release.sqlite';
                $fileId = null;

                if (file_exists($dbPath)) {
                    try {
                        $pdo = new PDO('sqlite:' . $dbPath);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);

                        $stmt = $pdo->prepare("
                            INSERT INTO uploaded_files (
                                stored_name, original_name, file_path, file_size, mime_type,
                                status, uploaded_at, created_at, updated_at
                            ) VALUES (?, ?, ?, ?, ?, 'uploaded', datetime('now'), datetime('now'), datetime('now'))
                        ");

                        $result = $stmt->execute([
                            $filename,
                            $file->getClientOriginalName(),
                            $path,
                            $file->getSize(),
                            $file->getMimeType()
                        ]);

                        if ($result) {
                            $fileId = $pdo->lastInsertId();
                        }
                    } catch (Exception $e) {
                        // 데이터베이스 오류 시에도 파일은 저장됨
                        \Log::error('File upload database error', ['error' => $e->getMessage()]);
                    }
                }

                $this->uploadedFiles[] = [
                    'id' => $fileId,
                    'original_name' => $file->getClientOriginalName(),
                    'file_name' => $filename,
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];

                $this->uploadProgress[$index] = 100;
                $this->calculateOverallProgress();

            } catch (\Exception $e) {
                session()->flash('error', "파일 업로드 중 오류가 발생했습니다: " . $e->getMessage());
                return;
            }
        }

        $this->currentStep = 'complete';
    }

    protected function calculateOverallProgress()
    {
        $totalProgress = array_sum($this->uploadProgress);
        $this->overallProgress = count($this->files) > 0 ?
            round($totalProgress / count($this->files)) : 0;
    }

    public function newUpload()
    {
        $this->reset();
        $this->currentStep = 'upload';
    }

    public function getTotalSize()
    {
        $totalSize = 0;
        foreach ($this->files as $file) {
            $totalSize += $file->getSize();
        }
        return $this->formatFileSize($totalSize);
    }

    public function getFileIcon($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $icons = [
            'jpg' => '🖼️', 'jpeg' => '🖼️', 'png' => '🖼️', 'gif' => '🖼️', 'webp' => '🖼️',
            'pdf' => '📄', 'doc' => '📝', 'docx' => '📝', 'txt' => '📄',
            'xls' => '📊', 'xlsx' => '📊', 'csv' => '📊',
            'zip' => '📦', 'rar' => '📦', '7z' => '📦',
            'mp4' => '🎥', 'avi' => '🎥', 'mov' => '🎥',
            'mp3' => '🎵', 'wav' => '🎵', 'flac' => '🎵'
        ];

        return $icons[$extension] ?? '📄';
    }

    public function formatFileSize($bytes)
    {
        if ($bytes == 0) return '0 B';

        $k = 1024;
        $sizes = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    public function render()
    {
        return view('livewire.file-upload-manager');
    }
}
