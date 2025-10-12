<?php

namespace App\Livewire\Pms\ApiDocumentation;

use Livewire\Component;

class Livewire extends Component
{
    public $apis;
    public $selectedCategory = 'all';
    public $searchTerm = '';
    public $selectedApi = null;

    public function mount()
    {
        $this->loadApiDocumentation();
    }

    public function selectCategory($category)
    {
        $this->selectedCategory = $category;
        $this->filterApis();
    }

    public function selectApi($apiId)
    {
        $this->selectedApi = collect($this->apis)->firstWhere('id', $apiId);
    }

    public function filterApis()
    {
        $this->loadApiDocumentation();
    }

    public function loadApiDocumentation()
    {
        $allApis = [
            [
                'id' => 1,
                'name' => 'GET /api/projects',
                'description' => '프로젝트 목록 조회',
                'method' => 'GET',
                'category' => 'projects',
                'endpoint' => '/api/projects',
                'parameters' => [
                    ['name' => 'page', 'type' => 'int', 'required' => false, 'description' => '페이지 번호'],
                    ['name' => 'limit', 'type' => 'int', 'required' => false, 'description' => '페이지당 항목 수'],
                    ['name' => 'status', 'type' => 'string', 'required' => false, 'description' => '프로젝트 상태']
                ],
                'response' => [
                    'success' => true,
                    'data' => [
                        ['id' => 1, 'name' => '프로젝트 A', 'status' => 'active']
                    ]
                ]
            ],
            [
                'id' => 2,
                'name' => 'POST /api/projects',
                'description' => '새 프로젝트 생성',
                'method' => 'POST',
                'category' => 'projects',
                'endpoint' => '/api/projects',
                'parameters' => [
                    ['name' => 'name', 'type' => 'string', 'required' => true, 'description' => '프로젝트명'],
                    ['name' => 'description', 'type' => 'string', 'required' => false, 'description' => '프로젝트 설명'],
                    ['name' => 'status', 'type' => 'string', 'required' => true, 'description' => '프로젝트 상태']
                ],
                'response' => [
                    'success' => true,
                    'data' => ['id' => 1, 'name' => '프로젝트 A', 'status' => 'active']
                ]
            ],
            [
                'id' => 3,
                'name' => 'GET /api/dashboard/stats',
                'description' => '대시보드 통계 조회',
                'method' => 'GET',
                'category' => 'dashboard',
                'endpoint' => '/api/dashboard/stats',
                'parameters' => [],
                'response' => [
                    'success' => true,
                    'data' => [
                        'totalProjects' => 42,
                        'activeProjects' => 18
                    ]
                ]
            ]
        ];

        $this->apis = collect($allApis)->filter(function($api) {
            $categoryMatch = $this->selectedCategory === 'all' || $api['category'] === $this->selectedCategory;
            $searchMatch = empty($this->searchTerm) || 
                          str_contains(strtolower($api['name']), strtolower($this->searchTerm)) ||
                          str_contains(strtolower($api['description']), strtolower($this->searchTerm));
            
            return $categoryMatch && $searchMatch;
        })->values()->toArray();
    }

    public function render()
    {
        return view('700-page-pms-api-documentation.000-index')
            ->layout('700-page-pms-common.000-layout', ['title' => 'API 문서']);
    }
}