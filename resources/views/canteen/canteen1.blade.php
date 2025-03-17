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
                    <h1 class="h3 mb-0 text-gray-800">Canteen 1</h1>
                </div>
                
                <div class="row">
                    
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-sm-flex align-items-center justify-content-between mb-4">
                                <h6 class="m-0 font-weight-bold text-primary">Scan Here</h6>
                            </div>
                            <div class="card-body">
                                <div id="reader"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-sm-flex align-items-center justify-content-between mb-4">
                                <h6 class="m-0 font-weight-bold text-primary">Canteen Data</h6>
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
                    <div class="modal-body"><p id="modal-text-record"></p></div>
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
<script src="{{asset('js/demo/datatables-demo.js')}}"></script>
<script>
    var tableCanteen = $('#dataTable').DataTable({
    destroy: true,
    responsive: true,
    serverside:true,
    ajax: '{{ route("canteen.showcanteen1") }}',
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
<script src="{{ asset('vendor/jquery/html5-qrcode.min.js') }}"></script>
<script>
    var date = document.getElementById('date').value;
    let html5QRCodeScanner = new Html5QrcodeScanner(
        "reader", {
            fps: 10,
            qrbox: {
                width: 300,
                height: 300,
            },
            supportedScanTypes: [
                // Html5QrcodeScanType.SCAN_TYPE_FILE, 
                Html5QrcodeScanType.SCAN_TYPE_CAMERA
            ],
        }
    );

    function onScanSuccess(decodedText, decodedResult) {
        // redirect ke link hasil scan
        // var decoder = "canteen?npk=" + decodedResult.decodedText + "&canteen_no=1";
        var decoder = decodedResult.decodedText + "&canteen_no=1&date=" + date;
        // alert(decoder);
        window.location.href = decoder;
        html5QRCodeScanner.clear();
        window.location.href = decoder;
    }
    html5QRCodeScanner.render(onScanSuccess);
</script>

</html>