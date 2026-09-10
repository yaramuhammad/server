<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset your password</title>
</head>
<body style="font-family: Arial, sans-serif; color:#0f172a; line-height:1.6;">
    <h2 style="margin-bottom:16px;">Reset your password</h2>

    <p>Hello {{ $recipientName }},</p>

    <p>
        We received a request to reset the password for your Edrak account.
        Click the button below to choose a new password. This link is valid for
        {{ $expiresInMinutes }} minutes.
    </p>

    <p style="margin:24px 0;">
        <a href="{{ $resetUrl }}"
           style="background:#0f172a; color:#ffffff; padding:12px 20px; border-radius:6px; text-decoration:none; display:inline-block;">
            Reset password
        </a>
    </p>

    <p style="font-size:13px; color:#475569;">
        If the button doesn't work, copy and paste this URL into your browser:<br>
        <span style="word-break:break-all;">{{ $resetUrl }}</span>
    </p>

    <hr style="margin:20px 0; border:none; border-top:1px solid #e2e8f0;">

    <p style="font-size:13px; color:#475569;">
        If you didn't request a password reset, you can safely ignore this email &mdash;
        your password will not be changed.
    </p>
</body>
</html>
