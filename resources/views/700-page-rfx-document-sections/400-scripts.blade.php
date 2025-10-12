<script>
// Alpine.js 또는 기타 JavaScript 로직이 필요한 경우 여기에 추가
document.addEventListener('livewire:init', () => {
    // Livewire 이벤트 리스너
    Livewire.on('sectionSelected', (event) => {
        console.log('Section selected:', event);
    });
});
</script>