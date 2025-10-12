<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plobin Proto - 통합 관리 시스템</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto p-6">
        <!-- 헤더 -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Plobin Proto 통합 관리 시스템</h1>
            <p class="text-gray-600">프로젝트 관리와 문서 분석을 한 곳에서</p>
        </div>

        <!-- PMS 프로젝트 관리 시스템 -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">PMS - 프로젝트 관리 시스템</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">

                <!-- 대시보드 -->
                <a href="/pms/dashboard" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-blue-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">대시보드</h3>
                            <p class="text-sm text-gray-500">프로젝트 현황 통계</p>
                        </div>
                    </div>
                </a>

                <!-- 프로젝트 목록 -->
                <a href="/pms/projects" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-blue-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">프로젝트 목록</h3>
                            <p class="text-sm text-gray-500">카드 형태 프로젝트 관리</p>
                        </div>
                    </div>
                </a>

                <!-- 테이블 뷰 -->
                <a href="/pms/table-view" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-blue-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">테이블 뷰</h3>
                            <p class="text-sm text-gray-500">테이블 형태 관리</p>
                        </div>
                    </div>
                </a>

                <!-- 칸반 보드 -->
                <a href="/pms/kanban" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-blue-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0v10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2H9a2 2 0 00-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">칸반 보드</h3>
                            <p class="text-sm text-gray-500">드래그 앤 드롭 관리</p>
                        </div>
                    </div>
                </a>

                <!-- 간트 차트 -->
                <a href="/pms/gantt" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-blue-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">간트 차트</h3>
                            <p class="text-sm text-gray-500">일정 타임라인</p>
                        </div>
                    </div>
                </a>

                <!-- 캘린더 뷰 -->
                <a href="/pms/calendar" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-blue-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">캘린더 뷰</h3>
                            <p class="text-sm text-gray-500">달력 형태 일정</p>
                        </div>
                    </div>
                </a>

                <!-- 사용자 권한 -->
                <a href="/pms/permissions" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-blue-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">사용자 권한</h3>
                            <p class="text-sm text-gray-500">권한 확인</p>
                        </div>
                    </div>
                </a>

                <!-- 티켓 관리 -->
                <a href="/pms/ticket" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-blue-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">티켓 관리</h3>
                            <p class="text-sm text-gray-500">이슈 티켓</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>

        <!-- RFX 문서 분석 시스템 -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">RFX - 문서 분석 시스템</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">

                <!-- RFX 대시보드 -->
                <a href="/rfx/dashboard" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-purple-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">RFX 대시보드</h3>
                            <p class="text-sm text-gray-500">문서 분석 현황</p>
                        </div>
                    </div>
                </a>

                <!-- 파일 업로드 -->
                <a href="/rfx/upload" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-purple-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">파일 업로드</h3>
                            <p class="text-sm text-gray-500">다중 파일 업로드</p>
                        </div>
                    </div>
                </a>

                <!-- 파일 목록 -->
                <a href="/rfx/files" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-purple-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">파일 목록</h3>
                            <p class="text-sm text-gray-500">파일 관리 및 조회</p>
                        </div>
                    </div>
                </a>

                <!-- 문서 분석 -->
                <a href="/rfx/analysis" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-purple-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">문서 분석</h3>
                            <p class="text-sm text-gray-500">AI 분석 결과</p>
                        </div>
                    </div>
                </a>

                <!-- 분석 요청 -->
                <a href="/rfx/requests" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-purple-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">분석 요청</h3>
                            <p class="text-sm text-gray-500">요청 관리</p>
                        </div>
                    </div>
                </a>

                <!-- 폼 실행 -->
                <a href="/rfx/forms" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-purple-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">폼 실행</h3>
                            <p class="text-sm text-gray-500">동적 폼 처리</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>

        <!-- 시스템 관리 -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">시스템 관리</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">

                <!-- 큐 모니터 -->
                <a href="/jobs" class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md hover:border-gray-300 transition-all">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16m-7 5h7"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">큐 모니터</h3>
                            <p class="text-sm text-gray-500">작업 큐 관리 및 모니터링</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>
    </div>
</body>
</html>
