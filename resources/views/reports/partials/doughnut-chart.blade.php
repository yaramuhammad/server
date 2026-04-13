{{-- SVG Doughnut Chart for PDF export --}}
{{-- Each category as a colored arc slice with inner hole --}}
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
    $outerR = 130;
    $innerR = 70;

    $colors = ['#3b82f6', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];

    // Calculate total for proportional slicing
    $total = 0;
    foreach ($categories as $cat) {
        $total += max(1, $cat['score_percentage'] ?? 0);
    }

    $slices = [];
    $currentAngle = -90; // Start from top

    for ($i = 0; $i < $count; $i++) {
        $cat = $categories[$i];
        $pct = max(1, $cat['score_percentage'] ?? 0);
        $sliceAngle = ($pct / $total) * 360;

        $startRad = deg2rad($currentAngle);
        $endRad = deg2rad($currentAngle + $sliceAngle);

        // Outer arc points
        $ox1 = $cx + $outerR * cos($startRad);
        $oy1 = $cy + $outerR * sin($startRad);
        $ox2 = $cx + $outerR * cos($endRad);
        $oy2 = $cy + $outerR * sin($endRad);

        // Inner arc points
        $ix1 = $cx + $innerR * cos($startRad);
        $iy1 = $cy + $innerR * sin($startRad);
        $ix2 = $cx + $innerR * cos($endRad);
        $iy2 = $cy + $innerR * sin($endRad);

        $largeArc = ($sliceAngle > 180) ? 1 : 0;

        // Path: move to outer start, arc to outer end, line to inner end, arc back to inner start, close
        $path = sprintf(
            'M %.1f,%.1f A %d,%d 0 %d,1 %.1f,%.1f L %.1f,%.1f A %d,%d 0 %d,0 %.1f,%.1f Z',
            $ox1, $oy1,
            $outerR, $outerR, $largeArc, $ox2, $oy2,
            $ix2, $iy2,
            $innerR, $innerR, $largeArc, $ix1, $iy1
        );

        $label = $cat['label'] ?? $cat['key'] ?? '';
        if (is_array($label)) {
            $label = $label[$locale] ?? $label['en'] ?? '';
        }
        $label = $s($label);

        // Label at mid-angle outside the doughnut
        $midAngle = deg2rad($currentAngle + $sliceAngle / 2);
        $labelR = $outerR + 18;
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

    $fontFamily = 'DejaVu Sans, sans-serif';
@endphp

<div style="text-align: center; margin: 15px 0;">
    <img src="data:image/svg+xml;base64,{{ base64_encode('
        <svg xmlns="http://www.w3.org/2000/svg" width="' . ($size + 80) . '" height="' . ($size + 80) . '" viewBox="-40 -40 ' . ($size + 80) . ' ' . ($size + 80) . '">
            <rect x="-40" y="-40" width="' . ($size + 80) . '" height="' . ($size + 80) . '" fill="#ffffff"/>

            ' . implode('', array_map(function($s) {
                return '<path d="' . $s['path'] . '" fill="' . $s['color'] . '" stroke="#ffffff" stroke-width="2"/>';
            }, $slices)) . '

            ' . implode('', array_map(function($s) use ($fontFamily) {
                return '<text x="' . $s['labelX'] . '" y="' . $s['labelY'] . '" text-anchor="' . $s['anchor'] . '" dominant-baseline="middle" font-size="9" fill="#6b7280" font-family="' . $fontFamily . '">' . htmlspecialchars($s['label']) . ' (' . $s['pct'] . '%)</text>';
            }, $slices)) . '
        </svg>
    ') }}" width="{{ $size + 80 }}" height="{{ $size + 80 }}" style="max-width: 100%;" />
</div>
