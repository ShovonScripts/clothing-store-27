@php
    $announcement = settings('announcement_text');
    $enabled = settings('announcement_enabled', true);
@endphp

@if($enabled && $announcement)
<div class="bg-accent text-white py-2 px-4 text-center text-sm font-medium" role="status" aria-live="polite">
    {{ $announcement }}
</div>
@endif