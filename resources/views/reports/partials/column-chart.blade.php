{{-- SVG Column (vertical bar) Chart for PDF export --}}
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

    $barGap = 8;
    $barW = min(40, ($plotW - ($count - 1) * $barGap) / $count);

    $colors = ['#22c55e', '#f59e0b', '#ef4444'];

    $bars = [];
    for ($i = 0; $i < $count; $i++) {
        $cat = $categories[$i];
        $pct = min(100, max(0, $cat['score_percentage'] ?? 0));
        $barH = ($pct / 100) * $plotH;

        $x = $padL + ($plotW / $count) * $i + ($plotW / $count - $barW) / 2;
        $y = $padT + $plotH - $barH;

        $label = $cat['label'] ?? $cat['key'] ?? '';
        if (is_array($label)) {
            $label = $label[$locale] ?? $label['en'] ?? '';
        }
        $label = $s($label);

        $color = $pct >= 70 ? $colors[0] : ($pct >= 40 ? $colors[1] : $colors[2]);

        $bars[] = [
            'x' => round($x, 1),
            'y' => round($y, 1),
            'w' => round($barW, 1),
            'h' => round($barH, 1),
            'pct' => round($pct),
            'label' => $label,
            'labelX' => round($x + $barW / 2, 1),
            'color' => $color,
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

            ' . implode('', array_map(function($b) {
                return '<rect x="' . $b['x'] . '" y="' . $b['y'] . '" width="' . $b['w'] . '" height="' . $b['h'] . '" fill="' . $b['color'] . '" rx="2"/>
                        <text x="' . $b['labelX'] . '" y="' . ($b['y'] - 5) . '" text-anchor="middle" font-size="8" font-weight="bold" fill="#374151" font-family="sans-serif">' . $b['pct'] . '%</text>';
            }, $bars)) . '

            ' . implode('', array_map(function($b) use ($padT, $plotH) {
                $ty = $padT + $plotH + 8;
                return '<text x="' . $b['labelX'] . '" y="' . $ty . '" text-anchor="end" font-size="7" fill="#6b7280" font-family="DejaVu Sans, sans-serif" transform="rotate(-55, ' . $b['labelX'] . ', ' . $ty . ')">' . htmlspecialchars($b['label']) . '</text>';
            }, $bars)) . '
        </svg>
    ') }}" width="{{ $totalW }}" height="{{ $totalH }}" style="max-width: 100%;" />
</div>
