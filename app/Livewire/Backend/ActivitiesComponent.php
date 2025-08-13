<?php

namespace App\Livewire\Backend;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ActivitiesComponent extends Component
{
    use WithPagination;

    // Search and Filter Properties
    public string $search = '';
    public string $activityType = '';
    public string $userFilter = '';
    public string $dateRange = 'today';
    public string $sortField = 'created_at';
    public string $sortDirection = 'DESC';
    public int $perPage = 15;

    // Activity types for filtering
    public array $activityTypes = [
        'user_login' => 'User Login',
        'user_logout' => 'User Logout',
        'user_created' => 'User Created',
        'user_updated' => 'User Updated',
        'user_deleted' => 'User Deleted',
        'shop_created' => 'Shop Created',
        'shop_updated' => 'Shop Updated',
        'shop_deleted' => 'Shop Deleted',
        'order_created' => 'Order Created',
        'order_updated' => 'Order Updated',
        'order_deleted' => 'Order Deleted',
        'role_assigned' => 'Role Assigned',
        'role_removed' => 'Role Removed',
        'email_verified' => 'Email Verified',
        'password_changed' => 'Password Changed',
        'profile_updated' => 'Profile Updated',
    ];

    protected array $queryString = [
        'search' => ['except' => ''],
        'activityType' => ['except' => ''],
        'userFilter' => ['except' => ''],
        'dateRange' => ['except' => 'today'],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'DESC'],
        'perPage' => ['except' => 15],
    ];

    /**
     * Update search and reset pagination
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Update activity type filter and reset pagination
     */
    public function updatedActivityType(): void
    {
        $this->resetPage();
    }

    /**
     * Update user filter and reset pagination
     */
    public function updatedUserFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Update date range filter and reset pagination
     */
    public function updatedDateRange(): void
    {
        $this->resetPage();
    }

    /**
     * Update per page and reset pagination
     */
    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Sort by field
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'ASC' ? 'DESC' : 'ASC';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'ASC';
        }

        $this->resetPage();
    }

    /**
     * Clear all filters
     */
    public function clearAllFilters(): void
    {
        $this->search = '';
        $this->activityType = '';
        $this->userFilter = '';
        $this->dateRange = 'today';
        $this->resetPage();
    }

    /**
     * Get activities query with filters
     */
    private function getActivitiesQuery()
    {
        $query = DB::table('activity_log')->select([
            'activity_log.*',
            'users.name as user_name',
            'users.email as user_email',
            'users.profile_photo_path'
        ])
        ->leftJoin('users', 'activity_log.causer_id', '=', 'users.id');

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('activity_log.description', 'like', '%' . $this->search . '%')
                  ->orWhere('activity_log.subject_type', 'like', '%' . $this->search . '%')
                  ->orWhere('activity_log.log_name', 'like', '%' . $this->search . '%')
                  ->orWhere('users.name', 'like', '%' . $this->search . '%')
                  ->orWhere('users.email', 'like', '%' . $this->search . '%');
            });
        }

        // Apply activity type filter
        if ($this->activityType) {
            $query->where('activity_log.log_name', $this->activityType);
        }

        // Apply user filter
        if ($this->userFilter) {
            $query->where('activity_log.causer_id', $this->userFilter);
        }

        // Apply date range filter
        $this->applyDateRangeFilter($query);

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

    /**
     * Apply date range filter to query
     */
    private function applyDateRangeFilter($query): void
    {
        switch ($this->dateRange) {
            case 'today':
                $query->whereDate('activity_log.created_at', today());
                break;
            case 'yesterday':
                $query->whereDate('activity_log.created_at', today()->subDay());
                break;
            case 'last_7_days':
                $query->where('activity_log.created_at', '>=', now()->subDays(7));
                break;
            case 'last_30_days':
                $query->where('activity_log.created_at', '>=', now()->subDays(30));
                break;
            case 'this_month':
                $query->whereYear('activity_log.created_at', now()->year)
                      ->whereMonth('activity_log.created_at', now()->month);
                break;
            case 'last_month':
                $query->whereYear('activity_log.created_at', now()->subMonth()->year)
                      ->whereMonth('activity_log.created_at', now()->subMonth()->month);
                break;
            case 'this_year':
                $query->whereYear('activity_log.created_at', now()->year);
                break;
        }
    }

    /**
     * Get paginated activities
     */
    private function getActivities()
    {
        return $this->getActivitiesQuery()->paginate($this->perPage);
    }

    /**
     * Get available users for filter
     */
    private function getUsers()
    {
        return User::orderBy('name')->get();
    }

    /**
     * Get activity statistics
     */
    private function getActivityStats(): array
    {
        $today = DB::table('activity_log')->whereDate('created_at', today())->count();
        $thisWeek = DB::table('activity_log')->where('created_at', '>=', now()->startOfWeek())->count();
        $thisMonth = DB::table('activity_log')->where('created_at', '>=', now()->startOfMonth())->count();
        $total = DB::table('activity_log')->count();

        return [
            'today' => $today,
            'this_week' => $thisWeek,
            'this_month' => $thisMonth,
            'total' => $total,
        ];
    }

    /**
     * Get recent activity types
     */
    private function getRecentActivityTypes(): array
    {
        return DB::table('activity_log')
            ->select('log_name', DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('log_name')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * Get most active users
     */
    private function getMostActiveUsers(): array
    {
        return DB::table('activity_log')
            ->select('users.name', 'users.email', DB::raw('count(activity_log.id) as activity_count'))
            ->join('users', 'activity_log.causer_id', '=', 'users.id')
            ->where('activity_log.created_at', '>=', now()->subDays(30))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('activity_count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    #[Layout('layouts.backend')]
    #[Title('Activities')]
    public function render()
    {
        return view('livewire.backend.activities-component', [
            'activities' => $this->getActivities(),
            'users' => $this->getUsers(),
            'stats' => $this->getActivityStats(),
            'recentActivityTypes' => $this->getRecentActivityTypes(),
            'mostActiveUsers' => $this->getMostActiveUsers(),
        ]);
    }
}
