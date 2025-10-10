<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-full mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">간트 차트</h1>
                    <p class="text-gray-600 mt-2">프로젝트 일정을 시각적으로 관리하세요</p>
                </div>
                <div class="flex space-x-3">
                    <!-- View Mode -->
                    <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                        <button wire:click="changeViewMode('week')" 
                                class="px-4 py-2 text-sm {{ $viewMode === 'week' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            주별
                        </button>
                        <button wire:click="changeViewMode('month')" 
                                class="px-4 py-2 text-sm {{ $viewMode === 'month' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            월별
                        </button>
                        <button wire:click="changeViewMode('quarter')" 
                                class="px-4 py-2 text-sm {{ $viewMode === 'quarter' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            분기별
                        </button>
                    </div>
                    
                    <!-- Time Range -->
                    <select wire:model.live="timeRange" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1month">1개월</option>
                        <option value="3months">3개월</option>
                        <option value="6months">6개월</option>
                        <option value="1year">1년</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Gantt Chart Container -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Chart Header -->
            <div class="border-b border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">프로젝트 일정표</h3>
                    <div class="text-sm text-gray-600">
                        {{ count($projects) }}개 프로젝트
                    </div>
                </div>
            </div>

            <!-- Chart Body -->
            <div class="overflow-x-auto">
                <div class="min-w-full">
                    <!-- Timeline Header -->
                    <div class="flex bg-gray-50 border-b border-gray-200">
                        <!-- Project Names Column -->
                        <div class="w-80 p-4 border-r border-gray-200">
                            <h4 class="font-medium text-gray-900">프로젝트</h4>
                        </div>
                        
                        <!-- Timeline Columns -->
                        <div class="flex-1 flex">
                            @php
                                $months = ['2024-10', '2024-11', '2024-12', '2025-01'];
                            @endphp
                            @foreach($months as $month)
                            <div class="flex-1 p-2 text-center border-r border-gray-200 last:border-r-0">
                                <div class="text-sm font-medium text-gray-900">{{ $month }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Project Rows -->
                    @foreach($projects as $project)
                    <div class="flex border-b border-gray-100 hover:bg-gray-50">
                        <!-- Project Info -->
                        <div class="w-80 p-4 border-r border-gray-200">
                            <div>
                                <h5 class="font-medium text-gray-900 mb-1">{{ $project['name'] }}</h5>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        @if($project['status'] === 'in_progress') bg-blue-100 text-blue-800
                                        @elseif($project['status'] === 'planning') bg-yellow-100 text-yellow-800
                                        @elseif($project['status'] === 'completed') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        @if($project['status'] === 'in_progress') 진행중
                                        @elseif($project['status'] === 'planning') 계획중
                                        @elseif($project['status'] === 'completed') 완료
                                        @else 대기중
                                        @endif
                                    </span>
                                    <span class="text-xs text-gray-600">{{ $project['progress'] }}%</span>
                                </div>
                                <div class="mt-2 text-xs text-gray-500">
                                    {{ $project['startDate'] }} ~ {{ $project['endDate'] }}
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="flex-1 p-2 relative" style="height: 80px;">
                            @php
                                // 간단한 바 표시 계산 (실제로는 날짜 계산 필요)
                                $startPos = 10; // 시작 위치 (%)
                                $width = 40; // 바 길이 (%)
                                
                                if($project['status'] === 'completed') {
                                    $barColor = 'bg-green-500';
                                } elseif($project['status'] === 'in_progress') {
                                    $barColor = 'bg-blue-500';
                                } elseif($project['status'] === 'planning') {
                                    $barColor = 'bg-yellow-500';
                                } else {
                                    $barColor = 'bg-gray-400';
                                }
                            @endphp
                            
                            <!-- Gantt Bar -->
                            <div class="gantt-bar absolute top-1/2 transform -translate-y-1/2 h-6 {{ $barColor }} rounded cursor-move hover:shadow-md transition-all duration-300 ease-in-out"
                                 style="left: {{ $startPos }}%; width: {{ $width }}%;"
                                 data-project-id="{{ $project['id'] }}"
                                 data-start-pos="{{ $startPos }}"
                                 data-width="{{ $width }}"
                                 onclick="selectProject({{ $project['id'] }})">
                                <!-- Progress Overlay -->
                                <div class="h-full bg-black bg-opacity-20 rounded transition-all duration-300" 
                                     style="width: {{ $project['progress'] }}%"></div>
                                
                                <!-- Project Name on Bar -->
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <span class="text-xs text-white font-medium truncate px-2">
                                        {{ $project['name'] }}
                                    </span>
                                </div>

                                <!-- Resize Handles -->
                                <div class="resize-handle-left absolute left-0 top-0 w-2 h-full cursor-ew-resize bg-white bg-opacity-0 hover:bg-opacity-50 transition-all duration-200"></div>
                                <div class="resize-handle-right absolute right-0 top-0 w-2 h-full cursor-ew-resize bg-white bg-opacity-0 hover:bg-opacity-50 transition-all duration-200"></div>
                            </div>

                            <!-- Milestone Markers -->
                            <div class="absolute top-1/2 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-red-500 rounded-full"
                                 style="left: {{ $startPos }}%;" 
                                 title="시작일: {{ $project['startDate'] }}"></div>
                            <div class="absolute top-1/2 transform -translate-y-1/2 translate-x-1 w-2 h-2 bg-red-500 rounded-full"
                                 style="left: {{ $startPos + $width }}%;" 
                                 title="마감일: {{ $project['endDate'] }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Chart Footer -->
            <div class="bg-gray-50 p-4 border-t border-gray-200">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded mr-2"></div>
                            <span>완료</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded mr-2"></div>
                            <span>진행중</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded mr-2"></div>
                            <span>계획중</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-400 rounded mr-2"></div>
                            <span>대기중</span>
                        </div>
                    </div>
                    <div>
                        클릭하여 프로젝트 상세 정보 확인
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Detail Panel -->
        @if($selectedProject)
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @php
                $selected = collect($projects)->firstWhere('id', $selectedProject);
            @endphp
            @if($selected)
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $selected['name'] }} 상세 정보</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">기본 정보</h4>
                    <dl class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">상태:</dt>
                            <dd class="font-medium">
                                @if($selected['status'] === 'in_progress') 진행중
                                @elseif($selected['status'] === 'planning') 계획중
                                @elseif($selected['status'] === 'completed') 완료
                                @else 대기중
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">우선순위:</dt>
                            <dd class="font-medium">
                                @if($selected['priority'] === 'high') 높음
                                @elseif($selected['priority'] === 'medium') 보통
                                @else 낮음
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">진행률:</dt>
                            <dd class="font-medium">{{ $selected['progress'] }}%</dd>
                        </div>
                    </dl>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">일정</h4>
                    <dl class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">시작일:</dt>
                            <dd class="font-medium">{{ $selected['startDate'] }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">마감일:</dt>
                            <dd class="font-medium">{{ $selected['endDate'] }}</dd>
                        </div>
                    </dl>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">팀원</h4>
                    <div class="flex flex-wrap gap-1">
                        @foreach($selected['team'] as $member)
                        <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-full">{{ $member }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <h4 class="font-medium text-gray-900 mb-2">설명</h4>
                <p class="text-sm text-gray-600">{{ $selected['description'] }}</p>
            </div>
            
            <div class="mt-4 flex justify-end">
                <button wire:click="$set('selectedProject', null)" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    닫기
                </button>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Drag & Drop JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let isDragging = false;
            let isResizing = false;
            let currentElement = null;
            let dragStartX = 0;
            let initialLeft = 0;
            let initialWidth = 0;
            let resizeType = null;

            // 드래그 가능한 간트 바 초기화
            function initializeGanttBars() {
                const ganttBars = document.querySelectorAll('.gantt-bar');
                
                ganttBars.forEach(bar => {
                    // 메인 바 드래그 이벤트
                    bar.addEventListener('mousedown', function(e) {
                        if (e.target.classList.contains('resize-handle-left') || 
                            e.target.classList.contains('resize-handle-right')) {
                            return; // 리사이즈 핸들 클릭 시 드래그 무시
                        }
                        
                        isDragging = true;
                        currentElement = this;
                        dragStartX = e.clientX;
                        initialLeft = parseFloat(this.style.left);
                        
                        // 드래그 중 시각적 피드백
                        this.style.opacity = '0.8';
                        this.style.transform = 'translateY(-50%) scale(1.05)';
                        this.style.zIndex = '1000';
                        
                        e.preventDefault();
                    });

                    // 리사이즈 핸들 이벤트
                    const leftHandle = bar.querySelector('.resize-handle-left');
                    const rightHandle = bar.querySelector('.resize-handle-right');

                    if (leftHandle) {
                        leftHandle.addEventListener('mousedown', function(e) {
                            isResizing = true;
                            resizeType = 'left';
                            currentElement = bar;
                            dragStartX = e.clientX;
                            initialLeft = parseFloat(bar.style.left);
                            initialWidth = parseFloat(bar.style.width);
                            
                            bar.style.opacity = '0.8';
                            e.preventDefault();
                            e.stopPropagation();
                        });
                    }

                    if (rightHandle) {
                        rightHandle.addEventListener('mousedown', function(e) {
                            isResizing = true;
                            resizeType = 'right';
                            currentElement = bar;
                            dragStartX = e.clientX;
                            initialWidth = parseFloat(bar.style.width);
                            
                            bar.style.opacity = '0.8';
                            e.preventDefault();
                            e.stopPropagation();
                        });
                    }
                });
            }

            // 마우스 이동 이벤트
            document.addEventListener('mousemove', function(e) {
                if (isDragging && currentElement) {
                    const container = currentElement.closest('.flex-1');
                    const containerWidth = container.offsetWidth;
                    const deltaX = e.clientX - dragStartX;
                    const deltaPercent = (deltaX / containerWidth) * 100;
                    
                    let newLeft = initialLeft + deltaPercent;
                    
                    // 경계 체크
                    if (newLeft < 0) newLeft = 0;
                    if (newLeft > 85) newLeft = 85; // 여유 공간 확보
                    
                    currentElement.style.left = newLeft + '%';
                    
                    // 실시간 날짜 피드백 (옵션)
                    showDatePreview(currentElement, newLeft);
                    
                } else if (isResizing && currentElement) {
                    const container = currentElement.closest('.flex-1');
                    const containerWidth = container.offsetWidth;
                    const deltaX = e.clientX - dragStartX;
                    const deltaPercent = (deltaX / containerWidth) * 100;
                    
                    if (resizeType === 'left') {
                        let newLeft = initialLeft + deltaPercent;
                        let newWidth = initialWidth - deltaPercent;
                        
                        if (newLeft >= 0 && newWidth >= 5) { // 최소 너비 5%
                            currentElement.style.left = newLeft + '%';
                            currentElement.style.width = newWidth + '%';
                        }
                    } else if (resizeType === 'right') {
                        let newWidth = initialWidth + deltaPercent;
                        const currentLeft = parseFloat(currentElement.style.left);
                        
                        if (newWidth >= 5 && (currentLeft + newWidth) <= 95) { // 최소 너비와 경계 체크
                            currentElement.style.width = newWidth + '%';
                        }
                    }
                }
            });

            // 마우스 업 이벤트
            document.addEventListener('mouseup', function(e) {
                if (isDragging && currentElement) {
                    // 드래그 완료 시 애니메이션 효과
                    currentElement.style.opacity = '1';
                    currentElement.style.transform = 'translateY(-50%) scale(1)';
                    currentElement.style.zIndex = 'auto';
                    
                    // 위치 정보 업데이트
                    updateProjectPosition(currentElement);
                    
                    // 성공 애니메이션
                    showSuccessAnimation(currentElement);
                    
                    isDragging = false;
                    currentElement = null;
                    hideDatePreview();
                    
                } else if (isResizing && currentElement) {
                    // 리사이즈 완료
                    currentElement.style.opacity = '1';
                    
                    // 크기 정보 업데이트
                    updateProjectSize(currentElement);
                    
                    // 성공 애니메이션
                    showSuccessAnimation(currentElement);
                    
                    isResizing = false;
                    resizeType = null;
                    currentElement = null;
                }
            });

            // 날짜 미리보기 표시
            function showDatePreview(element, leftPercent) {
                let preview = document.getElementById('date-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.id = 'date-preview';
                    preview.className = 'fixed bg-black text-white px-3 py-1 rounded text-sm z-50 pointer-events-none transition-all duration-200';
                    document.body.appendChild(preview);
                }
                
                // 날짜 계산 (예시)
                const startDate = new Date();
                startDate.setDate(startDate.getDate() + Math.round(leftPercent * 1.2)); // 대략적 계산
                
                preview.textContent = `시작일: ${startDate.toLocaleDateString('ko-KR')}`;
                preview.style.left = (event.clientX + 10) + 'px';
                preview.style.top = (event.clientY - 30) + 'px';
                preview.style.display = 'block';
            }

            function hideDatePreview() {
                const preview = document.getElementById('date-preview');
                if (preview) {
                    preview.style.display = 'none';
                }
            }

            // 성공 애니메이션
            function showSuccessAnimation(element) {
                // 펄스 효과
                element.style.transition = 'all 0.3s ease-in-out';
                element.style.boxShadow = '0 0 20px rgba(34, 197, 94, 0.6)';
                
                setTimeout(() => {
                    element.style.boxShadow = 'none';
                    
                    // 체크마크 애니메이션
                    const checkmark = document.createElement('div');
                    checkmark.innerHTML = '✓';
                    checkmark.className = 'absolute -top-8 left-1/2 transform -translate-x-1/2 bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold animate-bounce';
                    element.appendChild(checkmark);
                    
                    setTimeout(() => {
                        checkmark.remove();
                    }, 1500);
                }, 300);
            }

            // 프로젝트 위치 업데이트
            function updateProjectPosition(element) {
                const projectId = element.dataset.projectId;
                const newLeft = parseFloat(element.style.left);
                const width = parseFloat(element.style.width);
                
                // 실제 날짜 계산 (예시)
                const startDate = calculateDateFromPercent(newLeft);
                const endDate = calculateDateFromPercent(newLeft + width);
                
                // Livewire 메서드 호출
                if (window.Livewire) {
                    window.Livewire.find(element.closest('[wire\\:id]').getAttribute('wire:id'))
                        .call('updateProjectDates', projectId, startDate, endDate);
                }
                
                console.log(`프로젝트 ${projectId} 위치 업데이트: ${startDate} ~ ${endDate}`);
            }

            // 프로젝트 크기 업데이트  
            function updateProjectSize(element) {
                const projectId = element.dataset.projectId;
                const left = parseFloat(element.style.left);
                const width = parseFloat(element.style.width);
                
                const startDate = calculateDateFromPercent(left);
                const endDate = calculateDateFromPercent(left + width);
                
                if (window.Livewire) {
                    window.Livewire.find(element.closest('[wire\\:id]').getAttribute('wire:id'))
                        .call('updateProjectDates', projectId, startDate, endDate);
                }
                
                console.log(`프로젝트 ${projectId} 크기 업데이트: ${startDate} ~ ${endDate}`);
            }

            // 퍼센트를 날짜로 변환 (예시 함수)
            function calculateDateFromPercent(percent) {
                const baseDate = new Date('2024-10-01');
                const daysToAdd = Math.round((percent / 100) * 120); // 4개월 = 120일
                baseDate.setDate(baseDate.getDate() + daysToAdd);
                return baseDate.toISOString().split('T')[0];
            }

            // 프로젝트 선택 함수
            window.selectProject = function(projectId) {
                if (!isDragging && !isResizing) {
                    if (window.Livewire) {
                        window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'))
                            .call('selectProject', projectId);
                    }
                }
            };

            // 초기화
            initializeGanttBars();

            // Livewire 업데이트 후 재초기화
            document.addEventListener('livewire:morph.updated', function() {
                setTimeout(initializeGanttBars, 100);
            });

            // 업데이트 성공 메시지 리스너
            window.addEventListener('project-updated', function(event) {
                // 성공 메시지 표시
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
                toast.textContent = event.detail.message || '프로젝트가 업데이트되었습니다.';
                document.body.appendChild(toast);
                
                // 슬라이드 인 애니메이션
                setTimeout(() => {
                    toast.style.transform = 'translateX(0)';
                }, 10);
                
                // 자동 제거
                setTimeout(() => {
                    toast.style.transform = 'translateX(full)';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            });
        });
    </script>
</div>