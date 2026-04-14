{{-- Line chart for PDF export — shows score % across all tests --}}
{{-- Uses only basic SVG elements supported by DomPDF: rect, circle, line, text --}}
@php
    $attempts = $attempts ?? collect();
    $locale = $locale ?? 'en';
    $isAr = $locale === 'ar';
    $arSvc = app(\App\Services\ArabicTextService::class);
    $s = fn(?string $text) => $arSvc->shape($text ?? '');

    $count = $attempts->count();
    if ($count < 2) return;

    // Chart dimensions
    $totalW = 520;
    $totalH = 400;
    $padL = 35;
    $padR = 15;
    $padT = 20;
    $padB = 220;
    $plotW = $totalW - $padL - $padR;
    $plotH = $totalH - $padT - $padB;

    // Build data — expand Big Five categories into individual points
    $bigFiveKeys = ['extraversion', 'neuroticism', 'openness', 'agreeableness', 'conscientiousness'];
    $dataPoints = [];
    foreach ($attempts->values() as $attempt) {
        $details = $attempt->score_details;
        $cats = $details['categories'] ?? [];
        $catKeys = array_column($cats, 'key');
        $isBigFive = ($details['type'] ?? '') === 'category'
            && count($cats) === 5
            && count(array_diff($catKeys, $bigFiveKeys)) === 0;

        if ($isBigFive) {
            $testTitle = $attempt->test->getTranslation('title', $locale);
            foreach ($cats as $cat) {
                $catLabel = is_array($cat['label'] ?? null) ? ($cat['label'][$locale] ?? $cat['key']) : ($cat['label'] ?? $cat['key']);
                $combined = $testTitle ? "{$testTitle} — {$catLabel}" : $catLabel;
                $dataPoints[] = ['label' => $s($combined), 'pct' => round($cat['score_percentage'] ?? 0, 1)];
            }
        } else {
            $dataPoints[] = ['label' => $s($attempt->test->getTranslation('title', $locale)), 'pct' => round($attempt->score_percentage ?? 0, 1)];
        }
    }

    $count = count($dataPoints);
    if ($count < 2) return;

    $points = [];
    foreach ($dataPoints as $i => $dp) {
        $x = $padL + ($count > 1 ? ($i / ($count - 1)) * $plotW : $plotW / 2);
        $y = $padT + $plotH - (($dp['pct'] / 100) * $plotH);
        $points[] = [
            'x' => round($x, 1),
            'y' => round($y, 1),
            'pct' => $dp['pct'],
            'label' => $dp['label'],
        ];
    }
@endphp

<div style="text-align: center; margin: 15px 0; page-break-inside: avoid;">
    <p style="font-weight: bold; font-size: 11px; margin-bottom: 6px; color: #1e40af;">
        {{ $s($isAr ? 'نظرة عامة على الدرجات' : 'Score Overview') }}
    </p>

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

            ' . implode('', array_map(function($p, $i) use ($padT, $plotH) {
                $ty = $padT + $plotH + 8;
                return '<text x="' . $p['x'] . '" y="' . $ty . '" text-anchor="end" font-size="7" fill="#6b7280" font-family="sans-serif" transform="rotate(-70, ' . $p['x'] . ', ' . $ty . ')">' . htmlspecialchars($p['label']) . '</text>';
            }, $points, array_keys($points))) . '
        </svg>
    ') }}" width="{{ $totalW }}" height="{{ $totalH }}" style="max-width: 100%;" />
</div>
