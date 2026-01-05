<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Canteen;
use App\Models\CanteenTwo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class ScannerComponent extends Component
{
    public $barcode = '';
    public $canteenNo;
    
    // Flash message properties
    public $message = '';
    public $messageType = ''; // success, danger, warning, info
    public $showMessage = false;

    protected $listeners = ['clearMessage', 'focusInput'];

    public function mount($canteenNo = 1)
    {
        $this->canteenNo = $canteenNo;
    }

    public function updatedBarcode()
    {
        if (!empty(trim($this->barcode))) {
            $this->processScan();
        }
    }

    /**
     * The main entry point for processing a scanned barcode.
     */
    public function processScan()
    {
        $this->barcode = trim($this->barcode);
        if (empty($this->barcode)) {
            return;
        }

        try {
            // 1. Validate Barcode Format
            $barcodeData = $this->validateBarcode();
            if (!$barcodeData) return;

            // 2. Validate Time Windows
            $timeData = $this->validateTimeSlot();
            if (!$timeData) return;

            // 3. Verify Employee Status (Cached)
            if (!$this->isEmployeeActive($barcodeData['npk'], $barcodeData['name'])) return;

            // 4. Check for Duplicate Scans
            if ($this->hasRecentScan($barcodeData['npk'], $barcodeData['name'], $timeData)) return;

            // 5. Success: Save and Broadcast
            $this->saveScanRecord($barcodeData);
            $this->handleSuccess($barcodeData['npk'], $barcodeData['name']);

        } catch (Exception $e) {
            $this->showFlash('danger', 'Error: ' . $e->getMessage());
            $this->resetInput();
        }
    }

    /**
     * Ensures the barcode has the correct NPK_Name_Dept format.
     */
    private function validateBarcode()
    {
        if (!str_contains($this->barcode, '_')) {
            if ($this->barcode !== '') {
                $this->showFlash('danger', 'Invalid barcode format! Missing underscore.');
                $this->resetInput();
            }
            return null;
        }

        $parts = explode('_', $this->barcode);
        $data = [
            'npk'  => $parts[0] ?? '',
            'name' => $parts[1] ?? '',
            'dept' => $parts[2] ?? '',
        ];

        if (empty($data['npk']) || empty($data['name'])) {
            $this->showFlash('danger', 'Invalid barcode format! NPK and Name are required.');
            $this->resetInput();
            return null;
        }

        return $data;
    }

    /**
     * Checks if the current time is within allowed scanning windows.
     */
    private function validateTimeSlot()
    {
        $now = Carbon::now();
        $today = Carbon::today();
        
        $slots = [
            'lunch'  => ['start' => $today->copy()->setTime(11, 30, 0), 'end' => $today->copy()->setTime(14, 0, 0)],
            'dinner' => ['start' => $today->copy()->setTime(16, 30, 0), 'end' => $today->copy()->setTime(18, 0, 0)],
        ];

        if ($now < $slots['lunch']['start']) {
            $this->showFlash('warning', 'Belum masuk waktu istirahat ke-1!');
            $this->resetInput();
            return null;
        }

        if ($now >= $slots['lunch']['end'] && $now < $slots['dinner']['start']) {
            $this->showFlash('warning', 'Belum masuk waktu istirahat ke-2!');
            $this->resetInput();
            return null;
        }

        if ($now > $slots['dinner']['end']) {
            $this->showFlash('warning', 'Waktu scanning sudah berakhir!');
            $this->resetInput();
            return null;
        }

        $isLunch = $now->between($slots['lunch']['start'], $slots['lunch']['end']);
        return [
            'start' => $isLunch ? $slots['lunch']['start'] : $slots['dinner']['start'],
            'end'   => $isLunch ? $slots['lunch']['end']   : $slots['dinner']['end'],
            'now'   => $now
        ];
    }

    /**
     * Verifies if the employee is still active in the BIODATA system.
     */
    private function isEmployeeActive($npk, $name)
    {
        $exists = \Illuminate\Support\Facades\Cache::remember("emp_exists_{$npk}", 86400, function() use ($npk) {
            return DB::connection('sqlsrv')->table('BIODATA')->where('NPK', $npk)->exists();
        });

        if (!$exists) {
            $this->showFlash('danger', "Employee {$npk} - {$name} has been resign!");
            $this->resetInput();
            return false;
        }

        return true;
    }

    /**
     * Prevents multiple scans within the same time window across all canteens.
     */
    private function hasRecentScan($npk, $name, $timeData)
    {
        $range = [$timeData['start'], $timeData['end']];
        
        $alreadyScanned = DB::table('canteen')->where('npk', $npk)->whereBetween('created_at', $range)->exists() ||
                         DB::table('canteen_twos')->where('npk', $npk)->whereBetween('created_at', $range)->exists();

        if ($alreadyScanned) {
            $this->showFlash('danger', "Employee {$npk} - {$name} already scanned!");
            $this->resetInput();
            return true;
        }

        return false;
    }

    /**
     * Persists the scan record to the database.
     */
    private function saveScanRecord($data)
    {
        $table = $this->canteenNo == 1 ? 'canteen' : 'canteen_twos';
        $now = Carbon::now();

        DB::table($table)->insert([
            'canteen_no' => $this->canteenNo,
            'npk'        => $data['npk'],
            'name'       => $data['name'],
            'dept'       => $data['dept'],
            'date'       => $now,
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }

    /**
     * Handles UI feedback and real-time broadcasting after a successful scan.
     */
    private function handleSuccess($npk, $name)
    {
        $this->showFlash('success', "Employee {$npk} - {$name} successfully scanned!");
        
        // Broadcast for multi-device sync
        broadcast(new \App\Events\ScanProcessed($this->canteenNo));
        
        $this->emit('refreshTable');
        $this->resetInput();
    }

    private function showFlash($type, $message)
    {
        $this->messageType = $type;
        $this->message = $message;
        $this->showMessage = true;
        
        // Dispatch browser event for sound/animation
        $this->dispatchBrowserEvent('scan-result', ['type' => $type]);
    }

    private function resetInput()
    {
        $this->reset('barcode');
        $this->dispatchBrowserEvent('clear-barcode');
    }

    public function clearMessage()
    {
        $this->showMessage = false;
        $this->message = '';
        $this->messageType = '';
    }

    public function render()
    {
        return view('livewire.scanner-component')
            ->layout('layout.app-livewire');
    }
}
