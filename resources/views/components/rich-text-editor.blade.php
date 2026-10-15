@props([
    'name' => 'remarks',
    'value' => '',
    'editable' => true,
])

@php
    $content = old($name, $value);
    if ($content && str_contains($content, '&lt;')) {
        $content = html_entity_decode($content);
    }
@endphp

<section class="card p-6">
    <h2 class="section-title">Internal Notes</h2>

    <div>
        <div
            id="{{ $name }}Editor"
            style="height:250px;"
            data-editable="{{ $editable ? 'true' : 'false' }}">
            {!! $content !!}
        </div>

        <input
            type="hidden"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ $content }}">

        @error($name)
        <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>
</section>