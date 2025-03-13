<!DOCTYPE html>
<html lang="en">
@include('layout.header')
<body id="page-top">
<!-- Page Wrapper -->
@include('sweetalert::alert')
<div id="wrapper">
@include('layout.sidebar')

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            @include('layout.navbar')

            <!-- Begin Page Content -->
            <div class="container-fluid">
                @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>	
                    <strong>{{ $message }}</strong>
                </div>
                @endif

                @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>	
                    <strong>{{ $message }}</strong>
                </div>
                @endif

                @if ($message = Session::get('warning'))
                <div class="alert alert-warning alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>	
                    <strong>{{ $message }}</strong>
                </div>
                @endif

                @if ($message = Session::get('info'))
                <div class="alert alert-info alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>	
                    <strong>{{ $message }}</strong>
                </div>
                @endif
                
                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                </div>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Illustrations</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" width="100%"
                                src="{{asset('img/e-signature.jpg')}}" alt="...">
                        </div>
                        <p>Add some quality, svg illustrations to your project courtesy of <a
                                target="_blank" rel="nofollow" href="https://undraw.co/">unDraw</a>, a
                            constantly updated collection of beautiful svg images that you can use
                            completely free and without attribution!</p>
                        <a target="_blank" rel="nofollow" href="https://undraw.co/">Browse Illustrations on
                            unDraw &rarr;</a>
                    </div>
                </div>
            </div>

        </div>
        <!-- End of Main Content -->

@include('layout.footer')
</body>
<script src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('js/demo/datatables-demo.js')}}"></script>
</html>