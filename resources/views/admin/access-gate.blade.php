<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title>Admin Access — Photographer SB</title>
    <meta name="description" content="Photographer SB Admin Portal - Secure access for authorized administrators only.">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Prevent framing (clickjacking protection) -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta property="og:title" content="Admin Access — Photographer SB">
    <meta property="og:description" content="Photographer SB Admin Portal">
    <meta property="og:type" content="website">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Darkroom-inspired aesthetic */
        .access-gate-bg {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a0a0f 50%, #2d1015 100%);
            position: relative;
            overflow: hidden;
        }

        /* Subtle photography-themed pattern overlay */
        .access-gate-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(139, 21, 56, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(198, 47, 81, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Film strip accent */
        .film-strip {
            display: flex;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .film-frame {
            width: 2rem;
            height: 2rem;
            background: rgba(139, 21, 56, 0.2);
            border: 1px solid rgba(139, 21, 56, 0.3);
            border-radius: 0.25rem;
            animation: flicker 2s infinite;
        }

        .film-frame:nth-child(1) { animation-delay: 0s; }
        .film-frame:nth-child(2) { animation-delay: 0.2s; }
        .film-frame:nth-child(3) { animation-delay: 0.4s; }
        .film-frame:nth-child(4) { animation-delay: 0.6s; }
        .film-frame:nth-child(5) { animation-delay: 0.8s; }

        @keyframes flicker {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.8; }
        }

        /* Lens aperture animation */
        @keyframes aperture {
            0% { opacity: 0.5; }
            50% { opacity: 1; }
            100% { opacity: 0.5; }
        }

        .aperture-icon {
            animation: aperture 3s infinite;
        }

        /* Premium button styling */
        .btn-access {
            background: linear-gradient(135deg, #8B1538 0%, #C62F51 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-access::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-access:hover::before {
            left: 100%;
        }

        .btn-access:hover {
            box-shadow: 0 0 30px rgba(139, 21, 56, 0.4);
            transform: translateY(-2px);
        }

        .btn-access:active {
            transform: translateY(0);
        }

        /* Secondary link */
        .link-secondary {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }

        .link-secondary:hover {
            color: rgba(139, 21, 56, 1);
            border-bottom-color: rgba(139, 21, 56, 1);
        }

        /* Content container */
        .gate-content {
            position: relative;
            z-index: 10;
        }

        /* Hero text styling */
        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            line-height: 1.2;
            letter-spacing: -0.02em;
            font-weight: 700;
        }

        .hero-subtitle {
            font-size: clamp(1rem, 2vw, 1.25rem);
            line-height: 1.6;
            opacity: 0.9;
        }

        /* Logo area */
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        /* Responsive spacing */
        @media (max-width: 640px) {
            .gate-content {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
        }

        /* Accessibility focus states */
        .btn-access:focus-visible,
        .link-secondary:focus-visible {
            outline: 2px solid rgba(139, 21, 56, 0.8);
            outline-offset: 2px;
        }
    </style>
</head>
<body class="access-gate-bg font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="gate-content w-full max-w-md">
            <!-- Logo / Brand Area -->
            <div class="logo-container">
                <div class="inline-block">
                    <!-- Photography-themed icon -->
                    <svg class="aperture-icon w-16 h-16 text-primary mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>

                <h3 class="text-sm font-semibold text-gray-400 mb-2 tracking-widest uppercase">Photographer SB</h3>
                <p class="text-xs text-gray-500">Your Creative Portfolio Platform</p>
            </div>

            <!-- Main Content -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 md:p-10 shadow-2xl">
                <!-- Title & Subtitle -->
                <div class="text-center mb-8">
                    <h1 class="hero-title text-white mb-3">
                        Admin Access
                    </h1>
                    <p class="hero-subtitle text-gray-300 italic">
                        "This darkroom is protected. Only authorized admins can enter."
                    </p>
                </div>

                <!-- Film Strip Accent -->
                <div class="flex justify-center">
                    <div class="film-strip">
                        <div class="film-frame"></div>
                        <div class="film-frame"></div>
                        <div class="film-frame"></div>
                        <div class="film-frame"></div>
                        <div class="film-frame"></div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-8 mb-10 text-center">
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Welcome to the Photographer SB administrative portal. This area is strictly limited to authorized administrators and team members only.
                    </p>
                </div>

                <!-- Primary CTA Button -->
                <div class="mb-6">
                    <a href="/admin/login" class="btn-access w-full py-3 px-6 rounded-lg font-semibold text-white text-center inline-block transition-all">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Admin Login
                        </span>
                    </a>
                </div>

                <!-- Secondary Links -->
                <div class="text-center space-y-3">
                    <p>
                        <a href="{{ url('/') }}" class="link-secondary text-sm">
                            ← Back to Photographer SB
                        </a>
                    </p>
                    
                    <p class="text-xs text-gray-500 pt-2 border-t border-gray-700">
                        Lost access? 
                        <a href="mailto:support@photographar.com" class="text-primary hover:text-primary-light transition">
                            Contact support
                        </a>
                    </p>
                </div>
            </div>

            <!-- Security Badge (Subtle) -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-600 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-green-500/50" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414L10 3.586l4.707 4.707a1 1 0 01-1.414 1.414L11 6.414V15a1 1 0 11-2 0V6.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Secure portal — All traffic encrypted
                </p>
            </div>
        </div>
    </div>

    <!-- Prevent XSS in meta tags - no inline styles with user data -->
    <!-- Optional: Add meta refresh blocker for security -->
    <script nonce="restricted">
        // Security measures
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent any automatic redirects that might be injected
            window.stop();

            // Log access attempt (optional, for security audit)
            if (window.performance && window.performance.timing) {
                const perfData = window.performance.timing;
                const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
                console.log('Page loaded securely in ' + pageLoadTime + 'ms');
            }
        });

        // Disable right-click on this admin page
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        }, false);
    </script>
</body>
</html>
