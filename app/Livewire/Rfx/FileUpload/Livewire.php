<?php

namespace App\Livewire\Rfx\FileUpload;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\Rfx\FileUpload\Upload\Service;
use App\Services\Rfx\FileUpload\GetRecentUploads\Service as GetRecentUploadsService;

class Livewire extends Component
{
    use WithFileUploads;

    public $files = [];
    public $uploadProgress = [];
    public $guidelines;
    public $supportedFormats;
    public $maxFileSize;
    public $recentUploads = [];

    public function mount()
    {
        $this->loadUploadInfo();
        $this->loadRecentUploads();
    }

    public function uploadFiles()
    {
        $this->validate([
            'files.*' => 'required|file|max:10240', // 10MB max
        ]);

        $service = new Service();
        
        foreach ($this->files as $index => $file) {
            $this->uploadProgress[$index] = 0;
            
            // 파일 업로드 처리
            $result = $service->execute(['file' => $file]);
            
            if ($result['success']) {
                $this->uploadProgress[$index] = 100;
                $this->recentUploads[] = $result['data'];
            }
        }

        $this->files = [];
        $this->uploadProgress = [];
        
        session()->flash('message', '파일 업로드가 완료되었습니다.');
    }

    public function removeFile($index)
    {
        unset($this->files[$index]);
        unset($this->uploadProgress[$index]);
        $this->files = array_values($this->files);
        $this->uploadProgress = array_values($this->uploadProgress);
    }

    public function loadUploadInfo()
    {
        $this->guidelines = [
            '파일 크기는 최대 10MB까지 가능합니다.',
            '한 번에 최대 10개 파일까지 업로드할 수 있습니다.',
            '업로드된 파일은 자동으로 바이러스 검사를 수행합니다.',
            '분석 가능한 문서 형식을 권장합니다.'
        ];

        $this->supportedFormats = [
            'PDF' => ['pdf'],
            '문서' => ['doc', 'docx', 'txt'],
            '스프레드시트' => ['xls', 'xlsx', 'csv'],
            '이미지' => ['jpg', 'jpeg', 'png', 'gif'],
            '기타' => ['zip', 'rar']
        ];

        $this->maxFileSize = '10MB';
    }

    public function loadRecentUploads()
    {
        $service = new GetRecentUploadsService();
        $result = $service->execute([]);
        $this->recentUploads = (isset($result['success']) && $result['success']) ? $result['data'] : [];
    }

    public function render()
    {
        return view('700-page-rfx-file-upload/000-index');
    }
}