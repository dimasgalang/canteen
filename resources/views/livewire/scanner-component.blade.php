<div>
    @section('title', 'Barcode Scanner - Canteen ' . $canteenNo)
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-barcode mr-2"></i>
            Barcode Scanner - Canteen {{ $canteenNo }}
        </h1>
        <span class="badge badge-primary" x-data="{ time: new Date().toLocaleTimeString('en-GB') }" x-init="setInterval(() => time = new Date().toLocaleTimeString('en-GB'), 1000)">
            <i class="fas fa-clock mr-1"></i>
            <span x-text="time"></span>
        </span>
    </div>

    <!-- Flash Message with Alpine.js Auto-dismiss -->
    <div x-data="{ show: @entangle('showMessage') }" 
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-init="$watch('show', value => { if(value) setTimeout(() => { show = false; $wire.clearMessage() }, 3000) })"
         class="mb-4"
         style="display: none;">
        @if($message)
        <div class="alert alert-{{ $messageType }} alert-dismissible fade show d-flex align-items-center" role="alert">
            @if($messageType === 'success')
                <i class="fas fa-check-circle fa-2x mr-3"></i>
            @elseif($messageType === 'danger')
                <i class="fas fa-times-circle fa-2x mr-3"></i>
            @elseif($messageType === 'warning')
                <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
            @endif
            <div>
                <strong>{{ $message }}</strong>
            </div>
            <button type="button" class="close" @click="show = false; $wire.clearMessage()">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Scanner Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-qrcode mr-2"></i>
                Scan Barcode Karyawan
            </h6>
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="input-group input-group-lg">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-primary text-white">
                                <i class="fas fa-barcode"></i>
                            </span>
                        </div>
                        <input 
                            type="text" 
                            class="form-control form-control-lg scanner-input"
                            placeholder="Scan barcode disini..."
                            wire:model.defer="barcode"
                            wire:keydown.enter="processScan"
                            x-data
                            x-init="$el.focus()"
                            @clear-barcode.window="$el.value = ''; $el.focus()"
                            autocomplete="off"
                            autofocus>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" wire:click="processScan">
                                <span wire:loading.remove wire:target="processScan">
                                    <i class="fas fa-search"></i> Proses
                                </span>
                                <span wire:loading wire:target="processScan">
                                    <i class="fas fa-spinner fa-spin"></i> Loading...
                                </span>
                            </button>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block text-center">
                        Format barcode: NPK_NAMA_DEPARTMENT
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Table Component (embedded) -->
    @livewire('log-table-component', ['canteenNo' => $canteenNo])
    
    @push('scripts')
    <script>
        // Focus scanner input after any Livewire update
        document.addEventListener('livewire:load', function () {
            // Initial focus
            const input = document.querySelector('.scanner-input');
            if (input) input.focus();
            
            // Refocus after blur (when scanner loses focus for some reason)
            document.querySelector('.scanner-input')?.addEventListener('blur', function() {
                setTimeout(() => this.focus(), 100);
            });
        });
        
        // Handle clear barcode event - clear input and refocus
        window.addEventListener('clear-barcode', event => {
            const input = document.querySelector('.scanner-input');
            if (input) {
                input.value = '';
                input.focus();
            }
        });
        
        // Handle scan result sounds/effects
        window.addEventListener('scan-result', event => {
            const type = event.detail.type;
            // Optional: Play audio feedback
            // if (type === 'success') { new Audio('/sounds/success.mp3').play(); }
            // if (type === 'danger') { new Audio('/sounds/error.mp3').play(); }
        });
    </script>
    @endpush
</div>
