<?php

namespace App\Livewire\Rfx\FormExecution;

use Livewire\Component;
use App\Services\Rfx\FormExecution\Service;

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
        $service = new Service();
        $this->selectedForm = collect($this->forms)->firstWhere('id', $formId);
        $this->formData = $service->getFormFields($formId);
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
        $service = new Service();
        $result = $service->executeForm($this->selectedForm['id'], $this->formData);
        
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
        $service = new Service();
        $result = $service->retryExecution($executionId);
        
        if ($result['success']) {
            session()->flash('message', '폼 실행을 재시작했습니다.');
            $this->loadExecutions();
        }
    }

    public function cancelExecution($executionId)
    {
        $service = new Service();
        $result = $service->cancelExecution($executionId);
        
        if ($result['success']) {
            session()->flash('message', '폼 실행이 취소되었습니다.');
            $this->loadExecutions();
        }
    }

    public function downloadResult($executionId)
    {
        $service = new Service();
        $result = $service->downloadResult($executionId);
        
        if ($result['success']) {
            session()->flash('message', '결과 파일을 다운로드했습니다.');
        }
    }

    private function resetFormData()
    {
        $this->formData = [];
        if ($this->selectedForm) {
            $service = new Service();
            $fields = $service->getFormFields($this->selectedForm['id']);
            foreach ($fields as $field) {
                $this->formData[$field['name']] = $field['defaultValue'] ?? '';
            }
        }
    }

    public function loadForms()
    {
        $service = new Service();
        $this->forms = $service->getForms([
            'type' => $this->typeFilter
        ]);
    }

    public function loadExecutions()
    {
        $service = new Service();
        $this->executions = $service->execute([
            'status' => $this->statusFilter
        ]);
    }

    public function render()
    {
        return view('700-page-rfx-form-execution/000-index');
    }
}