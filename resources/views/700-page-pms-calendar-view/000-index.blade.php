<!-- FullCalendar CDN 추가 -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />

@push('scripts-head')
<!-- FullCalendar JavaScript -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/ko.global.min.js'></script>
@endpush

@include('700-page-pms-calendar-view.300-common-calendar-styles')

<div class="p-6 bg-gray-50 min-h-screen"
     x-data="{
         showModal: @entangle('showEventDetailModal').live,
         showFilters: @entangle('showFilters').live
     }">
    <div class="max-w-7xl mx-auto">
        @include('700-page-pms-calendar-view.100-header-calendar-controls')

        @include('700-page-pms-calendar-view.200-section-filters')

        @include('700-page-pms-calendar-view.200-section-calendar-navigation')

        <!-- FullCalendar Container -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden p-4">
            <div id="fullcalendar" wire:ignore></div>
        </div>

        @include('700-page-pms-calendar-view.200-modal-create-event')

        @include('700-page-pms-calendar-view.200-modal-event-detail')

        @include('700-page-pms-calendar-view.200-component-flash-message')

        @include('700-page-pms-calendar-view.200-section-legend')
    </div>
</div>

@include('700-page-pms-calendar-view.400-script-calendar-init')
