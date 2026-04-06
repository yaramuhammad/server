<!DOCTYPE html>
<html dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    @include('reports.partials.styles')
</head>
<body>
    @include('reports.partials.header', [
        'title' => $attempt->test->getTranslation('title'),
        'subtitle' => 'Individual Attempt Report',
    ])

    <div class="section-title">Participant Information</div>
    <table>
        <tbody>
            <tr><th>Name</th><td>{{ $participant->name ?? '—' }}</td></tr>
            <tr><th>Email</th><td>{{ $participant->email ?? '—' }}</td></tr>
            <tr><th>Department</th><td>{{ $participant->department ?? '—' }}</td></tr>
            <tr><th>Age</th><td>{{ $participant->age ?? '—' }}</td></tr>
            <tr><th>Gender</th><td>{{ $participant->gender ?? '—' }}</td></tr>
        </tbody>
    </table>

    <div class="section-title">Score Summary</div>
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $attempt->score_percentage }}%</div>
            <div class="stat-label">Score Percentage</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $attempt->score_raw }} / {{ $attempt->score_max }}</div>
            <div class="stat-label">Raw Score</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ gmdate('H:i:s', $attempt->time_spent_seconds ?? 0) }}</div>
            <div class="stat-label">Time Spent</div>
        </div>
    </div>

    <div class="section-title">Responses</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Question</th>
                <th>Response</th>
                <th>Scored</th>
                <th>Reverse</th>
            </tr>
        </thead>
        <tbody>
            @foreach($responses as $response)
                <tr>
                    <td>{{ $response->question->sort_order }}</td>
                    <td>{{ $response->question->getTranslation('text') }}</td>
                    <td>{{ $response->value }}</td>
                    <td>{{ $response->scored_value }}</td>
                    <td>{{ $response->question->is_reverse_scored ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Edrak - Business Psychology Assessment Platform</div>
</body>
</html>
