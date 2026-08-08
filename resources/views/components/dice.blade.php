{{-- resources/views/components/dice.blade.php --}}
@props(['value'])

@php
    $pips = match((int) $value) {
        1 => ['center'],
        2 => ['top-left', 'bottom-right'],
        3 => ['top-left', 'center', 'bottom-right'],
        4 => ['top-left', 'top-right', 'bottom-left', 'bottom-right'],
        5 => ['top-left', 'top-right', 'center', 'bottom-left', 'bottom-right'],
        6 => ['top-left', 'top-right', 'mid-left', 'mid-right', 'bottom-left', 'bottom-right'],
        default => []
    };
@endphp

<div class="dice" {{ $attributes }}>
    @foreach ($pips as $position)
        <div class="pip {{ $position }}"></div>
    @endforeach
</div>