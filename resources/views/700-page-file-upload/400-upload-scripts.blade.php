{{-- 파일 업로드 JavaScript 로직 --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const selectedFilesDiv = document.getElementById('selected-files');
    const fileList = document.getElementById('file-list');
    const fileCount = document.getElementById('file-count');
    const totalSize = document.getElementById('total-size');
    const clearFilesBtn = document.getElementById('clear-files');
    const uploadFilesBtn = document.getElementById('upload-files');
    const uploadProgress = document.getElementById('upload-progress');
    const progressList = document.getElementById('progress-list');
    const uploadComplete = document.getElementById('upload-complete');
    const overallProgressBar = document.getElementById('overall-progress-bar');
    const overallProgressText = document.getElementById('overall-progress-text');

    let selectedFiles = [];

    // 드래그 앤 드롭 이벤트
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-green-500', 'bg-green-50');
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-green-500', 'bg-green-50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-green-500', 'bg-green-50');
        handleFiles(e.dataTransfer.files);
    });

    // 드롭존 클릭 이벤트
    dropZone.addEventListener('click', () => {
        fileInput.click();
    });

    // 파일 선택 이벤트
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    // 파일 처리
    function handleFiles(files) {
        selectedFiles = [...selectedFiles, ...Array.from(files)];
        updateFileList();
    }

    // 파일 목록 업데이트
    function updateFileList() {
        fileList.innerHTML = '';
        let totalSizeBytes = 0;

        selectedFiles.forEach((file, index) => {
            totalSizeBytes += file.size;
            
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
            fileItem.innerHTML = `
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">${getFileIcon(file.name)}</span>
                    <div>
                        <p class="font-medium text-gray-900">${file.name}</p>
                        <p class="text-sm text-gray-500">${formatFileSize(file.size)}</p>
                    </div>
                </div>
                <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                    <span class="text-xl">🗑️</span>
                </button>
            `;
            fileList.appendChild(fileItem);
        });

        fileCount.textContent = selectedFiles.length;
        totalSize.textContent = formatFileSize(totalSizeBytes);
        
        if (selectedFiles.length > 0) {
            selectedFilesDiv.classList.remove('hidden');
        } else {
            selectedFilesDiv.classList.add('hidden');
        }
    }

    // 파일 삭제
    window.removeFile = function(index) {
        selectedFiles.splice(index, 1);
        updateFileList();
    };

    // 전체 파일 삭제
    clearFilesBtn.addEventListener('click', () => {
        selectedFiles = [];
        updateFileList();
    });

    // 업로드 시작
    uploadFilesBtn.addEventListener('click', () => {
        if (selectedFiles.length === 0) return;
        
        selectedFilesDiv.classList.add('hidden');
        uploadProgress.classList.remove('hidden');
        
        performActualUpload();
    });

    // 실제 파일 업로드
    function performActualUpload() {
        progressList.innerHTML = '';
        let completedFiles = 0;
        
        selectedFiles.forEach((file, index) => {
            const progressItem = document.createElement('div');
            progressItem.className = 'mb-3';
            progressItem.innerHTML = `
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-700">${file.name}</span>
                    <span id="progress-${index}" class="text-gray-500">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="bar-${index}" class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            `;
            progressList.appendChild(progressItem);
            
            // FormData 생성 및 업로드
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');
            
            // 실제 업로드 요청
            const xhr = new XMLHttpRequest();
            
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const progress = (e.loaded / e.total) * 100;
                    document.getElementById(`progress-${index}`).textContent = Math.round(progress) + '%';
                    document.getElementById(`bar-${index}`).style.width = progress + '%';
                    
                    // 전체 진행률 업데이트
                    const overallProgress = (completedFiles * 100 + progress) / selectedFiles.length;
                    overallProgressBar.style.width = overallProgress + '%';
                    overallProgressText.textContent = Math.round(overallProgress) + '%';
                }
            });
            
            xhr.addEventListener('load', () => {
                if (xhr.status === 200) {
                    completedFiles++;
                    document.getElementById(`progress-${index}`).textContent = '100%';
                    document.getElementById(`bar-${index}`).style.width = '100%';
                    
                    if (completedFiles === selectedFiles.length) {
                        setTimeout(() => {
                            uploadProgress.classList.add('hidden');
                            uploadComplete.classList.remove('hidden');
                        }, 500);
                    }
                } else {
                    document.getElementById(`progress-${index}`).textContent = '오류';
                    document.getElementById(`bar-${index}`).classList.add('bg-red-500');
                }
            });
            
            xhr.addEventListener('error', () => {
                document.getElementById(`progress-${index}`).textContent = '오류';
                document.getElementById(`bar-${index}`).classList.add('bg-red-500');
            });
            
            xhr.open('POST', '{{ route("api.file-upload.create") }}');
            xhr.send(formData);
        });
    }

    // 파일 아이콘 반환
    function getFileIcon(fileName) {
        const ext = fileName.split('.').pop().toLowerCase();
        const icons = {
            'jpg': '🖼️', 'jpeg': '🖼️', 'png': '🖼️', 'gif': '🖼️', 'webp': '🖼️',
            'pdf': '📄', 'doc': '📝', 'docx': '📝', 'txt': '📄',
            'xls': '📊', 'xlsx': '📊', 'csv': '📊',
            'zip': '📦', 'rar': '📦', '7z': '📦',
            'mp4': '🎥', 'avi': '🎥', 'mov': '🎥',
            'mp3': '🎵', 'wav': '🎵', 'flac': '🎵'
        };
        return icons[ext] || '📄';
    }

    // 파일 크기 포맷
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>