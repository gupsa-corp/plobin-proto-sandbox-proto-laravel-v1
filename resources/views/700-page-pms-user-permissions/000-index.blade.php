<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">사용자 권한</h1>
            <p class="text-gray-600 mt-2">현재 사용자의 권한과 역할을 확인하세요</p>
        </div>

        <!-- Current User Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">현재 사용자 정보</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">이름</dt>
                            <dd class="text-sm text-gray-900">{{ $currentUser['name'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">이메일</dt>
                            <dd class="text-sm text-gray-900">{{ $currentUser['email'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">부서</dt>
                            <dd class="text-sm text-gray-900">{{ $currentUser['department'] }}</dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">역할</dt>
                            <dd>
                                <span class="px-2 py-1 text-xs rounded-full font-medium bg-blue-100 text-blue-800">
                                    {{ collect($roles)->firstWhere('name', $currentUser['role'])['displayName'] ?? $currentUser['role'] }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">입사일</dt>
                            <dd class="text-sm text-gray-900">{{ $currentUser['joinDate'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">상태</dt>
                            <dd>
                                <span class="px-2 py-1 text-xs rounded-full font-medium bg-green-100 text-green-800">
                                    활성
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Permissions Matrix -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">권한 매트릭스</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                기능
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                읽기
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                쓰기
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                삭제
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($permissions as $key => $permission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $permission['name'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($permission['read'])
                                    <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($permission['write'])
                                    <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($permission['delete'])
                                    <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Role Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">역할 정보</h2>
            @php
                $currentRole = collect($roles)->firstWhere('name', $currentUser['role']);
            @endphp
            @if($currentRole)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-medium text-gray-900">{{ $currentRole['displayName'] }}</h3>
                    <span class="px-2 py-1 text-xs rounded-full font-medium bg-blue-100 text-blue-800">
                        현재 역할
                    </span>
                </div>
                <p class="text-sm text-gray-600">{{ $currentRole['description'] }}</p>
            </div>
            @endif
        </div>

        <!-- Available Roles -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">사용 가능한 역할</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($roles as $role)
                <div class="border border-gray-200 rounded-lg p-4 {{ $role['name'] === $currentUser['role'] ? 'bg-blue-50 border-blue-200' : '' }}">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-medium text-gray-900">{{ $role['displayName'] }}</h3>
                        @if($role['name'] === $currentUser['role'])
                        <span class="px-2 py-1 text-xs rounded-full font-medium bg-blue-100 text-blue-800">
                            현재
                        </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600">{{ $role['description'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Permission Legend -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">권한 설명</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">읽기</div>
                        <div class="text-gray-600">데이터 조회 권한</div>
                    </div>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">쓰기</div>
                        <div class="text-gray-600">데이터 생성/수정 권한</div>
                    </div>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">삭제</div>
                        <div class="text-gray-600">데이터 삭제 권한</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>