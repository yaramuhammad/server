{{-- SVG Polar Area Chart for PDF export --}}
{{-- Each category is a sector from center with radius proportional to score_percentage --}}
@php
    $categories = $categories ?? [];
    $locale = $locale ?? 'en';
    $isAr = $locale === 'ar';
    $arSvc = app(\App\Services\ArabicTextService::class);
    $s = fn(?string $text) => $arSvc->shape($text ?? '');
    $count = count($categories);
    if ($count < 1) return;

    $size = 340;
    $cx = $size / 2;
    $cy = $size / 2;
    $maxRadius = 130;
    $angleStep = 360 / $count;

    $colors = ['#3b82f6', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];

    $sectors = [];
    for ($i = 0; $i < $count; $i++) {
        $cat = $categories[$i];
        $pct = min(100, max(0, $cat['score_percentage'] ?? 0));
        $radius = ($maxRadius / 100) * $pct;

        $startAngle = -90 + ($i * $angleStep);
        $endAngle = -90 + (($i + 1) * $angleStep);

        $startRad = deg2rad($startAngle);
        $endRad = deg2rad($endAngle);

        $x1 = $cx + $radius * cos($startRad);
        $y1 = $cy + $radius * sin($startRad);
        $x2 = $cx + $radius * cos($endRad);
        $y2 = $cy + $radius * sin($endRad);

        $largeArc = ($angleStep > 180) ? 1 : 0;

        $label = $cat['label'] ?? $cat['key'] ?? '';
        if (is_array($label)) {
            $label = $label[$locale] ?? $label['en'] ?? '';
        }
        $label = $s($label);

        // Label position at mid-angle, outside max radius
        $midAngle = deg2rad($startAngle + $angleStep / 2);
        $labelR = $maxRadius + 20;
        $labelX = $cx + $labelR * cos($midAngle);
        $labelY = $cy + $labelR * sin($midAngle);

        $anchor = 'middle';
        $midDeg = $startAngle + $angleStep / 2;
        if ($midDeg > -80 && $midDeg < 80) $anchor = $isAr ? 'end' : 'start';
        elseif ($midDeg > 100 || $midDeg < -100) $anchor = $isAr ? 'start' : 'end';

        $sectors[] = [
            'path' => "M {$cx},{$cy} L " . round($x1, 1) . "," . round($y1, 1) . " A {$radius},{$radius} 0 {$largeArc},1 " . round($x2, 1) . "," . round($y2, 1) . " Z",
            'color' => $colors[$i % count($colors)],
            'label' => $label,
            'pct' => round($pct),
            'labelX' => round($labelX, 1),
            'labelY' => round($labelY, 1),
            'anchor' => $anchor,
        ];
    }

    // Grid circles
    $gridRadii = [];
    for ($level = 1; $level <= 4; $level++) {
        $gridRadii[] = round(($maxRadius / 4) * $level, 1);
    }

    $fontFamily = 'DejaVu Sans, sans-serif';
@endphp

<div style="text-align: center; margin: 15px 0;">
    <img src="data:image/svg+xml;base64,{{ base64_encode('
        <svg xmlns="http://www.w3.org/2000/svg" width="' . ($size + 80) . '" height="' . ($size + 80) . '" viewBox="-40 -40 ' . ($size + 80) . ' ' . ($size + 80) . '">
            <rect x="-40" y="-40" width="' . ($size + 80) . '" height="' . ($size + 80) . '" fill="#ffffff"/>

            ' . implode('', array_map(function($r) use ($cx, $cy) {
                return '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . $r . '" fill="none" stroke="#e5e7eb" stroke-width="0.8"/>';
            }, $gridRadii)) . '

            ' . implode('', array_map(function($s) {
                return '<path d="' . $s['path'] . '" fill="' . $s['color'] . '" fill-opacity="0.6" stroke="' . $s['color'] . '" stroke-width="1.5"/>';
            }, $sectors)) . '

            ' . implode('', array_map(function($s) use ($fontFamily) {
                return '<text x="' . $s['labelX'] . '" y="' . $s['labelY'] . '" text-anchor="' . $s['anchor'] . '" dominant-baseline="middle" font-size="9" fill="#6b7280" font-family="' . $fontFamily . '">' . htmlspecialchars($s['label']) . ' (' . $s['pct'] . '%)</text>';
            }, $sectors)) . '
        </svg>
    ') }}" width="{{ $size + 80 }}" height="{{ $size + 80 }}" style="max-width: 100%;" />
</div>
