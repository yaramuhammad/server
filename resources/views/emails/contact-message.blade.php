<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contact form message</title>
</head>
<body style="font-family: Arial, sans-serif; color:#0f172a; line-height:1.6;">
    <h2 style="margin-bottom:16px;">New contact form submission</h2>
    <table cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
        <tr><td><strong>Name:</strong></td><td>{{ $senderName }}</td></tr>
        <tr><td><strong>Email:</strong></td><td>{{ $senderEmail }}</td></tr>
        @if($senderPhone)
            <tr><td><strong>Phone:</strong></td><td>{{ $senderPhone }}</td></tr>
        @endif
        @if($senderSubject)
            <tr><td><strong>Subject:</strong></td><td>{{ $senderSubject }}</td></tr>
        @endif
    </table>
    <hr style="margin:20px 0; border:none; border-top:1px solid #e2e8f0;">
    <div style="white-space:pre-wrap;">{{ $messageBody }}</div>
</body>
</html>
