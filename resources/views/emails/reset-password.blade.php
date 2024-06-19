<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333333;
            text-align: center;
            margin-bottom: 20px;
        }
        p {
            color: #555555;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 5px;
            text-align: center;
        }
        .footer {
            text-align: center;
            color: #888888;
            margin-top: 20px;
        }

        @media screen and (max-width: 600px) {
            .container {
                padding: 10px;
            }
            .button {
                display: block;
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Reset Password</h1>
    <p>Click the button below to reset your password:</p>
    <p><a class="button" href="{{ url('/api/reset-password/'.$token) }}">Reset Password</a></p>
    <p>If you did not request a password reset, no further action is required.</p>
    <p class="footer">Thanks, {{ config('app.name') }}</p>
</div>
</body>
</html>
