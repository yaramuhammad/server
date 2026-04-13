{{-- SVG Pie Chart for PDF export --}}
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
    $radius = 130;

    $colors = ['#3b82f6', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];

    $total = 0;
    foreach ($categories as $cat) {
        $total += max(1, $cat['score_percentage'] ?? 0);
    }

    $slices = [];
    $currentAngle = -90;

    for ($i = 0; $i < $count; $i++) {
        $cat = $categories[$i];
        $pct = max(1, $cat['score_percentage'] ?? 0);
        $sliceAngle = ($pct / $total) * 360;

        $startRad = deg2rad($currentAngle);
        $endRad = deg2rad($currentAngle + $sliceAngle);

        $x1 = $cx + $radius * cos($startRad);
        $y1 = $cy + $radius * sin($startRad);
        $x2 = $cx + $radius * cos($endRad);
        $y2 = $cy + $radius * sin($endRad);

        $largeArc = ($sliceAngle > 180) ? 1 : 0;

        $path = sprintf(
            'M %d,%d L %.1f,%.1f A %d,%d 0 %d,1 %.1f,%.1f Z',
            $cx, $cy, $x1, $y1, $radius, $radius, $largeArc, $x2, $y2
        );

        $label = $cat['label'] ?? $cat['key'] ?? '';
        if (is_array($label)) {
            $label = $label[$locale] ?? $label['en'] ?? '';
        }
        $label = $s($label);

        $midAngle = deg2rad($currentAngle + $sliceAngle / 2);
        $labelR = $radius + 18;
        $labelX = $cx + $labelR * cos($midAngle);
        $labelY = $cy + $labelR * sin($midAngle);

        $midDeg = $currentAngle + $sliceAngle / 2;
        $anchor = 'middle';
        if ($midDeg > -80 && $midDeg < 80) $anchor = $isAr ? 'end' : 'start';
        elseif ($midDeg > 100 || $midDeg < -100) $anchor = $isAr ? 'start' : 'end';

        $slices[] = [
            'path' => $path,
            'color' => $colors[$i % count($colors)],
            'label' => $label,
            'pct' => round($cat['score_percentage'] ?? 0),
            'labelX' => round($labelX, 1),
            'labelY' => round($labelY, 1),
            'anchor' => $anchor,
        ];

        $currentAngle += $sliceAngle;
    }
@endphp

<div style="text-align: center; margin: 15px 0;">
    <img src="data:image/svg+xml;base64,{{ base64_encode('
        <svg xmlns="http://www.w3.org/2000/svg" width="' . ($size + 80) . '" height="' . ($size + 80) . '" viewBox="-40 -40 ' . ($size + 80) . ' ' . ($size + 80) . '">
            <rect x="-40" y="-40" width="' . ($size + 80) . '" height="' . ($size + 80) . '" fill="#ffffff"/>

            ' . implode('', array_map(function($s) {
                return '<path d="' . $s['path'] . '" fill="' . $s['color'] . '" stroke="#ffffff" stroke-width="2"/>';
            }, $slices)) . '

            ' . implode('', array_map(function($s) {
                return '<text x="' . $s['labelX'] . '" y="' . $s['labelY'] . '" text-anchor="' . $s['anchor'] . '" dominant-baseline="middle" font-size="9" fill="#6b7280" font-family="DejaVu Sans, sans-serif">' . htmlspecialchars($s['label']) . ' (' . $s['pct'] . '%)</text>';
            }, $slices)) . '
        </svg>
    ') }}" width="{{ $size + 80 }}" height="{{ $size + 80 }}" style="max-width: 100%;" />
</div>
