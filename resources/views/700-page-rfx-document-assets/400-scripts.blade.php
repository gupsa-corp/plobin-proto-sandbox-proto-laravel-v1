<script>
    // Livewire 이벤트 리스너
    document.addEventListener('livewire:initialized', () => {
        // 상태 업데이트 이벤트
        Livewire.on('status-updated', (event) => {
            console.log('Status updated:', event.status);
        });

        // 편집 저장 이벤트
        Livewire.on('edit-saved', () => {
            console.log('Edit saved successfully');
        });
    });
</script>
