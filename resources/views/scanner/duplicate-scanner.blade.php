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
                        <h1 class="h3 mb-0 text-gray-800">Duplicate Scanner</h1>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-sm-flex align-items-center justify-content-between mb-4">
                                    <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-end">
                                        <div class="col-xl-3 col-md-4 col-sm-6 mb-2">
                                            <label>Date :</label>
                                            <input class="form-control" type="date" id="filterdate" name="filterdate" value="">
                                        </div>
                                        <div class="col-auto mb-2">
                                            <button id='filter-data' type="button" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                                        </div>
                                        <div class="col-auto mb-2">
                                            <button id='btn-bulk-move' type="button" class="btn btn-success" disabled><i class="fas fa-exchange-alt"></i> Bulk Move to Canteen</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-sm-flex align-items-center justify-content-between mb-4">
                                    <h6 class="m-0 font-weight-bold text-primary">Duplicate Scanner Data</h6>
                                    <span id="selected-count" class="badge badge-info" style="display:none;">0 selected</span>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm" id="dataTable" width="100%"
                                            cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th width="30"><input type="checkbox" id="select-all"></th>
                                                    <th>No</th>
                                                    <th>NPK</th>
                                                    <th>Nama Karyawan</th>
                                                    <th>Already Scanned Canteen</th>
                                                    <th>Need To Scan Canteen</th>
                                                    <th>Date</th>
                                                    <th>Created At</th>
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

            @include('layout.footer')

        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->
</body>
<!-- Page level plugins -->
<script src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

<!-- Page level custom scripts -->
<script src="{{asset('js/demo/datatables-demo.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var date = new Date();
        var today = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
        document.getElementById("filterdate").value = today;

        var jsonDuplicate = '{{ route("scanner.duplicateData") }}';
        var tableDuplicate = $('#dataTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            dom: 'rtip',
            ajax: {
                url: jsonDuplicate,
                data: function (d) {
                    d.date = document.getElementById('filterdate').value;
                }
            },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'npk', name: 'npk', orderable: false },
                { data: 'name', name: 'name', orderable: false },
                { data: 'already_scan_canteen_number', name: 'already_scan_canteen_number', orderable: false },
                { data: 'need_to_scan_canteen_number', name: 'need_to_scan_canteen_number', orderable: false },
                { data: 'date_formated', name: 'date_formated', orderable: false },
                { data: 'created_at_formated', name: 'created_at_formated', orderable: false },
            ],
        });

        // Filter button
        $('#filter-data').click(function () {
            tableDuplicate.draw();
        });

        // Select All checkbox
        $('#select-all').on('click', function () {
            var isChecked = $(this).is(':checked');
            $('.select-row').prop('checked', isChecked);
            updateSelectedCount();
        });

        // Individual checkbox change
        $(document).on('change', '.select-row', function () {
            var total = $('.select-row').length;
            var checked = $('.select-row:checked').length;
            $('#select-all').prop('checked', total === checked);
            updateSelectedCount();
        });

        // Update selected count badge and button state
        function updateSelectedCount() {
            var count = $('.select-row:checked').length;
            if (count > 0) {
                $('#selected-count').text(count + ' selected').show();
                $('#btn-bulk-move').prop('disabled', false);
            } else {
                $('#selected-count').hide();
                $('#btn-bulk-move').prop('disabled', true);
            }
        }

        // Bulk Move button
        $('#btn-bulk-move').on('click', function () {
            var selectedIds = [];
            $('.select-row:checked').each(function () {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                alert('Please select at least one record.');
                return;
            }

            if (!confirm('Are you sure you want to move ' + selectedIds.length + ' record(s) to their respective canteen tables?')) {
                return;
            }

            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: '{{ route("scanner.duplicate.bulkMove") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: selectedIds
                },
                success: function (response) {
                    alert(response.message);
                    $('#select-all').prop('checked', false);
                    updateSelectedCount();
                    tableDuplicate.draw();
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred.';
                    alert('Error: ' + msg);
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fas fa-exchange-alt"></i> Bulk Move to Canteen');
                }
            });
        });
    });
</script>

</html>