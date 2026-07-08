@php
    $showUnit = $showUnit ?? false;
    $unitText = $showUnit && $product->unit ? $product->unit : null;
@endphp

@if ($product->is_on_sale)
    <span class="freshbox-sale-price">
        <span class="freshbox-price-amount">{{ number_format($product->current_price, 0, ',', '.') }}VN&#272;</span>
        @if ($unitText)
            <span class="freshbox-price-unit"> / {{ $unitText }}</span>
        @endif
    </span>
    <del class="freshbox-old-price">
        <span class="freshbox-price-amount">{{ number_format($product->price, 0, ',', '.') }}VN&#272;</span>
        @if ($unitText)
            <span class="freshbox-price-unit"> / {{ $unitText }}</span>
        @endif
    </del>
    <span class="freshbox-sale-badge">-{{ $product->sale_percent }}%</span>
@else
    <span>
        <span class="freshbox-price-amount">{{ number_format($product->price, 0, ',', '.') }}VN&#272;</span>
        @if ($unitText)
            <span class="freshbox-price-unit"> / {{ $unitText }}</span>
        @endif
    </span>
@endif
