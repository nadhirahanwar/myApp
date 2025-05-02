<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Login Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 30px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 6px;">
        <h2 style="color: #333;">Hello!</h2>
        <p style="font-size: 16px;">You recently tried to log in. Please use the code below to verify your identity:</p>
        <div style="text-align: center; margin: 30px 0;">
            <span style="font-size: 32px; font-weight: bold; color: #2d3748;">{{ $code }}</span>
        </div>
        <p style="font-size: 14px; color: #666;">
            This code will expire in 10 minutes. If you did not try to sign in, you can ignore this email.
        </p>
        <p style="font-size: 14px; color: #666;">Thank you,<br>Your App Team</p>
    </div>
</body>
</html>
