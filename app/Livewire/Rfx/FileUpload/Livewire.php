<?php

namespace App\Livewire\Rfx\FileUpload;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\Rfx\FileUpload\Upload\Service;
use App\Services\Rfx\FileUpload\GetRecentUploads\Service as GetRecentUploadsService;
use App\Services\Rfx\FileUpload\ProcessOcrRequest\Service as ProcessOcrRequestService;
use App\Services\Rfx\FileUpload\GetOcrRequests\Service as GetOcrRequestsService;
use App\Services\Rfx\FileUpload\GetOcrRequestDetail\Service as GetOcrRequestDetailService;
use App\Services\Rfx\FileUpload\DeleteOcrRequest\Service as DeleteOcrRequestService;

class Livewire extends Component
{
    use WithFileUploads;

    public $files = [];
    public $uploadProgress = [];
    public $guidelines;
    public $supportedFormats;
    public $maxFileSize;
    public $recentUploads = [];
    public $ocrRequests = [];
    public $selectedRequest = null;
    public $showDetailModal = false;

    public function mount()
    {
        $this->loadUploadInfo();
        $this->loadRecentUploads();
        $this->loadOcrRequests();
    }

    public function uploadFiles()
    {
        $this->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $uploadService = new Service();
        $ocrService = new ProcessOcrRequestService();

        foreach ($this->files as $index => $file) {
            $this->uploadProgress[$index] = 0;

            // 1단계: 파일을 디스크에 저장
            $uploadResult = $uploadService->execute(['file' => $file]);

            if (!$uploadResult['success']) {
                session()->flash('error', '파일 업로드 실패: ' . $uploadResult['message']);
                continue;
            }

            $this->uploadProgress[$index] = 50;

            // 2단계: 저장된 파일 경로로 OCR 처리 요청
            // Storage 퍼사드를 사용해 실제 경로 가져오기
            $storedName = $uploadResult['data']['stored_name'] ?? '';

            if ($storedName) {
                $filePath = \Illuminate\Support\Facades\Storage::disk('plobin_uploads')->path($storedName);

                if (file_exists($filePath)) {
                    $result = $ocrService->execute(['file_path' => $filePath]);

                    if ($result['success']) {
                        $this->uploadProgress[$index] = 100;
                    }
                }
            }
        }

        $this->files = [];
        $this->uploadProgress = [];
        $this->loadOcrRequests();

        session()->flash('message', 'OCR 처리 요청이 완료되었습니다.');
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
            '이미지 파일(JPG, PNG) 또는 PDF 파일만 업로드 가능합니다.',
            '파일 크기는 최대 10MB까지 가능합니다.',
            'OCR 처리는 업로드 후 자동으로 시작됩니다.',
            '처리 결과는 요청 목록에서 확인할 수 있습니다.'
        ];

        $this->supportedFormats = [
            '이미지' => ['jpg', 'jpeg', 'png'],
            'PDF' => ['pdf']
        ];

        $this->maxFileSize = '10MB';
    }

    public function loadRecentUploads()
    {
        $service = new GetRecentUploadsService();
        $result = $service->execute([]);
        $this->recentUploads = (isset($result['success']) && $result['success']) ? $result['data'] : [];
    }

    public function loadOcrRequests()
    {
        $service = new GetOcrRequestsService();
        $result = $service->execute(['page' => 1, 'limit' => 10]);
        $this->ocrRequests = (isset($result['success']) && $result['success']) ? $result['data']['requests'] : [];
    }

    public function viewRequestDetail($requestId)
    {
        $service = new GetOcrRequestDetailService();
        $result = $service->execute(['request_id' => $requestId]);

        if ($result['success']) {
            $this->selectedRequest = $result['data'];
            $this->showDetailModal = true;
        }
    }

    public function deleteRequest($requestId)
    {
        $service = new DeleteOcrRequestService();
        $result = $service->execute(['request_id' => $requestId]);

        if ($result['success']) {
            $this->loadOcrRequests();
            session()->flash('message', 'OCR 요청이 삭제되었습니다.');
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedRequest = null;
    }

    public function render()
    {
        return view('700-page-rfx-file-upload/000-index');
    }
}