<div class="h-full bg-white shadow-lg border-r">
    <div class="p-4 border-b">
        <h2 class="text-lg font-semibold text-gray-800">{{ ucfirst($container) }} Sandbox</h2>
    </div>
    
    <nav class="p-4 space-y-2">
        <!-- Dashboard -->
        <a href="/sandbox/{{ $container }}/100-domain-pms/101-screen-dashboard" 
           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
            📊 Dashboard
        </a>
        
        <!-- Project Management -->
        <div class="space-y-1">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider px-3 py-1">
                Project Management
            </div>
            <a href="/sandbox/{{ $container }}/100-domain-pms/103-screen-table-view" 
               class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                📋 Table View
            </a>
            <a href="/sandbox/{{ $container }}/100-domain-pms/104-screen-kanban-board" 
               class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                📌 Kanban Board
            </a>
            <a href="/sandbox/{{ $container }}/100-domain-pms/106-screen-calendar-view" 
               class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                📅 Calendar View
            </a>
        </div>
        
        <!-- File Management -->
        <div class="space-y-1">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider px-3 py-1">
                File Management
            </div>
            <a href="/sandbox/{{ $container }}/101-domain-rfx/101-screen-multi-file-upload" 
               class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                📁 File Upload
            </a>
            <a href="/sandbox/{{ $container }}/101-domain-rfx/103-screen-uploaded-files-list" 
               class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                📂 Files List
            </a>
            <a href="/sandbox/{{ $container }}/101-domain-rfx/105-screen-document-analysis" 
               class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                🔍 Document Analysis
            </a>
        </div>
    </nav>
</div>