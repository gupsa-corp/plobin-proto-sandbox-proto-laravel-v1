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
        // 티켓 상세 정보 로드 (실제로는 Service를 호출)
        $this->ticket = [
            'id' => $ticketId,
            'title' => '웹사이트 로그인 버튼이 작동하지 않습니다',
            'description' => '메인 페이지의 로그인 버튼을 클릭해도 아무 반응이 없습니다. 콘솔에는 JavaScript 오류가 표시됩니다.',
            'status' => 'open',
            'priority' => 'high',
            'type' => 'bug',
            'reporter' => '김사용자',
            'assignee' => '이개발자',
            'created_at' => '2024-10-09 14:30:00',
            'updated_at' => '2024-10-09 16:20:00',
            'project' => '웹사이트 리뉴얼',
            'tags' => ['UI', 'JavaScript', 'Login']
        ];

        $this->comments = [
            [
                'id' => 1,
                'content' => '문제를 확인했습니다. JavaScript 파일에서 이벤트 리스너가 제대로 등록되지 않고 있는 것 같습니다.',
                'author' => '이개발자',
                'created_at' => '2024-10-09 15:00:00',
                'avatar' => '👨‍💻'
            ],
            [
                'id' => 2,
                'content' => '추가 정보: Chrome 개발자 도구에서 확인한 오류 메시지를 첨부합니다.',
                'author' => '김사용자',
                'created_at' => '2024-10-09 15:30:00',
                'avatar' => '👤'
            ]
        ];

        $this->attachments = [
            [
                'id' => 1,
                'name' => 'console-error.png',
                'size' => '245 KB',
                'type' => 'image',
                'uploaded_at' => '2024-10-09 15:30:00'
            ],
            [
                'id' => 2,
                'name' => 'network-tab.png',
                'size' => '156 KB',
                'type' => 'image',
                'uploaded_at' => '2024-10-09 15:35:00'
            ]
        ];
    }

    public function render()
    {
        return view('700-page-pms-ticket-detail/000-index');
    }
}