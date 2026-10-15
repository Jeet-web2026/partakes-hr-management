@if(session('success'))
<div class="mb-4 rounded-lg border bg-blue-950 border-blue-800 p-4 text-white message-body">
    <div class="flex flex-row items-start justify-between">
        <div class="flex items-center gap-3">
            <i class="ri-checkbox-circle-line text-2xl"></i>
            <span>{{ session('success') }}</span>
        </div>
        <span class="cursor-pointer x"><i class="ri-close-line text-2xl"></i></span>
    </div>
</div>
@endif
@if(session('error'))
<div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800 message-body">
    <div class="flex flex-row items-start justify-between">
        <div class="flex items-center gap-3">
            <i class="ri-error-warning-line text-2xl"></i>
            <span>{{ session('error') }}</span>
        </div>
        <span class="cursor-pointer x"><i class="ri-close-line text-2xl"></i></span>
    </div>
</div>
@endif