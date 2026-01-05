<div class="card shadow-lg border-0 mb-4">
    <!-- Card Header with Stats -->
    <div class="card-header py-3 bg-white border-bottom-0">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-stream mr-2"></i>
                    DATA CANTEEN {{ $canteenNo }}
                    <span class="ml-2 d-inline-flex align-items-center">
                        <span class="position-relative d-inline-flex">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success opacity-75" style="width: 10px; height: 10px; background: #28a745; border-radius: 50%; display: inline-block;"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-success" style="width: 10px; height: 10px; background: #218838; border-radius: 50%; display: inline-block; position: absolute; top: 0; left: 0;"></span>
                        </span>
                        <!-- <span class="badge badge-success border-0 px-2 ml-1" style="font-size: 0.7rem; letter-spacing: 1px;">LIVE</span> -->
                    </span>
                    <span class="badge badge-light shadow-sm ml-2 text-primary border">{{ $totalCount }} Today</span>
                </h6>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center">
                    <!-- Per Page Selector -->
                    <select wire:model="perPage" class="form-control form-control-sm mr-2" style="width: 80px;">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    
                    <!-- Search Input -->
                    <div class="input-group" style="width: 250px;">
                        <input 
                            type="text" 
                            class="form-control form-control-sm" 
                            placeholder="Cari NPK/Nama/Dept..." 
                            wire:model.debounce.300ms="search">
                        <div class="input-group-append">
                            <span class="input-group-text bg-primary text-white">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Table Body -->
    <div class="card-body">
        <div class="table-responsive position-relative" wire:loading.class="opacity-50">
            <!-- Loading Overlay -->
            <div wire:loading.flex class="justify-content-center align-items-center position-absolute w-100 h-100" 
                 style="top: 0; left: 0; background: rgba(255,255,255,0.7); z-index: 10;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            
            <table class="table table-bordered table-hover table-sm mb-0">
                <thead class="thead-light">
                    <tr>
                        <th width="60">No</th>
                        <th>NPK</th>
                        <th>Nama Karyawan</th>
                        <th>Department</th>
                        <th>Canteen</th>
                        <th width="120">Waktu Scan</th>
                    </tr>
                </thead>
                <tbody x-data>
                    @forelse($logs as $index => $log)
                    <tr x-show="true" 
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 transform -translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0">
                        <td class="text-center">
                            {{ ($logs->currentPage() - 1) * $logs->perPage() + $index + 1 }}
                        </td>
                        <td>
                            <span class="font-weight-bold text-primary">{{ $log->npk }}</span>
                        </td>
                        <td>{{ $log->name }}</td>
                        <td>{{ $log->dept ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $log->canteen_no }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-secondary">
                                {{ $log->created_at->format('H:i:s') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p class="mb-0">Belum ada data scanning hari ini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} data
            </div>
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
