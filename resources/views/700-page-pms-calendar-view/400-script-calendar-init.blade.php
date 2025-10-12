@push('scripts')
<script>
// Livewire 이벤트 리스너 등록 - Using window event listeners for Livewire dispatched events
window.addEventListener('view-mode-changed', (event) => {
    console.log('View mode changed event received:', event.detail);
    const mode = event.detail.mode || event.detail[0]?.mode || 'month';
    console.log('Changing calendar view to:', mode === 'week' ? 'timeGridWeek' : 'dayGridMonth');

    if (window.calendarInstance) {
        window.calendarInstance.changeView(mode === 'week' ? 'timeGridWeek' : 'dayGridMonth');
    }
});

window.addEventListener('calendar-updated', () => {
    console.log('Calendar updated event received');
    if (window.calendarInstance) {
        window.calendarInstance.refetchEvents();
        const livewireComponent = Livewire.first();
        livewireComponent.get('currentDate').then(currentDate => {
            if (currentDate && window.calendarInstance) {
                window.calendarInstance.gotoDate(currentDate);
            }
        });
    }
});

document.addEventListener('livewire:navigated', function() {
    initCalendar();
});

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('fullcalendar')) {
        initCalendar();
    }
});

function initCalendar() {
    const calendarEl = document.getElementById('fullcalendar');
    if (!calendarEl) {
        console.log('Calendar element not found');
        return;
    }

    // Check if calendar already initialized
    if (window.calendarInstance) {
        console.log('Calendar already initialized, destroying old instance');
        window.calendarInstance.destroy();
    }

    // Livewire 컴포넌트 찾기 - first() 사용 (전체 페이지가 하나의 Livewire 컴포넌트)
    const livewireComponent = Livewire.first();
    console.log('Initializing FullCalendar...');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ko',
        initialView: @js($viewMode === 'week' ? 'timeGridWeek' : 'dayGridMonth'),
        initialDate: @js($currentDate),
        headerToolbar: false,
        height: 'auto',

        events: function(fetchInfo, successCallback, failureCallback) {
            @this.call('getFullCalendarEvents').then(events => {
                console.log('Events loaded:', events.length);
                successCallback(events);
            });
        },

        eventClick: function(info) {
            console.log('Event clicked, ID:', info.event.id);
            const eventId = parseInt(info.event.id);

            // Call Livewire method via $wire
            Livewire.first().$wire.call('showEventDetail', eventId);
        },

        dateClick: function(info) {
            if (info.jsEvent.detail === 2) {
                @this.call('openCreateModal', info.dateStr);
            } else {
                @this.call('selectDate', info.dateStr);
            }
        },

        eventContent: function(arg) {
            let props = arg.event.extendedProps;
            let icon = '';
            if (props.status === 'completed') {
                icon = '<svg class="w-3 h-3 inline mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
            } else if (props.status === 'in_progress') {
                icon = '<div class="w-2 h-2 inline-block mr-1 bg-blue-500 rounded-full animate-pulse"></div>';
            }

            let hours = props.estimated_hours ?
                `<span class="text-[10px] ml-1">${props.estimated_hours}h</span>` : '';

            return {
                html: `
                    <div class="fc-event-main-frame px-1">
                        <div class="fc-event-title-container flex items-center justify-between">
                            <div class="flex items-center flex-1 min-w-0">
                                ${icon}
                                <span class="fc-event-title truncate">${arg.event.title}</span>
                            </div>
                            ${hours}
                        </div>
                    </div>
                `
            };
        },

        viewDidMount: () => {
            console.log('FullCalendar rendered');
        },

        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5],
            startTime: '09:00',
            endTime: '18:00',
        },

        weekends: true,

        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        }
    });

    calendar.render();

    // Store calendar instance globally for refresh
    window.calendarInstance = calendar;
}
</script>
@endpush
