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
                    <h1 class="h3 mb-0 text-gray-800">Add Outsourcing Scan Manually</h1>
                </div>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Input Data Form</h6>
                    </div>
                    <div class="card-body">

                        <form action="{{ route('canteen.storeManual') }}" method="POST">
                            @csrf

                            <div id="form-container">
                                <div class="card border-left-primary shadow-sm mb-3 item-row">
                                    <div class="card-body py-3">
                                        <div class="form-row">
                                            <div class="form-group col-md-2">
                                                <label class="small font-weight-bold text-gray-700">Canteen No</label>
                                                <select class="form-control form-control-sm" name="data[0][canteen_no]" required>
                                                    <option value="1">Canteen 1</option>
                                                    <option value="2">Canteen 2</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="small font-weight-bold text-gray-700">NPK</label>
                                                <input type="text" class="form-control form-control-sm input-npk" name="data[0][npk]" readonly required>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="small font-weight-bold text-gray-700">Nama Karyawan</label>
                                                <select class="form-control form-control-sm select-name" name="data[0][name]" required>
                                                    <option value="" disabled selected>Select Name</option>
                                                    @foreach($outsources as $outsource)
                                                    <option value="{{ $outsource->NAMA }}">{{ $outsource->NAMA }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="small font-weight-bold text-gray-700">Department</label>
                                                <input type="text" class="form-control form-control-sm" name="data[0][dept]" placeholder="outsourcing" required>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="small font-weight-bold text-gray-700">Date</label>
                                                <input type="date" class="form-control form-control-sm date-input" name="data[0][date]" required value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                            </div>
                                            <div class="form-group col-md-1 d-flex flex-column justify-content-end">
                                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                                    <i class="fas fa-trash"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <button type="button" class="btn btn-outline-success btn-sm" id="addRow">
                                    <i class="fas fa-plus"></i> Add Employee
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save All Data
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->
        
@include('layout.footer')
</body>
<!-- Page level plugins -->

<script>
    $(document).ready(function() {
        let rowIdx = 1;
        let outsourceOptions = `@foreach($outsources as $outsource)<option value="{{ $outsource->NAMA }}">{{ $outsource->NAMA }}</option>@endforeach`;

        function initializeSelect2() {
            $('.select-name').select2({
                placeholder: 'Select Name',
                width: '100%'
            });
        }
        
        initializeSelect2();

        $(document).on('change', '.select-name', function() {
            let selectedName = $(this).val();
            let parentRow = $(this).closest('.item-row');
            
            if (selectedName) {
                $.ajax({
                    url: '{{ route('outsource.getNpk') }}',
                    type: 'GET',
                    data: { name: selectedName },
                    success: function(response) {
                        parentRow.find('.input-npk').val(response.npk);
                    }
                });
            } else {
                parentRow.find('.input-npk').val('');
            }
        });

        $('#addRow').on('click', function() {
            let currentDate = '{{ \Carbon\Carbon::now()->format('Y-m-d') }}';
            let newIdx = rowIdx;

            $('#form-container').append(`
                <div class="card border-left-primary shadow-sm mb-3 item-row" id="R${newIdx}">
                    <div class="card-body py-3">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label class="small font-weight-bold text-gray-700">Canteen No</label>
                                <select class="form-control form-control-sm" name="data[${newIdx}][canteen_no]" required>
                                    <option value="1">Canteen 1</option>
                                    <option value="2">Canteen 2</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="small font-weight-bold text-gray-700">NPK</label>
                                <input type="text" class="form-control form-control-sm input-npk" name="data[${newIdx}][npk]" placeholder="Autofilled" readonly required>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="small font-weight-bold text-gray-700">Nama Karyawan</label>
                                <select class="form-control form-control-sm select-name" name="data[${newIdx}][name]" required>
                                    <option value="" disabled selected>Select Name</option>
                                    ${outsourceOptions}
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="small font-weight-bold text-gray-700">Department</label>
                                <input type="text" class="form-control form-control-sm" name="data[${newIdx}][dept]" placeholder="outsourcing" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="small font-weight-bold text-gray-700">Date</label>
                                <input type="date" class="form-control form-control-sm date-input" name="data[${newIdx}][date]" required value="${currentDate}">
                            </div>
                            <div class="form-group col-md-1 d-flex flex-column justify-content-end">
                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            
            $('#R' + newIdx).find('.select-name').select2({
                placeholder: 'Select Name',
                width: '100%'
            });
            
            rowIdx++;
        });

        $(document).on('click', '.remove-row', function() {
            let rowCount = $('.item-row').length;
            if (rowCount > 1) {
                $(this).closest('.item-row').remove();
            } else {
                alert('At least one row is required.');
            }
        });
    });
</script>
</html>
