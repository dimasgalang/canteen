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

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Barcode Scanner</h1>
                </div>
                
                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Data Barcode</h6>
                    </div>
                    <div class="card-body">
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
                        <div>
                            <label>Barcode :</label>
                            <input class="form-control" type="text" id="barcode" name="barcode" autocomplete="off">
                        </div>
                        <br>
                    </div>
                </div>
                <!-- Content Row -->

            </div>
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-sm-flex align-items-center justify-content-between mb-4">
                                <h6 class="m-0 font-weight-bold text-primary">Canteen 2 Data</h6>
                                <a href="{{ route('canteen.createManual') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                    <i class="fas fa-plus fa-sm text-white-50"></i> Add Manual Data
                                </a>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="date" id="date" value="{{ date('Y-m-d') }}">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>NPK</th>
                                                <th>Nama Karyawan</th>
                                                <th>Canteen</th>
                                                <th>Time Scanning</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->
        <!-- Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="delete-title" class="modal-title" id="exampleModalLabel">Delete Record</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body"><p id="modal-text-user"></p></div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                        <a id="btn-confirm" href=""><button class="btn btn-primary" type="button">Confirm</button></a>
                    </div>
                </div>
            </div>
        </div>

@include('layout.footer')
</body>
<!-- Page level plugins -->
<script src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

<!-- Page level custom scripts -->
<script src="{{asset('js/demo/datatables-demo.js')}}"></script>

<script>
    var tableCanteen = $('#dataTable').DataTable({
    destroy: true,
    responsive: true,
    ajax: '{{ route("canteen.showcanteen2") }}',
    columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            { data: 'npk', name: 'npk', orderable: false },
            { data: 'name', name: 'name', orderable: false },
            { data: 'canteen_no', name: 'canteen_no', orderable: false },
            { data: 'created_at_formated', name: 'created_at_formated', orderable: false },
        ],
    });
    // setInterval( function () {
    //     tableCanteen.ajax.reload();
    // }, 1000);
</script>

<script>
    $('.btn-delete-user').on('click', function () {
        $('#btn-confirm').attr('href', $(this).data('delete-link'));
        $("#modal-text-user").text('Apakah anda yakin ingin menghapus user ' + $(this).data('user-name') + '?');
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        document.getElementById("barcode").focus();
        $('input[name="barcode"]').blur(function(){
            $('input[name="barcode"]').focus();
        });

        var typingTimer;
        var doneTypingInterval = 500;
        var $input = $('#barcode');

        $input.on('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });

        $input.on('keydown', function () {
            clearTimeout(typingTimer);
        });

        function doneTyping () {
            onScanSuccess();
        }
        // document.getElementById("barcode").addEventListener("input", onScanSuccess);
    });
    function onScanSuccess() {
        var decoder = document.getElementById("barcode").value;
        // alert("?barcode=" + decoder);
        window.location.href = "barcodecanteen2?barcode=" + decoder;
    }
</script>
</html>