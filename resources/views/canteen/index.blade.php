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
                <form method="get" action="{{ route('canteen.export') }}" enctype="multipart/form-data">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Canteen List</h1>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-sm-flex align-items-center justify-content-between mb-4">
                                <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div>
                                            <label>From Date :</label>
                                            <input class="date form-control" type="date" id="fromdate" name="fromdate" value="">
                                        </div>
                                        <br>
                                        <button id='filter-data' type="button" class="btn btn-primary">Filter</button>
                                        <button id='export' type="submit" class="btn btn-success">Export To Excel</button>
                                        <button id='synchronize' type="button" class="btn btn-info"><i class="fas fa-sync fa-sm"></i>  Synchronize</button>
                                    </div>
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div>
                                            <label>To Date :</label>
                                            <input class="date form-control" type="date" id="todate" name="todate" value="">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div>
                                            <label>Canteen :</label>
                                            <select name="canteen_no" id="canteen_no" class="form-control">
                                                <option disabled selected hidden>Select Canteen</option>
                                                <option value="1">Canteen 1</option>
                                                <option value="2">Canteen 2</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div>
                                            <label>Break :</label>
                                            <select name="break" id="break" class="form-control">
                                                <option disabled selected hidden>Select Break</option>
                                                <option value="normal">Normal Break</option>
                                                <option value="overtime">Overtime Break</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-sm-flex align-items-center justify-content-between mb-4">
                                <h6 class="m-0 font-weight-bold text-primary">Canteen Data</h6>
                            </div>
                            <div class="card-body">
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
            </form>

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
<script src="{{asset('vendor/sweetalert/sweetalert2.js')}}"></script>

<!-- Page level custom scripts -->
<script src="{{asset('js/demo/datatables-demo.js')}}"></script>
<script type="text/javascript">
    $( document ).ready(function() {
        var date = new Date();
        var firstDay = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
        var lastDay = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + (date.getDate() + 1)).slice(-2);
        document.getElementById("fromdate").value = firstDay;
        document.getElementById("todate").value = lastDay;
    });

    var jsonCanteen = '{{ route("canteen.showcanteen") }}';
    $.get(jsonCanteen, function (data) {
        var tableCanteen = $('#dataTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: true,
        dom: 'rtip',
        ajax: {
            url: jsonCanteen,
            data: function (d) {
                    d.fromdate = document.getElementById('fromdate').value,
                    d.todate = document.getElementById('todate').value,
                    d.canteen_no = document.getElementById('canteen_no').value
                    d.break = document.getElementById('break').value
                }
            },
        columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                { data: 'npk', name: 'npk', orderable: false },
                { data: 'name', name: 'name', orderable: false },
                { data: 'canteen_no', name: 'canteen_no', orderable: false },
                { data: 'created_at_formated', name: 'created_at_formated', orderable: false },
            ],
        });
        $('#filter-data').click(function(){
            tableCanteen.draw();
            // console.log("Clicked");
        });
        // setInterval( function () {
        //     tableCanteen.ajax.reload();
        // }, 1000);

        $('#synchronize').click(function(){
            $(this).hide();
            Swal.fire({
                title: "Process",
                html: "Syncronizing Data.. Please Wait!!",
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                },
            })
            
            $.post('{{ route("canteen.synchronize") }}', {
                _token: '{{ csrf_token() }}',
                canteen_no: document.getElementById('canteen_no').value,
            }).done(function(response) {
                if (response.success) {
                // Success logic here
                $('#synchronize').show();
                Swal.fire({
                    title: 'Success',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        tableCanteen.draw();
                    }
                });
            } else {
                // Handle case where success is false
                Swal.fire({
                    title: 'Failed',
                    text: 'Failed to synchronize canteen data',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
            }).fail(function(xhr) {
                // Handle error response
                console.log("Error:", xhr.responseText);
            });
        });
    });
    </script>

</html>