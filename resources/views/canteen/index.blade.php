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
                    <h1 class="h3 mb-0 text-gray-800">Canteen List</h1>
                </div>
                
                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-sm-flex align-items-center justify-content-between mb-4">
                        <h6 class="m-0 font-weight-bold text-primary">Canteen Data</h6>
                        <form method="GET" id="form-void">
                                <select name="void" id="void" class="form-control" onchange="document.getElementById('form-void').submit()" style="width: 300px;">
                                    <option disabled selected hidden>Select Status</option>
                                    <option value="false" {{ app('request')->input('void') == 'false'  ? 'selected' : ''}}>Active</option>
                                    <option value="true" {{ app('request')->input('void') == 'true'  ? 'selected' : ''}}>Void</option>
                                </select>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>NPK</th>
                                        <th>Canteen</th>
                                        <th>Time Scanning</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($canteens as $canteen)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $canteen->npk }}</td>
                                        <td>{{ $canteen->canteen_no }}</td>
                                        <td>{{ $canteen->created_at }}</td>
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

<!-- Page level custom scripts -->
<script src="{{asset('js/demo/datatables-demo.js')}}"></script>

</html>