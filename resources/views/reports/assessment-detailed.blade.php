<!DOCTYPE html>
<html dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    @include('reports.partials.styles')
</head>
<body>
    @include('reports.partials.header', [
        'title' => $assessment->getTranslation('title'),
        'subtitle' => 'Detailed Results Report',
    ])

    @foreach($participants as $index => $participant)
        @if($index > 0)
            <div class="page-break"></div>
        @endif

        <div class="section-title">{{ $participant->name ?? 'Anonymous' }} ({{ $participant->email ?? '—' }})</div>

        @foreach($participant->attempts as $attempt)
            <p style="margin: 8px 0 4px;"><strong>{{ $attempt->test->getTranslation('title') }}</strong> — Score: {{ $attempt->score_percentage }}%</p>
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
                    @foreach($attempt->responses->sortBy('question.sort_order') as $response)
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
        @endforeach
    @endforeach

    <div class="footer">Edrak - Business Psychology Assessment Platform</div>
</body>
</html>
