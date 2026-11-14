{{-- Result reported as the top-ranked categories (e.g. Holland types), each with its description --}}
@php
    $pick = fn ($value) => is_array($value) ? ($value[$lang] ?? $value['en'] ?? '') : (string) $value;
    $ranks = $isAr
        ? ['النمط الأول', 'النمط الثاني', 'النمط الثالث', 'النمط الرابع', 'النمط الخامس', 'النمط السادس']
        : ['1st type', '2nd type', '3rd type', '4th type', '5th type', '6th type'];
@endphp

<p style="font-weight: bold; font-size: 11px; margin: 10px 0 6px; color: #1e40af;">
    {{ $s($isAr ? 'أبرز الأنماط لديك' : 'Your top types') }}
</p>

@foreach($topCategories as $i => $category)
    <div class="range-indicator" style="text-align: {{ $isAr ? 'right' : 'left' }};">
        <div class="range-label">{{ $s(($ranks[$i] ?? (string) ($i + 1)) . ': ' . $pick($category['label'] ?? $category['key'])) }}</div>
        @if(!empty($category['description']))
            <div class="range-description">{!! nl2br(e($s($pick($category['description'])))) !!}</div>
        @endif
    </div>
@endforeach
