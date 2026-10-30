{{-- SVG Radar Chart for PDF export --}}
{{-- Usage: @include('reports.partials.radar-chart', ['categories' => $categories, 'locale' => $locale]) --}}
@php
    $categories = $categories ?? [];
    $locale = $locale ?? 'en';
    $arSvc = app(\App\Services\ArabicTextService::class);
    $s = fn(?string $text) => $arSvc->shape($text ?? '');
    $count = count($categories);
    if ($count < 1) return;

    $totalW = 520;
    $totalH = 400;
    $cx = $totalW / 2;
    $cy = $totalH / 2 + 5;
    $radius = 125;
    $angleStep = (2 * M_PI) / max(3, $count);
    $startAngle = -M_PI / 2; // first axis points straight up

    $point = fn (float $r, int $i) => [
        round($cx + $r * cos($startAngle + $i * $angleStep), 1),
        round($cy + $r * sin($startAngle + $i * $angleStep), 1),
    ];

    // Wrap a label into lines of ~22 chars on the logical (unshaped) text, then shape each line,
    // so Arabic lines keep their word order after shaping.
    $wrap = function (string $text): array {
        $lines = [];
        $line = '';
        foreach (preg_split('/\s+/u', trim($text)) as $word) {
            if ($line !== '' && mb_strlen($line . ' ' . $word) > 22) {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $line === '' ? $word : $line . ' ' . $word;
            }
        }
        if ($line !== '') $lines[] = $line;
        return array_slice($lines, 0, 3);
    };

    $grid = '';
    foreach ([20, 40, 60, 80, 100] as $level) {
        $pts = [];
        for ($i = 0; $i < $count; $i++) {
            $pts[] = implode(',', $point($radius * $level / 100, $i));
        }
        $grid .= '<polygon points="' . implode(' ', $pts) . '" fill="none" stroke="#e5e7eb" stroke-width="' . ($level === 100 ? '1.2' : '0.8') . '"/>';
        $grid .= '<text x="' . ($cx + 3) . '" y="' . round($cy - $radius * $level / 100 + 3, 1) . '" font-size="7" fill="#9ca3af" font-family="sans-serif">' . $level . '%</text>';
    }

    $axes = '';
    $data = [];
    $dots = '';
    $labels = '';
    for ($i = 0; $i < $count; $i++) {
        $cat = $categories[$i];
        $pct = min(100, max(0, $cat['score_percentage'] ?? 0));

        [$ax, $ay] = $point($radius, $i);
        $axes .= '<line x1="' . $cx . '" y1="' . $cy . '" x2="' . $ax . '" y2="' . $ay . '" stroke="#e5e7eb" stroke-width="0.8"/>';

        [$dx, $dy] = $point($radius * $pct / 100, $i);
        $data[] = $dx . ',' . $dy;
        $dots .= '<circle cx="' . $dx . '" cy="' . $dy . '" r="3" fill="#2563eb"/>';

        $label = $cat['label'] ?? $cat['key'] ?? '';
        if (is_array($label)) {
            $label = $label[$locale] ?? $label['en'] ?? '';
        }
        $lines = $wrap($label);
        $lines[] = round($pct) . '%';

        // Anchor by which side of the chart the axis points to.
        $cos = cos($startAngle + $i * $angleStep);
        $sin = sin($startAngle + $i * $angleStep);
        $anchor = $cos > 0.25 ? 'start' : ($cos < -0.25 ? 'end' : 'middle');
        [$lx, $ly] = $point($radius + 12, $i);
        $lineH = 10;
        $blockH = count($lines) * $lineH;
        // Top labels grow upward, bottom labels downward, side labels are vertically centred.
        $y0 = $sin < -0.5 ? $ly - $blockH + 6 : ($sin > 0.5 ? $ly + 8 : $ly - $blockH / 2 + 7);

        foreach ($lines as $li => $text) {
            $isPct = $li === count($lines) - 1;
            $labels .= '<text x="' . $lx . '" y="' . round($y0 + $li * $lineH, 1) . '" text-anchor="' . $anchor . '" font-size="' . ($isPct ? '8' : '8') . '"'
                . ($isPct ? ' font-weight="bold" fill="#1e40af"' : ' fill="#374151"')
                . ' font-family="DejaVu Sans, sans-serif">' . htmlspecialchars($isPct ? $text : $s($text)) . '</text>';
        }
    }

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $totalW . '" height="' . $totalH . '" viewBox="0 0 ' . $totalW . ' ' . $totalH . '">'
        . '<rect x="0" y="0" width="' . $totalW . '" height="' . $totalH . '" fill="#ffffff"/>'
        . $grid . $axes
        . '<polygon points="' . implode(' ', $data) . '" fill="#3b82f6" fill-opacity="0.22" stroke="#2563eb" stroke-width="2"/>'
        . $dots . $labels
        . '</svg>';
@endphp

<div style="text-align: center; margin: 15px 0;">
    <img src="data:image/svg+xml;base64,{{ base64_encode($svg) }}" width="{{ $totalW }}" height="{{ $totalH }}" style="max-width: 100%;" />
</div>
