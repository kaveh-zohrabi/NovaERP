<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to NovaERP</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa; margin: 0; padding: 40px 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #4F46E5, #7C3AED); padding: 40px 32px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); margin: 8px 0 0; font-size: 14px; }
        .body { padding: 40px 32px; color: #374151; line-height: 1.7; }
        .body h2 { font-size: 20px; color: #111827; margin: 0 0 16px; }
        .body p { margin: 0 0 16px; font-size: 15px; }
        .credentials { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 24px 0; }
        .credentials p { margin: 4px 0; font-size: 14px; }
        .credentials strong { color: #374151; }
        .btn { display: inline-block; background: #4F46E5; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: 600; font-size: 15px; margin: 8px 0; }
        .btn:hover { background: #4338CA; }
        .footer { background: #f9fafb; padding: 24px 32px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { margin: 0; font-size: 13px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to NovaERP</h1>
            <p>Enterprise Resource Planning System</p>
        </div>

        <div class="body">
            <h2>Hello, {{ $user->name }}!</h2>

            <p>Your account has been created and you now have access to NovaERP. Below are your login credentials:</p>

            <div class="credentials">
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Password:</strong> Please check with your administrator for your initial password.</p>
            </div>

            <p>You can start by logging in and exploring the dashboard. If you have any questions, contact your system administrator.</p>

            <p style="text-align: center; margin-top: 32px;">
                <a href="{{ url('/login') }}" class="btn">Log in to NovaERP</a>
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} NovaERP. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
