<?php

namespace App\Livewire\Rfx\FormExecution;

use Livewire\Component;
use App\Services\Rfx\FormExecution\ListExecutions\Service as ListExecutionsService;
use App\Services\Rfx\FormExecution\GetForms\Service as GetFormsService;
use App\Services\Rfx\FormExecution\GetFormFields\Service as GetFormFieldsService;
use App\Services\Rfx\FormExecution\ExecuteForm\Service as ExecuteFormService;
use App\Services\Rfx\FormExecution\RetryExecution\Service as RetryExecutionService;
use App\Services\Rfx\FormExecution\CancelExecution\Service as CancelExecutionService;
use App\Services\Rfx\FormExecution\DownloadResult\Service as DownloadResultService;

class Livewire extends Component
{
    public $forms;
    public $selectedForm = null;
    public $formData = [];
    public $executions = [];
    public $selectedExecution = null;
    public $showExecuteModal = false;
    public $statusFilter = '';
    public $typeFilter = '';

    public function mount()
    {
        $this->loadForms();
        $this->loadExecutions();
    }

    public function updatedStatusFilter()
    {
        $this->loadExecutions();
    }

    public function updatedTypeFilter()
    {
        $this->loadForms();
    }

    public function selectForm($formId)
    {
        $service = new GetFormFieldsService();
        $this->selectedForm = collect($this->forms)->firstWhere('id', $formId);
        $this->formData = $service->execute($formId);
    }

    public function openExecuteModal()
    {
        if (!$this->selectedForm) {
            session()->flash('error', '먼저 폼을 선택해주세요.');
            return;
        }
        
        $this->showExecuteModal = true;
        $this->resetFormData();
    }

    public function closeExecuteModal()
    {
        $this->showExecuteModal = false;
    }

    public function executeForm()
    {
        $service = new ExecuteFormService();
        $result = $service->execute($this->selectedForm['id'], $this->formData);
        
        if ($result['success']) {
            session()->flash('message', '폼이 성공적으로 실행되었습니다.');
            $this->closeExecuteModal();
            $this->loadExecutions();
        }
    }

    public function selectExecution($executionId)
    {
        $this->selectedExecution = collect($this->executions)->firstWhere('id', $executionId);
    }

    public function retryExecution($executionId)
    {
        $service = new RetryExecutionService();
        $result = $service->execute($executionId);
        
        if ($result['success']) {
            session()->flash('message', '폼 실행을 재시작했습니다.');
            $this->loadExecutions();
        }
    }

    public function cancelExecution($executionId)
    {
        $service = new CancelExecutionService();
        $result = $service->execute($executionId);
        
        if ($result['success']) {
            session()->flash('message', '폼 실행이 취소되었습니다.');
            $this->loadExecutions();
        }
    }

    public function downloadResult($executionId)
    {
        $service = new DownloadResultService();
        $result = $service->execute($executionId);
        
        if ($result['success']) {
            session()->flash('message', '결과 파일을 다운로드했습니다.');
        }
    }

    private function resetFormData()
    {
        $this->formData = [];
        if ($this->selectedForm) {
            $service = new GetFormFieldsService();
            $fields = $service->execute($this->selectedForm['id']);
            foreach ($fields as $field) {
                $this->formData[$field['name']] = $field['defaultValue'] ?? '';
            }
        }
    }

    public function loadForms()
    {
        $service = new GetFormsService();
        $this->forms = $service->execute([
            'type' => $this->typeFilter
        ]);
    }

    public function loadExecutions()
    {
        $service = new ListExecutionsService();
        $this->executions = $service->execute([
            'status' => $this->statusFilter
        ]);
    }

    public function render()
    {
        return view('700-page-rfx-form-execution/000-index');
    }
}