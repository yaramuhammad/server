{{-- SVG Line Chart for PDF export — shows category scores as connected line --}}
@php
    $categories = $categories ?? [];
    $locale = $locale ?? 'en';
    $isAr = $locale === 'ar';
    $arSvc = app(\App\Services\ArabicTextService::class);
    $s = fn(?string $text) => $arSvc->shape($text ?? '');
    $count = count($categories);
    if ($count < 1) return;

    $totalW = 520;
    $totalH = 350;
    $padL = 35;
    $padR = 15;
    $padT = 25;
    $padB = 100;
    $plotW = $totalW - $padL - $padR;
    $plotH = $totalH - $padT - $padB;

    $points = [];
    for ($i = 0; $i < $count; $i++) {
        $cat = $categories[$i];
        $pct = min(100, max(0, $cat['score_percentage'] ?? 0));
        $x = $padL + ($count > 1 ? ($i / ($count - 1)) * $plotW : $plotW / 2);
        $y = $padT + $plotH - (($pct / 100) * $plotH);

        $label = $cat['label'] ?? $cat['key'] ?? '';
        if (is_array($label)) {
            $label = $label[$locale] ?? $label['en'] ?? '';
        }
        $label = $s($label);

        $points[] = [
            'x' => round($x, 1),
            'y' => round($y, 1),
            'pct' => round($pct),
            'label' => $label,
        ];
    }
@endphp

<div style="text-align: center; margin: 15px 0;">
    <img src="data:image/svg+xml;base64,{{ base64_encode('
        <svg xmlns="http://www.w3.org/2000/svg" width="' . $totalW . '" height="' . $totalH . '" viewBox="0 0 ' . $totalW . ' ' . $totalH . '">
            <rect x="0" y="0" width="' . $totalW . '" height="' . $totalH . '" fill="#ffffff"/>

            ' . implode('', array_map(function($val) use ($padL, $padT, $plotW, $plotH) {
                $gy = round($padT + $plotH - (($val / 100) * $plotH), 1);
                return '<line x1="' . $padL . '" y1="' . $gy . '" x2="' . ($padL + $plotW) . '" y2="' . $gy . '" stroke="#e5e7eb" stroke-width="0.8"/>
                        <text x="' . ($padL - 4) . '" y="' . ($gy + 3) . '" text-anchor="end" font-size="8" fill="#9ca3af" font-family="sans-serif">' . $val . '%</text>';
            }, [0, 25, 50, 75, 100])) . '

            <line x1="' . $padL . '" y1="' . ($padT + $plotH) . '" x2="' . ($padL + $plotW) . '" y2="' . ($padT + $plotH) . '" stroke="#d1d5db" stroke-width="1"/>

            <line x1="' . $padL . '" y1="' . round($padT + $plotH - (0.5 * $plotH), 1) . '" x2="' . ($padL + $plotW) . '" y2="' . round($padT + $plotH - (0.5 * $plotH), 1) . '" stroke="#dc2626" stroke-width="1" stroke-dasharray="6,3"/>

            ' . implode('', array_map(function($i) use ($points) {
                if ($i === 0) return '';
                $p1 = $points[$i - 1];
                $p2 = $points[$i];
                return '<line x1="' . $p1['x'] . '" y1="' . $p1['y'] . '" x2="' . $p2['x'] . '" y2="' . $p2['y'] . '" stroke="#3b82f6" stroke-width="2.5"/>';
            }, range(0, count($points) - 1))) . '

            ' . implode('', array_map(function($p) {
                return '<circle cx="' . $p['x'] . '" cy="' . $p['y'] . '" r="4" fill="#3b82f6"/>
                        <text x="' . $p['x'] . '" y="' . ($p['y'] - 8) . '" text-anchor="middle" font-size="8" font-weight="bold" fill="#1e40af" font-family="sans-serif">' . $p['pct'] . '%</text>';
            }, $points)) . '

            ' . implode('', array_map(function($p) use ($padT, $plotH) {
                $ty = $padT + $plotH + 8;
                return '<text x="' . $p['x'] . '" y="' . $ty . '" text-anchor="end" font-size="7" fill="#6b7280" font-family="DejaVu Sans, sans-serif" transform="rotate(-55, ' . $p['x'] . ', ' . $ty . ')">' . htmlspecialchars($p['label']) . '</text>';
            }, $points)) . '
        </svg>
    ') }}" width="{{ $totalW }}" height="{{ $totalH }}" style="max-width: 100%;" />
</div>
