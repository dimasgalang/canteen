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

    public function processScan()
    {
        if (empty(trim($this->barcode))) {
            return;
        }

        try {
            $exploding = explode('_', $this->barcode);
            $npk = $exploding[0] ?? '';
            $name = $exploding[1] ?? '';
            $dept = $exploding[2] ?? null;

            if (empty($npk) || empty($name)) {
                $this->showFlash('danger', 'Invalid barcode format!');
                $this->resetInput();
                return;
            }

            // Time-based validation - Check FIRST to avoid unnecessary DB calls
            $now = Carbon::now();
            $today = Carbon::today();
            
            // Define time windows
            $firstBreakStart = $today->copy()->setTime(11, 30, 0);
            $firstBreakEnd = $today->copy()->setTime(14, 0, 0);
            $overtimeStart = $today->copy()->setTime(16, 30, 0);
            $overtimeEnd = $today->copy()->setTime(18, 0, 0);

            // Check if we are in a valid scanning period
            $isFirstBreak = $now >= $firstBreakStart && $now < $firstBreakEnd;
            $isOvertimeBreak = $now >= $overtimeStart && $now <= $overtimeEnd;

            // Fail fast if outside valid times
            if ($now < $firstBreakStart) {
                $this->showFlash('warning', 'Belum masuk waktu istirahat ke-1!');
                $this->resetInput();
                return;
            }

            if ($now >= $firstBreakEnd && $now < $overtimeStart) {
                $this->showFlash('warning', 'Belum masuk waktu istirahat ke-2!');
                $this->resetInput();
                return;
            }

            if ($now > $overtimeEnd) {
                $this->showFlash('warning', 'Waktu istirahat sudah selesai!');
                $this->resetInput();
                return;
            }

            // Only check employee if time is valid
            $checkEmployee = DB::connection('sqlsrv')
                ->table('BIODATA')
                ->where('NPK', '=', $npk)
                ->exists(); // Use exists() instead of get() -> count() for speed

            if (!$checkEmployee) {
                $this->showFlash('danger', "Employee {$npk} - {$name} has been resign!");
                $this->resetInput();
                return;
            }

            // Determine model based on canteen number
            $model = $this->canteenNo == 1 ? Canteen::class : CanteenTwo::class;
            $otherCanteenName = $this->canteenNo == 1 ? 'canteen 2' : 'canteen 1';

            // Check existing scans ONLY for the current active period
            if ($isFirstBreak) {
                $checkExistFirst = Canteen::where('created_at', '>=', $firstBreakStart)
                    ->where('created_at', '<', $firstBreakEnd)
                    ->where('npk', $npk)
                    ->exists();

                $checkExistSecond = CanteenTwo::where('created_at', '>=', $firstBreakStart)
                    ->where('created_at', '<', $firstBreakEnd)
                    ->where('npk', $npk)
                    ->exists();

                $thisCanteenExists = $this->canteenNo == 1 ? $checkExistFirst : $checkExistSecond;
                $otherCanteenExists = $this->canteenNo == 1 ? $checkExistSecond : $checkExistFirst;

                if (!$thisCanteenExists && !$otherCanteenExists) {
                    $this->createScanRecord($model, $npk, $name, $dept);
                    $this->showFlash('success', "Employee {$npk} - {$name} successfully scanned!");
                    $this->emit('refreshTable');
                    $this->resetInput();
                    return;
                }
                
                if ($otherCanteenExists) {
                    $this->showFlash('danger', "Employee {$npk} - {$name} already scanned in {$otherCanteenName}!");
                    $this->resetInput();
                    return;
                }
                
                $this->showFlash('danger', "Employee {$npk} - {$name} already scanned!");
                $this->resetInput();
                return;
            }

            if ($isOvertimeBreak) {
                $checkLemburFirst = Canteen::where('created_at', '>=', $overtimeStart)
                    ->where('created_at', '<', $overtimeEnd)
                    ->where('npk', $npk)
                    ->exists();

                $checkLemburSecond = CanteenTwo::where('created_at', '>=', $overtimeStart)
                    ->where('created_at', '<', $overtimeEnd)
                    ->where('npk', $npk)
                    ->exists();

                $thisCanteenLembur = $this->canteenNo == 1 ? $checkLemburFirst : $checkLemburSecond;
                $otherCanteenLembur = $this->canteenNo == 1 ? $checkLemburSecond : $checkLemburFirst;

                if (!$thisCanteenLembur && !$otherCanteenLembur) {
                    $this->createScanRecord($model, $npk, $name, $dept);
                    $this->showFlash('success', "Employee {$npk} - {$name} successfully scanned!");
                    $this->emit('refreshTable');
                    $this->resetInput();
                    return;
                }
                
                if ($otherCanteenLembur) {
                    $this->showFlash('danger', "Employee {$npk} - {$name} already scanned in {$otherCanteenName}!");
                    $this->resetInput();
                    return;
                }
                
                $this->showFlash('danger', "Employee {$npk} - {$name} already scanned!");
                $this->resetInput();
                return;
            }

        } catch (Exception $e) {
            $this->showFlash('danger', 'Invalid input, please check the barcode data!');
            $this->resetInput();
        }
    }

    private function createScanRecord($model, $npk, $name, $dept)
    {
        $model::create([
            'canteen_no' => $this->canteenNo,
            'npk' => $npk,
            'name' => $name,
            'dept' => $dept,
            'date' => Carbon::now()
        ]);
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
