<!DOCTYPE html>
<html dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    @include('reports.partials.styles')
</head>
<body>
    @include('reports.partials.header', [
        'title' => $assessment->getTranslation('title'),
        'subtitle' => 'Assessment Summary Report',
    ])

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $participants->count() }}</div>
            <div class="stat-label">Total Participants</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $tests->count() }}</div>
            <div class="stat-label">Tests</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $completedCount }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>

    <div class="section-title">Participant Results</div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Company</th>
                <th>Job Title</th>
                @foreach($tests as $test)
                    <th>{{ $test->getTranslation('title') }} (%)</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $participant)
                <tr>
                    <td>{{ $participant->name ?? '—' }}</td>
                    <td>{{ $participant->email ?? '—' }}</td>
                    <td>{{ $participant->company ?? '—' }}</td>
                    <td>{{ $participant->job_title ?? '—' }}</td>
                    @php $attemptsByTest = $participant->attempts->keyBy('test_id'); @endphp
                    @foreach($tests as $test)
                        @php $attempt = $attemptsByTest->get($test->id); @endphp
                        <td>{{ $attempt?->score_percentage ?? '—' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Edrak - Business Psychology Assessment Platform</div>
</body>
</html>
