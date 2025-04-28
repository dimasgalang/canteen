<!DOCTYPE html>
<html lang="en">
@include('layout.header')
<body id="page-top">
@include('sweetalert::alert')
<!-- Page Wrapper -->
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
                    <h1 class="h3 mb-0 text-gray-800">Daftar Karyawan</h1>
                    <div>
                    <form method="GET" action="{{ route('karyawan.batchQR') }}" >
                        <button id="submitqr" type="submit" class="btn btn-sm btn-primary shadow-s">Generate Batch QR</button>
                    </form>
                    <form method="GET" action="{{ route('karyawan.batchBarcode') }}" >
                        <button id="submitbarcode" type="submit" class="btn btn-sm btn-primary shadow-s">Generate Batch Barcode</button>
                    </form>
                    </div>
                </div>
                
                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Data Karyawan</h6>
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
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>NPK</th>
                                        <th>Nama Karyawan</th>
                                        <th>Bagian</th>
                                        <th>QR</th>
                                        <th>Barcode</th>
                                        <!-- <th>Photo</th> -->
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $employee)
                                    <tr>
                                        <td>{{ $employee->NPK }}</td>
                                        <td>{{ $employee->NAMA_KARYAWAN }}</td>
                                        <td>{{ $employee->DEPARTEMENT }}</td>
                                        <td class="text-center"><img id="qr" src="{{url('/storage/qr/'. $employee->NPK . '_' . $employee->NAMA_KARYAWAN . '_' . $employee->DEPARTEMENT . '.png')}}" style="width: 150;"></td>
                                        <td class="text-center"><img id="barcode" src="{{url('/storage/barcode/'. $employee->NPK . '_' . $employee->NAMA_KARYAWAN . '_' . $employee->DEPARTEMENT . '.png')}}" style="width: 150;"></td>
                                        <td align="center">
                                            <a href ="{{ route('karyawan.generateqr', ['id' => $employee->NPK]) }}" class="btn btn-primary btn-circle btn-sm">
                                                <i class="fas fa-qrcode"></i>
                                            </a>
                                            <a id="show-user" class="btn btn-primary btn-circle btn-sm btn-show-detail" data-url="{{ route('karyawan.detail', $employee->NPK) }}" data-show-link="{{ $employee->NPK }}" data-show-title="{{ $employee->NPK . ' - ' . $employee->NAMA_KARYAWAN }}" data-show-image="{{ asset('/foto-npk/' . $employee->NPK . '.jpg') }}">
                                                <i class="fas fa-info"></i>
                                            </a>
                                            <a class="btn btn-danger btn-circle btn-sm btn-delete-record" data-delete-link="delete/{{ $employee->NPK }}" data-delete-name="{{ $employee->NAMA_KARYAWAN }}" data-toggle="modal" data-target="#deleteModal">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Content Row -->

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->
         
        <!-- Modal -->
        <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="detail-title" class="modal-title" id="exampleModalLabel">Data Karyawan</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-3 col-md-6 mb-4">
                                <center><img id="employee-PIC" src="" style="width: 200px;"></center>
                            </div>
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div>
                                    <label>NPK :</label>
                                    <input class="form-control" type="text" id="employee-NPK" value="" readonly>
                                </div>
                                <br>
                                <div>
                                    <label>NAMA :</label>
                                    <input class="form-control" type="text" id="employee-NAMA" value="" readonly>
                                </div>
                                <br>
                                <div>
                                    <label>DEPARTEMENT :</label>
                                    <input class="form-control" type="text" id="employee-DEPARTEMENT" value="" readonly>
                                </div>
                                <br>
                                <div>
                                    <label>JENIS KELAMIN :</label>
                                    <input class="form-control" type="text" id="employee-JK" value="" readonly>
                                </div>
                                <br>
                                <div>
                                    <label>TANGGAL LAHIR :</label>
                                    <input class="form-control" type="text" id="employee-TGLLAHIR" value="" readonly>
                                </div>
                                <br>
                                <div>
                                    <label>PENDIDIKAN TERAKHIR :</label>
                                    <input class="form-control" type="text" id="employee-PDDK" value="" readonly>
                                </div>
                                <br>
                                <div>
                                    <label>AGAMA :</label>
                                    <input class="form-control" type="text" id="employee-AGAMA" value="" readonly>
                                </div>
                                <br>
                                <div>
                                    <label>TMK :</label>
                                    <input class="form-control" type="text" id="employee-TMK" value="" readonly>
                                </div>
                                <br>
                            </div>
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div>
                                    <label>USIA MASUK :</label>
                                    <input class="form-control" type="text" id="employee-USIA" value="" readonly>
                                </div>
                                <br>
                                <div>
                                    <label>ALAMAT :</label>
                                    <input class="form-control" type="text" id="employee-ALAMAT" value="" readonly>
                                </div>
                                <br>
                                <div>
                                    <label>KABUPATEN :</label>
                                    <input class="form-control" type="text" id="employee-KABUPATEN" value="" readonly>
                                </div>
                                <br>
                                <div>
                                    <label>NO. KTP :</label>
                                    <input class="form-control" type="text" id="employee-KTP" value="" readonly>
                                </div>
                                <br>
                                <div>
                                    <label>NO. KK :</label>
                                    <input class="form-control" type="text" id="employee-NO_KK" value="" readonly>
                                </div>
                                <br>
                                <div>
                                    <label>NAMA IBU :</label>
                                    <input class="form-control" type="text" id="employee-IBU" value="" readonly>
                                </div>
                                <br>
                                <div>
                                    <label>NO. HP :</label>
                                    <input class="form-control" type="text" id="employee-HP" value="" readonly>
                                </div>
                                <br>
                                <div>
                                    <label>STATUS :</label>
                                    <input class="form-control" type="text" id="employee-STATUS" value="" readonly>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
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
<script src="{{asset('vendor/sweetalert/sweetalert2.js')}}"></script>

