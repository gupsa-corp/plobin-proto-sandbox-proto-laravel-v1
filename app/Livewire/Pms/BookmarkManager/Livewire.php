<?php

namespace App\Livewire\Pms\BookmarkManager;

use Livewire\Component;

class Livewire extends Component
{
    public $bookmarks;
    public $categories;
    public $selectedCategory = 'all';
    public $showBookmarkForm = false;
    public $editingBookmark = null;
    public $bookmarkForm = [
        'title' => '',
        'url' => '',
        'category' => '',
        'description' => ''
    ];

    public function mount()
    {
        $this->loadBookmarks();
        $this->loadCategories();
    }

    public function selectCategory($category)
    {
        $this->selectedCategory = $category;
        $this->filterBookmarks();
    }

    public function openBookmarkForm($bookmarkId = null)
    {
        $this->editingBookmark = $bookmarkId;
        if ($bookmarkId) {
            $bookmark = collect($this->bookmarks)->firstWhere('id', $bookmarkId);
            $this->bookmarkForm = [
                'title' => $bookmark['title'],
                'url' => $bookmark['url'],
                'category' => $bookmark['category'],
                'description' => $bookmark['description']
            ];
        } else {
            $this->bookmarkForm = [
                'title' => '',
                'url' => '',
                'category' => '',
                'description' => ''
            ];
        }
        $this->showBookmarkForm = true;
    }

    public function closeBookmarkForm()
    {
        $this->showBookmarkForm = false;
        $this->editingBookmark = null;
        $this->bookmarkForm = [
            'title' => '',
            'url' => '',
            'category' => '',
            'description' => ''
        ];
    }

    public function saveBookmark()
    {
        // 북마크 저장 로직 (실제로는 Service를 호출)
        $this->closeBookmarkForm();
        $this->loadBookmarks();
    }

    public function deleteBookmark($bookmarkId)
    {
        // 북마크 삭제 로직 (실제로는 Service를 호출)
        $this->loadBookmarks();
    }

    public function filterBookmarks()
    {
        $this->loadBookmarks();
    }

    public function loadBookmarks()
    {
        $allBookmarks = [
            [
                'id' => 1,
                'title' => 'Laravel 공식 문서',
                'url' => 'https://laravel.com/docs',
                'category' => 'development',
                'description' => 'Laravel 프레임워크 공식 문서',
                'favicon' => '🌐',
                'created_at' => '2024-10-01'
            ],
            [
                'id' => 2,
                'title' => 'Tailwind CSS',
                'url' => 'https://tailwindcss.com',
                'category' => 'design',
                'description' => 'CSS 프레임워크 Tailwind CSS',
                'favicon' => '🎨',
                'created_at' => '2024-10-02'
            ],
            [
                'id' => 3,
                'title' => 'GitHub',
                'url' => 'https://github.com',
                'category' => 'tools',
                'description' => '소스 코드 저장소',
                'favicon' => '📁',
                'created_at' => '2024-10-03'
            ],
            [
                'id' => 4,
                'title' => 'Figma',
                'url' => 'https://figma.com',
                'category' => 'design',
                'description' => '협업 디자인 도구',
                'favicon' => '🎨',
                'created_at' => '2024-10-04'
            ],
            [
                'id' => 5,
                'title' => 'Stack Overflow',
                'url' => 'https://stackoverflow.com',
                'category' => 'development',
                'description' => '개발자 질문 답변 커뮤니티',
                'favicon' => '💬',
                'created_at' => '2024-10-05'
            ]
        ];

        $this->bookmarks = collect($allBookmarks)->filter(function($bookmark) {
            return $this->selectedCategory === 'all' || $bookmark['category'] === $this->selectedCategory;
        })->values()->toArray();
    }

    public function loadCategories()
    {
        $this->categories = [
            ['id' => 'all', 'name' => '전체', 'count' => 5],
            ['id' => 'development', 'name' => '개발', 'count' => 2],
            ['id' => 'design', 'name' => '디자인', 'count' => 2],
            ['id' => 'tools', 'name' => '도구', 'count' => 1]
        ];
    }

    public function render()
    {
        return view('700-page-pms-bookmark-manager/000-index');
    }
}