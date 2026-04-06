{{-- SVG Radar Chart for PDF export --}}
{{-- Usage: @include('reports.partials.radar-chart', ['categories' => $categories, 'locale' => $locale]) --}}
@php
    $categories = $categories ?? [];
    $locale = $locale ?? 'en';
    $count = count($categories);
    if ($count < 3) return;

    $size = 300;
    $cx = $size / 2;
    $cy = $size / 2;
    $radius = 110;
    $levels = 5;

    // Calculate points for each category on the radar
    $angleStep = (2 * M_PI) / $count;
    $startAngle = -M_PI / 2; // Start from top

    $dataPoints = [];
    $labelPoints = [];
    $gridLevels = [];

    // Generate grid polygons
    for ($level = 1; $level <= $levels; $level++) {
        $r = ($radius / $levels) * $level;
        $points = [];
        for ($i = 0; $i < $count; $i++) {
            $angle = $startAngle + ($i * $angleStep);
            $x = $cx + $r * cos($angle);
            $y = $cy + $r * sin($angle);
            $points[] = round($x, 1) . ',' . round($y, 1);
        }
        $gridLevels[] = implode(' ', $points);
    }

    // Generate axis lines and labels
    for ($i = 0; $i < $count; $i++) {
        $angle = $startAngle + ($i * $angleStep);
        $cat = $categories[$i];

        // Axis endpoint
        $axisX = $cx + $radius * cos($angle);
        $axisY = $cy + $radius * sin($angle);

        // Data point (based on percentage)
        $pct = min(100, max(0, $cat['score_percentage'] ?? 0));
        $r = ($radius / 100) * $pct;
        $dataX = $cx + $r * cos($angle);
        $dataY = $cy + $r * sin($angle);
        $dataPoints[] = round($dataX, 1) . ',' . round($dataY, 1);

        // Label position (slightly outside)
        $labelR = $radius + 20;
        $labelX = $cx + $labelR * cos($angle);
        $labelY = $cy + $labelR * sin($angle);

        $label = $cat['label'] ?? $cat['key'] ?? '';
        if (is_array($label)) {
            $label = $label[$locale] ?? $label['en'] ?? '';
        }

        $labelPoints[] = [
            'x' => round($labelX, 1),
            'y' => round($labelY, 1),
            'axisX' => round($axisX, 1),
            'axisY' => round($axisY, 1),
            'label' => $label,
            'pct' => round($pct),
            'angle' => $angle,
        ];
    }

    $dataPolygon = implode(' ', $dataPoints);
@endphp

<div style="text-align: center; margin: 15px 0;">
    <svg width="{{ $size + 80 }}" height="{{ $size + 80 }}" viewBox="-40 -40 {{ $size + 80 }} {{ $size + 80 }}" xmlns="http://www.w3.org/2000/svg">
        {{-- Grid polygons --}}
        @foreach ($gridLevels as $index => $points)
            <polygon
                points="{{ $points }}"
                fill="none"
                stroke="#e5e7eb"
                stroke-width="{{ $index === count($gridLevels) - 1 ? '1.5' : '0.8' }}"
            />
        @endforeach

        {{-- Axis lines --}}
        @foreach ($labelPoints as $lp)
            <line
                x1="{{ $cx }}" y1="{{ $cy }}"
                x2="{{ $lp['axisX'] }}" y2="{{ $lp['axisY'] }}"
                stroke="#e5e7eb" stroke-width="0.8"
            />
        @endforeach

        {{-- Data polygon --}}
        <polygon
            points="{{ $dataPolygon }}"
            fill="rgba(59, 130, 246, 0.2)"
            stroke="#3b82f6"
            stroke-width="2"
        />

        {{-- Data points --}}
        @foreach ($dataPoints as $point)
            @php [$px, $py] = explode(',', $point); @endphp
            <circle cx="{{ $px }}" cy="{{ $py }}" r="3.5" fill="#3b82f6" />
        @endforeach

        {{-- Labels --}}
        @foreach ($labelPoints as $lp)
            @php
                $anchor = 'middle';
                $angleDeg = rad2deg($lp['angle']);
                if ($angleDeg > -80 && $angleDeg < 80) $anchor = 'start';
                elseif ($angleDeg > 100 || $angleDeg < -100) $anchor = 'end';
            @endphp
            <text
                x="{{ $lp['x'] }}" y="{{ $lp['y'] }}"
                text-anchor="{{ $anchor }}"
                dominant-baseline="middle"
                font-size="9" fill="#6b7280"
            >{{ $lp['label'] }} ({{ $lp['pct'] }}%)</text>
        @endforeach
    </svg>
</div>
