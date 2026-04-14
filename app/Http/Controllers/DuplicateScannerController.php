<?php

namespace App\Http\Controllers;

use App\Models\DuplicateScanner;
use App\Models\Canteen;
use App\Models\CanteenTwo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DuplicateScannerController extends Controller
{
    /**
     * Display the duplicate scanner index page.
     */
    public function index()
    {
        return view('scanner.duplicate-scanner');
    }

    /**
     * Return DataTables JSON for duplicate scanner records.
     */
    public function data(Request $request)
    {
        $query = DuplicateScanner::query();

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('date_formated', function ($row) {
                return $row->date
                    ? Carbon::parse($row->date)->format('d-m-Y')
                    : '-';
            })
            ->addColumn('created_at_formated', function ($row) {
                return $row->created_at
                    ? Carbon::parse($row->created_at)->format('d-m-Y H:i:s')
                    : '-';
            })
            ->addColumn('action', function ($row) {
                return '<input type="checkbox" class="select-row" value="' . $row->id . '">';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Bulk move duplicate scanner records to the correct canteen table.
     *
     * Logic:
     * - For each selected duplicate record, check if the employee's scan exists
     *   in the "already_scan_canteen_number" canteen table.
     * - If exists → delete from old canteen, insert into "need_to_scan" canteen,
     *   then remove from duplicate_scanner table.
     * - If not exists → keep in duplicate_scanner (skip).
     */
    public function bulkMove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:duplicate_scanner,id',
        ]);

        $moved = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            $records = DuplicateScanner::whereIn('id', $request->ids)->get();

            foreach ($records as $record) {
                $npk = $record->npk;
                $name = $record->name;
                $date = $record->date;
                $alreadyScanCanteen = $record->already_scan_canteen_number;
                $needToScanCanteen = $record->need_to_scan_canteen_number;

                // Determine the source model (where the employee already scanned)
                $sourceModel = $alreadyScanCanteen == 1 ? new Canteen() : new CanteenTwo();

                // Find the existing scan in the source canteen table
                $existingScan = $sourceModel->where('npk', $npk)
                    ->whereDate('date', Carbon::parse($date)->toDateString())
                    ->first();

                if ($existingScan) {
                    // Determine the target model (where the employee needs to scan)
                    $targetModel = $needToScanCanteen == 1 ? new Canteen() : new CanteenTwo();

                    // Insert into target canteen table
                    $targetModel->create([
                        'canteen_no' => $needToScanCanteen,
                        'npk' => $npk,
                        'name' => $name,
                        'dept' => $existingScan->dept,
                        'date' => $existingScan->date,
                    ]);

                    // Delete from source canteen table
                    $existingScan->delete();

                    // Remove from duplicate_scanner
                    $record->delete();

                    $moved++;
                } else {
                    // Can't swap — source scan not found, keep in duplicate
                    $skipped++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Bulk move completed. Moved: {$moved}, Skipped: {$skipped}.",
                'moved' => $moved,
                'skipped' => $skipped,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Bulk move failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a duplicate scanner record.
     */
    public function destroy($id)
    {
        try {
            $record = DuplicateScanner::findOrFail($id);
            $record->delete();

            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete record: ' . $e->getMessage(),
            ], 500);
        }
    }
}
