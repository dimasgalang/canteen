<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chutex E-Canteen')</title>

    <!-- Fonts & Styles -->
    <link rel="icon" type="image/x-icon" href="{{asset('storage/images/canteen_icon.ico')}}">
    <link href="{{asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="{{asset('css/sb-admin-2.min.css')}}" rel="stylesheet">
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <!-- Alpine.js for animations -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        /* Livewire loading indicator */
        [wire\:loading] { display: none; }
        [wire\:loading].flex { display: flex; }
        
        /* Flash message animations */
        .flash-enter { opacity: 0; transform: translateY(-10px); }
        .flash-enter-active { transition: all 0.3s ease-out; }
        .flash-leave-active { transition: all 0.3s ease-in; }
        .flash-leave-to { opacity: 0; transform: translateY(-10px); }
        
        /* Auto-focus glow effect */
        .scanner-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.5);
            border-color: #4e73df;
        }
        
        /* Loading overlay */
        .opacity-50 { opacity: 0.5; }
    </style>
    
    @stack('styles')
</head>
<body id="page-top">
    <div id="wrapper">
        @include('layout.sidebar')
        
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('layout.navbar')
                
                <!-- Main Content -->
                <div class="container-fluid">
                    {{ $slot }}
                </div>
            </div>
            
            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; PT. Chutex International Indonesia - {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Core Scripts -->
    <script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('vendor/jquery-easing/jquery.easing.min.js')}}"></script>
    <script src="{{asset('js/sb-admin-2.min.js')}}"></script>
    
    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Global Livewire Event Handlers -->
    <script>
        // Refocus input after Livewire updates
        document.addEventListener('livewire:load', function () {
            Livewire.hook('message.processed', (message, component) => {
                const scannerInput = document.querySelector('.scanner-input');
                if (scannerInput) {
                    scannerInput.focus();
                }
            });
        });
        
        // Handle focus-scanner event
        window.addEventListener('focus-scanner', event => {
            const input = document.querySelector('.scanner-input');
            if (input) {
                input.focus();
                input.select();
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
