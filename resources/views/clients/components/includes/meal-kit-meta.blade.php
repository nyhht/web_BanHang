@php
    $totalTime = (int) ($product->prep_time ?? 0) + (int) ($product->cook_time ?? 0);
    $showStorage = $showStorage ?? false;
@endphp

@if ($product->serving_size || $totalTime || $product->calories || $product->expiry_days || ($showStorage && $product->storage_instruction))
    <div class="freshbox-meal-kit-meta">
        @if ($product->serving_size)
            <span><i class="fas fa-users"></i> {{ $product->serving_size }} người</span>
        @endif
        @if ($totalTime)
            <span><i class="far fa-clock"></i> {{ $totalTime }} phút</span>
        @endif
        @if ($product->calories)
            <span><i class="fas fa-fire"></i> {{ $product->calories }} kcal</span>
        @endif
        @if ($product->expiry_days)
            <span><i class="fas fa-calendar-check"></i> {{ $product->expiry_days }} ngày</span>
        @endif
        @if ($showStorage && $product->storage_instruction)
            <span class="freshbox-meal-kit-storage"><i class="fas fa-snowflake"></i> {{ $product->storage_instruction }}</span>
        @endif
    </div>
@endif
