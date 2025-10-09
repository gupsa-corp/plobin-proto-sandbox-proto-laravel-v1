@php
    $sandboxPath = storage_path("sandbox/container/{$container}");
    $contentPath = "{$sandboxPath}/{$domain}/{$page}/000-content.blade.php";
@endphp

@if(file_exists($contentPath))
    @include("sandbox.container.{$container}.{$domain}.{$page}.000-content")
@else
    <div class="p-8">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Content Not Found
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Content file not found at: {{ $contentPath }}</p>
                        <p class="mt-1">Container: {{ $container }} | Domain: {{ $domain }} | Page: {{ $page }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif