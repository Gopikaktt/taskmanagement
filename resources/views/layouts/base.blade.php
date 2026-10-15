<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Task Management System')</title>
    
    <!-- Design System CSS (with cache buster) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ file_exists(public_path('css/app.css')) ? filemtime(public_path('css/app.css')) : time() }}">
    
    <!-- Feather Icons or Simple SVG -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body>
    @yield('navbar')

    <main class="container">
        @if (session('success'))
            <div class="alert alert-success" role="alert" data-auto-dismiss="4000">
                <div class="alert-content">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="alert-close" aria-label="Close notification" title="Dismiss" onclick="dismissAlert(this.closest('.alert'))">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert" data-auto-dismiss="5000">
                <div class="alert-content">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="alert-close" aria-label="Close notification" title="Dismiss" onclick="dismissAlert(this.closest('.alert'))">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info" role="alert" data-auto-dismiss="4000">
                <div class="alert-content">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>{{ session('info') }}</span>
                </div>
                <button type="button" class="alert-close" aria-label="Close notification" title="Dismiss" onclick="dismissAlert(this.closest('.alert'))">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert" data-auto-dismiss="7000">
                <div class="alert-content" style="align-items: flex-start;">
                    <i class="fa-solid fa-circle-xmark" style="margin-top: 2px;"></i>
                    <div>
                        <strong>Please check the following errors:</strong>
                        <ul style="margin-top: 0.25rem; padding-left: 1.25rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="alert-close" aria-label="Close notification" title="Dismiss" onclick="dismissAlert(this.closest('.alert'))">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Toast / Alert Auto-Dismiss & Close Handlers -->
    <script>
        function dismissAlert(alertEl) {
            if (!alertEl || alertEl.classList.contains('alert-dismissing')) return;
            alertEl.classList.add('alert-dismissing');
            alertEl.style.opacity = '0';
            alertEl.style.transform = 'translateY(-6px)';
            
            setTimeout(() => {
                alertEl.style.maxHeight = '0';
                alertEl.style.paddingTop = '0';
                alertEl.style.paddingBottom = '0';
                alertEl.style.marginTop = '0';
                alertEl.style.marginBottom = '0';
                alertEl.style.borderWidth = '0';
            }, 120);

            setTimeout(() => {
                alertEl.remove();
            }, 450);
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.alert[data-auto-dismiss]').forEach(alert => {
                const timeoutMs = parseInt(alert.getAttribute('data-auto-dismiss')) || 4000;
                let timer = setTimeout(() => dismissAlert(alert), timeoutMs);

                // Pause timer when mouse hovers over alert, restart timer when mouse leaves
                alert.addEventListener('mouseenter', () => {
                    clearTimeout(timer);
                });

                alert.addEventListener('mouseleave', () => {
                    timer = setTimeout(() => dismissAlert(alert), 2000);
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
