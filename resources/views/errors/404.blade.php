<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #6F112D 0%, #8B1538 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            text-align: center;
            background: white;
            padding: 60px 40px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .error-code {
            font-size: 120px;
            font-weight: 800;
            background: linear-gradient(135deg, #6F112D 0%, #8B1538 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 20px;
        }

        .error-title {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 16px;
        }

        .error-description {
            font-size: 16px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6F112D 0%, #8B1538 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(111, 17, 45, 0.4);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #1f2937;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 32px;
        }

        @media (max-width: 640px) {
            .container {
                padding: 40px 24px;
            }

            .error-code {
                font-size: 80px;
            }

            .error-title {
                font-size: 24px;
            }

            .error-description {
                font-size: 14px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M21.21 15.89C20.5738 17.3945 19.5788 18.7202 18.3119 19.7513C17.045 20.7824 15.5447 21.4874 13.9424 21.8048C12.3401 22.1221 10.6843 22.0421 9.12006 21.5718C7.55578 21.1015 6.13054 20.255 4.97235 19.1067C3.81415 17.9584 2.95636 16.5423 2.47271 14.9831C1.98906 13.4239 1.89487 11.769 2.19805 10.1646C2.50123 8.56024 3.19389 7.05236 4.21449 5.77738C5.23509 4.5024 6.55233 3.49552 8.05 2.84003" stroke="#8B1538" stroke-width="2" stroke-linecap="round"/>
            <path d="M12 8V12L15 15" stroke="#6F112D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="10" stroke="#8B1538" stroke-width="2" stroke-dasharray="2 4" opacity="0.3"/>
        </svg>

        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-description">
            Oops! The page you're looking for doesn't exist. It might have been moved or deleted.
        </p>

        <div class="action-buttons">
            <a href="/" class="btn btn-primary">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                Go Home
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Go Back
            </a>
        </div>
    </div>
</body>
</html>
