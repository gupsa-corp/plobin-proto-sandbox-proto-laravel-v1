{{-- 파일 목록 관리 JavaScript --}}
<script>
function uploadedFilesList() {
    return {
        files: [],
        selectedFiles: [],
        searchQuery: '',
        statusFilter: '',
        typeFilter: '',
        sortField: 'created_at',
        sortDirection: 'desc',
        loading: false,
        searchTimeout: null,
        showFileModal: false,
        showBulkAnalysisModal: false,
        selectedFile: null,
        bulkAnalysisType: 'document_analysis',

        pagination: {
            total: 0,
            limit: 20,
            offset: 0,
            hasNext: false,
            hasPrev: false
        },

        get currentPage() {
            return Math.floor(this.pagination.offset / this.pagination.limit) + 1;
        },

        async init() {
            await this.loadFiles();
        },

        async loadFiles() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    limit: this.pagination.limit,
                    offset: this.pagination.offset,
                    sort: this.sortField,
                    direction: this.sortDirection
                });

                if (this.searchQuery.trim()) {
                    params.append('search', this.searchQuery.trim());
                }

                if (this.statusFilter) {
                    params.append('status', this.statusFilter);
                }

                if (this.typeFilter) {
                    params.append('type', this.typeFilter);
                }

                const response = await fetch(`{{ route('api.file-upload.list') }}?${params}`);
                const result = await response.json();

                if (result.success && result.data) {
                    this.files = result.data.files || result.data;
                    this.pagination = {
                        total: result.data.total || this.files.length,
                        limit: this.pagination.limit,
                        offset: this.pagination.offset,
                        hasNext: this.pagination.offset + this.pagination.limit < (result.data.total || 0),
                        hasPrev: this.pagination.offset > 0
                    };
                } else {
                    console.error('API 응답 오류:', result.message);
                    this.files = [];
                }
            } catch (error) {
                console.error('파일 목록 로딩 실패:', error);
                this.files = [];
            } finally {
                this.loading = false;
            }
        },

        handleSearch() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            this.searchTimeout = setTimeout(() => {
                this.pagination.offset = 0;
                this.loadFiles();
            }, 500);
        },

        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'asc';
            }
            this.loadFiles();
        },

        loadNextPage() {
            if (this.pagination.hasNext) {
                this.pagination.offset += this.pagination.limit;
                this.loadFiles();
            }
        },

        loadPreviousPage() {
            if (this.pagination.hasPrev) {
                this.pagination.offset = Math.max(0, this.pagination.offset - this.pagination.limit);
                this.loadFiles();
            }
        },

        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedFiles = this.files.map(file => file.id);
            } else {
                this.selectedFiles = [];
            }
        },

        viewFile(file) {
            this.selectedFile = file;
            this.showFileModal = true;
        },

        downloadFile(file) {
            const downloadUrl = `/storage/${file.file_path}`;
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = file.original_name;
            link.style.display = 'none';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },

        async requestAnalysis(file) {
            try {
                const response = await fetch(`{{ route('api.document-analysis.create') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        file_id: file.id
                    })
                });

                const result = await response.json();

                if (result.success) {
                    file.analysis_status = 'processing';
                    file.is_analysis_requested = true;
                    alert('🤖 AI 분석 요청이 접수되었습니다.');
                    this.showFileModal = false;
                    await this.loadFiles(); // 목록 새로고침
                } else {
                    alert('🤖 AI 분석 요청에 실패했습니다: ' + result.message);
                }
            } catch (error) {
                console.error('AI 분석 요청 실패:', error);
                alert('🤖 AI 분석 요청에 실패했습니다.');
            }
        },

        async deleteFile(file) {
            if (!confirm('정말로 이 파일을 삭제하시겠습니까?')) return;

            try {
                const response = await fetch(`{{ route('api.file-upload.delete') }}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        file_id: file.id
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.files = this.files.filter(f => f.id !== file.id);
                    this.selectedFiles = this.selectedFiles.filter(id => id !== file.id);
                    alert('파일이 삭제되었습니다.');
                } else {
                    alert('파일 삭제에 실패했습니다: ' + result.message);
                }
            } catch (error) {
                console.error('파일 삭제 실패:', error);
                alert('파일 삭제에 실패했습니다.');
            }
        },

        bulkAnalyze() {
            if (this.selectedFiles.length === 0) return;
            this.showBulkAnalysisModal = true;
        },

        async submitBulkAnalysis() {
            try {
                for (const fileId of this.selectedFiles) {
                    const response = await fetch(`{{ route('api.document-analysis.create') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            file_id: fileId,
                            analysis_type: this.bulkAnalysisType
                        })
                    });

                    if (response.ok) {
                        const file = this.files.find(f => f.id == fileId);
                        if (file) {
                            file.analysis_status = 'processing';
                            file.is_analysis_requested = true;
                        }
                    }
                }

                this.selectedFiles = [];
                this.showBulkAnalysisModal = false;
                alert('🤖 일괄 AI 분석 요청이 접수되었습니다.');
                await this.loadFiles(); // 목록 새로고침
            } catch (error) {
                console.error('일괄 AI 분석 요청 실패:', error);
                alert('🤖 일괄 AI 분석 요청에 실패했습니다.');
            }
        },

        getFileIcon(mimeType) {
            const icons = {
                'application/pdf': 'PDF',
                'application/msword': 'DOC',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'DOC',
                'application/vnd.ms-excel': 'XLS',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'XLS',
                'text/plain': 'TXT',
                'text/csv': 'CSV'
            };
            
            if (mimeType && mimeType.startsWith('image/')) {
                return 'IMG';
            }
            
            return icons[mimeType] || 'FILE';
        },

        getFileTypeClass(mimeType) {
            const classes = {
                'application/pdf': 'bg-red-500',
                'application/msword': 'bg-blue-500',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'bg-blue-500',
                'application/vnd.ms-excel': 'bg-green-500',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'bg-green-500',
                'text/plain': 'bg-gray-500',
                'text/csv': 'bg-purple-500'
            };
            
            if (mimeType && mimeType.startsWith('image/')) {
                return 'bg-pink-500';
            }
            
            return classes[mimeType] || 'bg-gray-500';
        },

        getFileTypeName(mimeType) {
            const names = {
                'application/pdf': 'PDF',
                'application/msword': 'Word',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'Word',
                'application/vnd.ms-excel': 'Excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'Excel',
                'text/plain': '텍스트',
                'text/csv': 'CSV'
            };
            
            if (mimeType && mimeType.startsWith('image/')) {
                return '이미지';
            }
            
            return names[mimeType] || '알 수 없음';
        },

        getStatusClass(status) {
            const statusClasses = {
                'pending': 'bg-gray-100 text-gray-800',
                'processing': 'bg-yellow-100 text-yellow-800',
                'completed': 'bg-green-100 text-green-800',
                'failed': 'bg-red-100 text-red-800'
            };
            return statusClasses[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusText(status) {
            const statusTexts = {
                'pending': '대기중',
                'processing': '분석 중',
                'completed': '분석 완료',
                'failed': '오류'
            };
            return statusTexts[status] || status;
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        formatDate(datetime) {
            if (!datetime) return '';
            const date = new Date(datetime);
            return date.toLocaleDateString('ko-KR') + ' ' + date.toLocaleTimeString('ko-KR', { hour: '2-digit', minute: '2-digit' });
        }
    }
}
</script>