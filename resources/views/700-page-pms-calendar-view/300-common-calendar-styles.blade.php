<style>
/* FullCalendar 커스텀 스타일 */
.fc-event {
    border-left-width: 3px !important;
    cursor: pointer;
    transition: all 0.2s;
}

.fc-event:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.fc-daygrid-day.fc-day-today {
    background-color: #eff6ff !important;
}

.fc-daygrid-day-number {
    font-size: 0.875rem;
    font-weight: 500;
}

.fc-col-header-cell-cushion {
    padding: 8px 4px;
    font-weight: 600;
}

.fc-event-title {
    font-size: 0.75rem;
}

.fc-loading {
    display: none;
}

[x-cloak] {
    display: none !important;
}

/* 애니메이션 정의 */
@keyframes fade-in {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
