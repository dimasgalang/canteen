<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,400,500,700,900" rel="stylesheet">

    <title>Chutex International Indonesia</title>
    <link rel="icon" type="image/x-icon" href="{{asset('img/icon.ico')}}">
    <!-- Additional CSS Files -->
    <link href="{{asset('homepage/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('homepage/css/font-awesome.css')}}" rel="stylesheet">
    <link href="{{asset('homepage/css/fullpage.min.css')}}" rel="stylesheet">
    <link href="{{asset('homepage/css/owl.carousel.css')}}" rel="stylesheet">
    <link href="{{asset('homepage/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('homepage/css/templatemo-style.css')}}" rel="stylesheet">
    <link href="{{asset('homepage/css/responsive.css')}}" rel="stylesheet">

    </head>
    
    <body>
    
    <div>
        <div class="preloader">
            <div class="preloader-bounce">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <header id="header">
            <div class="container-fluid">
                <div class="navbar">
                    <a href="#" id="logo" title="Chutex">
                    </a>
                    <div class="navigation-row">
                        <nav id="navigation">
                            <button type="button" class="navbar-toggle"> <i class="fa fa-bars"></i> </button>
                            <div class="nav-box navbar-collapse">
                                <ul class="navigation-menu nav navbar-nav navbars" id="nav">
                                    <li><a href="#">Home</a></li>
                                    <li><a href="{{ route('login') }}">Login</a></li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <video autoplay muted loop id="myVideo">
          <source src="{{asset('homepage/images/chutex.mp4')}}" type="video/mp4">
        </video>

        <div id="fullpage" class="fullpage-default">

        </div>

        <div id="social-icons">
            <div class="text-right">
                <ul class="social-icons">
                    <li><a href="#" title="Facebook"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="#" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="#" title="Linkedin"><i class="fa fa-linkedin"></i></a></li>
                    <li><a href="#" title="Instagram"><i class="fa fa-behance"></i></a></li>
                </ul>
            </div>
        </div>
    </div>  

    <script src="{{asset('homepage/js/jquery.js')}}"></script>
    <script src="{{asset('homepage/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('homepage/js/scrolloverflow.js')}}"></script>
    <script src="{{asset('homepage/js/owl.carousel.min.js')}}"></script>
    <script src="{{asset('homepage/js/fullpage.min.js')}}"></script>
    <script src="{{asset('homepage/js/jquery.inview.min.js')}}"></script>
    <script src="{{asset('homepage/js/form.js')}}"></script>
    <script src="{{asset('homepage/js/custom.js')}}"></script>
  </body>
</html>