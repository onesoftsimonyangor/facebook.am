<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Successful</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f0f2f5;
        }

        .message-box {
            background: #fff;
            padding: 40px 60px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .message-box h1 {
            font-size: 2.5rem;
            color: #1877f2;
            margin-bottom: 20px;
        }

        .message-box p {
            font-size: 1.25rem;
            color: #333;
            margin-bottom: 30px;
        }

        .message-box a {
            display: inline-block;
            background: #1877f2;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1rem;
            transition: background 0.2s ease;
        }

        .message-box a:hover {
            background: #0d65d9;
        }
    </style>
</head>
<body>
<div class="message-box">
    <h1>Password Reset Successful</h1>
    <p>Your password has been reset successfully. You can now log in with your new password.</p>
    <a href="{{ redirect('login') }}">Go to Login</a>
</div>
</body>
</html>