<!-- Page level custom scripts -->
<script src="{{asset('js/demo/datatables-demo.js')}}"></script>
<script>
    $('#dataTable').DataTable({
    "order": [[ 2, "asc" ]] 
});
</script>
<script>
    $('.btn-show-detail').on('click', function () {
        $('#pdf-src').attr('src', '/slip-gaji/' + $(this).data('show-link'));
        $("#detail-title").text($(this).data('show-title'));
    });
    $('.btn-delete-record').on('click', function () {
            $('#btn-confirm').attr('href', $(this).data('delete-link'));
            $("#modal-text-record").text('Apakah anda yakin ingin menghapus karyawan ' + $(this).data('delete-name') + '?');
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('body').on('click', '#show-user', function() {
            var userURL = $(this).data('url');
            var userIMG = $(this).data('show-image');
            $.get(userURL, function (data) {
                $('#detailModal').modal('show');
                $('#employee-NPK').val(data[0].NPK);
                $('#employee-NAMA').val(data[0].NAMA);
                $('#employee-DEPARTEMENT').val(data[0].DEPARTEMENT);
                $('#employee-JK').val(data[0].JK);
                $('#employee-TGLLAHIR').val(data[0].TGLLAHIR);
                $('#employee-PDDK').val(data[0].PDDK);
                $('#employee-AGAMA').val(data[0].AGAMA);
                $('#employee-TMK').val(data[0].TMK);
                $('#employee-USIA').val(data[0].USIA);
                $('#employee-ALAMAT').val(data[0].ALAMAT);
                $('#employee-KABUPATEN').val(data[0].KABUPATEN);
                $('#employee-KTP').val(data[0].KTP);
                $('#employee-NO_KK').val(data[0].NO_KK);
                $('#employee-IBU').val(data[0].IBU);
                $('#employee-HP').val(data[0].HP);
                $('#employee-STATUS').val(data[0].STATUS);
                $('#employee-PIC').attr('src', userIMG);
            })
        });
    });
    
    $("#submitqr").click(function() {
        $(this).hide();
        Swal.fire({
            title: "Process",
            html: "Generating All QR Code.. Please Wait!!",
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            },
        })
    });
    $("#submitbarcode").click(function() {
        $(this).hide();
        Swal.fire({
            title: "Process",
            html: "Generating All Barcode.. Please Wait!!",
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            },
        })
    });
</script>
</html>