<?php

namespace App\Livewire\Pms\TicketDetail;

use Livewire\Component;

class Livewire extends Component
{
    public $ticket;
    public $comments;
    public $newComment = '';
    public $showEditForm = false;
    public $attachments;

    public function mount($ticketId = 1)
    {
        $this->loadTicketDetail($ticketId);
    }

    public function toggleEditForm()
    {
        $this->showEditForm = !$this->showEditForm;
    }

    public function addComment()
    {
        if (trim($this->newComment)) {
            // 댓글 추가 로직 (실제로는 Service를 호출)
            $this->comments[] = [
                'id' => count($this->comments) + 1,
                'content' => $this->newComment,
                'author' => '현재 사용자',
                'created_at' => now()->format('Y-m-d H:i:s'),
                'avatar' => '👤'
            ];
            $this->newComment = '';
        }
    }

    public function updateTicketStatus($status)
    {
        $this->ticket['status'] = $status;
        // 상태 업데이트 로직 (실제로는 Service를 호출)
    }

    public function updateTicketPriority($priority)
    {
        $this->ticket['priority'] = $priority;
        // 우선순위 업데이트 로직 (실제로는 Service를 호출)
    }

    public function loadTicketDetail($ticketId)
    {
        $service = new \App\Services\Pms\TicketDetail\Service();
        $data = $service->execute($ticketId);

        $this->ticket = $data['ticket'];
        $this->comments = $data['comments'];
        $this->attachments = $data['attachments'];
    }

    public function render()
    {
        return view('700-page-pms-ticket-detail.000-index')
            ->layout('700-page-pms-common.000-layout', ['title' => '티켓 상세']);
    }
}