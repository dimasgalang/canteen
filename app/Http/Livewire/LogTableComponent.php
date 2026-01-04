<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Canteen;
use App\Models\CanteenTwo;
use Carbon\Carbon;

class LogTableComponent extends Component
{
    use WithPagination;

    public $canteenNo;
    public $search = '';
    public $perPage = 15;

    protected $listeners = ['refreshTable' => '$refresh'];
    protected $paginationTheme = 'bootstrap';

    // Reset pagination when search changes
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 15],
    ];

    public function mount($canteenNo = 1)
    {
        $this->canteenNo = $canteenNo;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getTotalCountProperty()
    {
        $model = $this->canteenNo == 1 ? Canteen::class : CanteenTwo::class;
        return $model::whereDate('created_at', Carbon::today())->count();
    }

    public function render()
    {
        $model = $this->canteenNo == 1 ? Canteen::class : CanteenTwo::class;
        
        $logs = $model::whereDate('created_at', Carbon::today())
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('npk', 'like', '%'.$this->search.'%')
                      ->orWhere('name', 'like', '%'.$this->search.'%')
                      ->orWhere('dept', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.log-table-component', [
            'logs' => $logs,
            'totalCount' => $this->totalCount
        ]);
    }
}
