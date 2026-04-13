<!DOCTYPE html>
@php
    $isAr = $dir === 'rtl';
    $lang = $isAr ? 'ar' : 'en';
    $arSvc = app(\App\Services\ArabicTextService::class);
    // Helper to shape text for PDF rendering
    $s = fn(?string $text) => $arSvc->shape($text ?? '');
@endphp
<html dir="{{ $dir }}" lang="{{ $lang }}">
<head>
    <meta charset="UTF-8">
    @include('reports.partials.styles')
    <style>
        .category-bar-container { width: 100%; margin-bottom: 8px; }
        .category-bar-label { font-size: 10px; font-weight: bold; color: #374151; margin-bottom: 2px; }
        .category-bar-track { width: 100%; height: 18px; background: #e5e7eb; border-radius: 3px; position: relative; }
        .category-bar-fill { height: 18px; background: #2563eb; border-radius: 3px; display: inline-block; }
        .category-bar-value { font-size: 9px; color: #666; margin-top: 1px; }
        .range-indicator { text-align: center; padding: 15px; border: 2px solid #2563eb; border-radius: 8px; margin: 10px 0; }
        .range-label { font-size: 16px; font-weight: bold; color: #2563eb; }
        .range-description { font-size: 11px; color: #555; margin-top: 4px; }
    </style>
</head>
<body>
    @include('reports.partials.header', [
        'title' => $s($isAr ? 'تقرير الملف النفسي' : 'Psycho-Profile Report'),
        'subtitle' => $s($accountName),
    ])

    <div class="section-title">{{ $s($isAr ? 'معلومات المشارك' : 'Participant Information') }}</div>
    <table>
        <tbody>
            <tr><th style="width: 150px;">{{ $s($isAr ? 'الاسم' : 'Name') }}</th><td>{{ $s($accountName) }}</td></tr>
            <tr><th>{{ $s($isAr ? 'البريد الإلكتروني' : 'Email') }}</th><td>{{ $accountEmail }}</td></tr>
            @if($accountPhone)<tr><th>{{ $s($isAr ? 'الهاتف' : 'Phone') }}</th><td>{{ $accountPhone }}</td></tr>@endif
            @if($accountCompany)<tr><th>{{ $s($isAr ? 'الشركة' : 'Company') }}</th><td>{{ $s($accountCompany) }}</td></tr>@endif
            @if($accountJobTitle)<tr><th>{{ $s($isAr ? 'المسمى الوظيفي' : 'Job Title') }}</th><td>{{ $s($accountJobTitle) }}</td></tr>@endif
            @if($accountAge)<tr><th>{{ $s($isAr ? 'العمر' : 'Age') }}</th><td>{{ $accountAge }}</td></tr>@endif
            @if($accountGender)<tr><th>{{ $s($isAr ? 'الجنس' : 'Gender') }}</th><td>{{ $s(ucfirst($accountGender)) }}</td></tr>@endif
        </tbody>
    </table>

    @foreach($assessments as $index => $entry)
        @if($index > 0)
            <div class="page-break"></div>
        @endif

        <div class="section-title">{{ $s($entry['assessment_title']) }}</div>

        @if(count($entry['attempts']) === 0)
            <p style="color: #888; font-style: italic;">{{ $s($isAr ? 'لا توجد اختبارات مكتملة.' : 'No completed tests.') }}</p>
        @endif

        {{-- Score overview line chart (when 2+ tests completed) --}}
        @if(count($entry['attempts']) >= 2)
            @include('reports.partials.score-line-chart', ['attempts' => collect($entry['attempts']), 'locale' => $lang])
            <div class="page-break"></div>
        @endif

        @foreach($entry['attempts'] as $attemptIdx => $attempt)
            @if($attemptIdx > 0)
                <div class="page-break"></div>
            @endif

            <p style="margin: 10px 0 6px; font-weight: bold; font-size: 14px; color: #1e40af;">
                {{ $s($attempt->test->getTranslation('title')) }}
            </p>

            @php
                $details = $attempt->score_details;
                $scoringType = $details['type'] ?? 'simple';
            @endphp

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $attempt->score_percentage }}%</div>
                    <div class="stat-label">{{ $s($isAr ? 'النتيجة' : 'Score') }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $attempt->score_raw }} / {{ $attempt->score_max }}</div>
                    <div class="stat-label">{{ $s($isAr ? 'الدرجة الخام' : 'Raw Score') }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ gmdate('H:i:s', $attempt->time_spent_seconds ?? 0) }}</div>
                    <div class="stat-label">{{ $s($isAr ? 'الوقت المستغرق' : 'Time Spent') }}</div>
                </div>
            </div>

            {{-- Category Scoring --}}
            @if($scoringType === 'category' && !empty($details['categories']))
                @php
                    $chartType = $attempt->test->chart_type ?? 'bar';
                @endphp

                <p style="font-weight: bold; font-size: 11px; margin: 10px 0 6px; color: #1e40af;">
                    {{ $s($isAr ? 'تفصيل الأبعاد' : 'Category Breakdown') }}
                </p>

                @if($chartType === 'pie')
                    @include('reports.partials.pie-chart', ['categories' => $details['categories'], 'locale' => $lang])
                @elseif($chartType === 'column')
                    @include('reports.partials.column-chart', ['categories' => $details['categories'], 'locale' => $lang])
                @elseif($chartType === 'line')
                    @include('reports.partials.line-chart', ['categories' => $details['categories'], 'locale' => $lang])
                @elseif($chartType === 'doughnut')
                    @include('reports.partials.doughnut-chart', ['categories' => $details['categories'], 'locale' => $lang])
                @else
                    {{-- Default: bar chart (CSS progress bars) --}}
                    @foreach($details['categories'] as $cat)
                        @php
                            $catLabel = is_array($cat['label'] ?? null) ? ($cat['label'][$lang] ?? $cat['key']) : ($cat['label'] ?? $cat['key']);
                            $interpLabel = '';
                            if (!empty($cat['interpretation'])) {
                                $interpLabel = is_array($cat['interpretation']) ? ($cat['interpretation'][$lang] ?? '') : $cat['interpretation'];
                            }
                        @endphp
                        <div class="category-bar-container">
                            <div class="category-bar-label">{{ $s($catLabel) }}</div>
                            <div class="category-bar-track">
                                <div class="category-bar-fill" style="width: {{ min(100, max(0, $cat['score_percentage'])) }}%;"></div>
                            </div>
                            <div class="category-bar-value">
                                {{ $cat['score_percentage'] }}% ({{ $cat['score_raw'] }}/{{ $cat['score_max'] }})
                                @if($interpLabel)
                                    — <strong>{{ $s($interpLabel) }}</strong>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Text detail list for all chart types --}}
                @if($chartType !== 'bar')
                    @foreach($details['categories'] as $cat)
                        @php
                            $catLabel = is_array($cat['label'] ?? null) ? ($cat['label'][$lang] ?? $cat['key']) : ($cat['label'] ?? $cat['key']);
                            $interpLabel = '';
                            if (!empty($cat['interpretation'])) {
                                $interpLabel = is_array($cat['interpretation']) ? ($cat['interpretation'][$lang] ?? '') : $cat['interpretation'];
                            }
                        @endphp
                        <div class="category-bar-value" style="margin: 2px 0;">
                            <strong>{{ $s($catLabel) }}</strong>:
                            {{ $cat['score_percentage'] }}% ({{ $cat['score_raw'] }}/{{ $cat['score_max'] }})
                            @if($interpLabel)
                                — {{ $s($interpLabel) }}
                            @endif
                        </div>
                    @endforeach
                @endif

            @endif

            {{-- Range Scoring --}}
            @if($scoringType === 'range' && !empty($details['matched_range']))
                @php
                    $rangeLabel = is_array($details['matched_range']['label'] ?? null)
                        ? ($details['matched_range']['label'][$lang] ?? '') : ($details['matched_range']['label'] ?? '');
                    $rangeDesc = is_array($details['matched_range']['description'] ?? null)
                        ? ($details['matched_range']['description'][$lang] ?? '') : ($details['matched_range']['description'] ?? '');
                @endphp
                <div class="range-indicator">
                    <div class="range-label">{{ $s($rangeLabel) }}</div>
                    @if($rangeDesc)
                        <div class="range-description">{{ $s($rangeDesc) }}</div>
                    @endif
                    <div style="font-size: 9px; color: #888; margin-top: 4px;">
                        {{ $s($isAr ? 'المدى' : 'Range') }}: {{ $details['matched_range']['min'] }} - {{ $details['matched_range']['max'] }}
                    </div>
                </div>

            @endif
        @endforeach
    @endforeach

    <div class="footer">{{ $s($isAr ? 'إدراك - منصة تقييم علم النفس التجاري' : 'Edrak - Business Psychology Assessment Platform') }}</div>
</body>
</html>
