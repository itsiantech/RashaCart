<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>@yield('title', setting('app_name'))</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            margin: 0; 
            padding: 0; 
            background-color: #f4f4f4;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background: #ffffff; 
            padding: 20px; 
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header { 
            text-align: center; 
            border-bottom: 2px solid #f0f0f0; 
            padding-bottom: 20px; 
            margin-bottom: 20px;
        }
        .logo { 
            max-width: 150px; 
            max-height: 50px; 
            margin-bottom: 15px;
        }
        .footer { 
            text-align: center; 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 1px solid #f0f0f0; 
            color: #666; 
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with Logo -->
        <div class="header">
            <img src="{{ asset('storage/logo/' . setting('app_logo')) }}" alt="{{ setting('app_name') }}" class="logo">
            @yield('header')
        </div>

        <!-- Main Content -->
        <div class="content">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0;">Thank you for choosing <strong>{{ setting('app_name') }}</strong>!</p>
            <p style="margin: 5px 0 0 0;">
                &copy; {{ date('Y') }} {{ setting('app_name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>