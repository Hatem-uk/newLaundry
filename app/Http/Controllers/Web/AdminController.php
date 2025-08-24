<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Laundry;
use App\Models\Service;
use App\Models\Agent;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Helper;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        try {
            $stats = $this->getDashboardStats();
            return view('admin.dashboard', compact('stats'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show users management page
     */
    public function users()
    {
        try {
            $users = User::with(['customer', 'admin', 'agent'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            return view('admin.users', compact('users'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show agents management page
     */
    public function agents()
    {
        try {
            $agents = Agent::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            return view('admin.agents', compact('agents'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show laundries management page
     */
    public function laundries()
    {
        try {
            $laundries = Laundry::with(['user', 'city'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            $counts = $this->getLaundryCounts();
                
            return view('admin.laundries', compact('laundries', 'counts'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show services management page
     */
    public function services()
    {
        try {
            $services = Service::with(['provider' => function($query) {
                    $query->with(['laundry', 'agent', 'admin']);
                }])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            return view('admin.services', compact('services'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show orders management page
     */
    public function orders()
    {
        try {
            $orders = Order::with(['user', 'recipient', 'provider', 'target'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            return view('admin.orders', compact('orders'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show order tracking page
     */
    public function tracking()
    {
        try {
            $orders = Order::with(['user', 'provider', 'target'])
                ->whereIn('status', ['pending', 'in_process', 'completed'])
                ->orderBy('updated_at', 'desc')
                ->paginate(15);
                
            $counts = $this->getOrderStatusCounts();
                
            return view('admin.tracking', compact('orders', 'counts'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    // ==================== USER MANAGEMENT FUNCTIONS ====================

    /**
     * View user details
     */
    public function viewUser(User $user)
    {
        try {
            $user->load(['customer', 'admin', 'agent']);
            return view('admin.users.view', compact('user'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show edit user form
     */
    public function editUser(User $user)
    {
        try {
            $user->load(['customer', 'admin', 'agent']);
            return view('admin.users.edit', compact('user'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        try {
            $validated = $this->validateUserUpdate($request, $user);
            $this->updateUserData($user, $validated);
            
            $successMessage = Helper::messageSuccess('User updated');
            return redirect()->route('admin.users.view', $user)->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->withInput()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        try {
            $validated = $this->validateUserCreation($request);
            $user = $this->createUserAccount($validated);
            $this->createUserProfile($user, $validated);
            $this->sendRegistrationEmails($user);

            $successMessage = Helper::messageSuccess('User created');
            return redirect()->route('admin.users')->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->withInput()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        try {
            $this->validateUserDeletion($user);
            $userName = $user->name;
            $user->delete();

            $successMessage = Helper::messageSuccess("User '{$userName}' deleted");
            return redirect()->route('admin.users')->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    // ==================== AGENT MANAGEMENT FUNCTIONS ====================

    /**
     * View agent details
     */
    public function viewAgent(Agent $agent)
    {
        try {
            $agent->load(['user', 'city']);
            return view('admin.agents.view', compact('agent'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show edit agent form
     */
    public function editAgent(Agent $agent)
    {
        try {
            $agent->load(['user', 'city']);
            return view('admin.agents.edit', compact('agent'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Update agent
     */
    public function updateAgent(Request $request, Agent $agent)
    {
        try {
            $validated = $this->validateAgentUpdate($request);
            $this->updateAgentData($agent, $validated);

            $successMessage = Helper::messageSuccess('Agent updated');
            return redirect()->route('admin.agents.view', $agent)->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->withInput()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Approve pending agent
     */
    public function approveAgent(Request $request, User $user)
    {
        try {
            $this->validateAgentApproval($user);
            $this->approveAgentUser($user);

            $successMessage = Helper::messageSuccess('Agent approved');
            return redirect()->back()->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Reject pending agent
     */
    public function rejectAgent(Request $request, User $user)
    {
        try {
            $this->validateAgentApproval($user);
            $this->rejectAgentUser($user);

            $successMessage = Helper::messageSuccess('Agent rejected');
            return redirect()->back()->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    // ==================== LAUNDRY MANAGEMENT FUNCTIONS ====================

    /**
     * View laundry details
     */
    public function viewLaundry(Laundry $laundry)
    {
        try {
            $laundry->load(['user', 'city']);
            $this->ensureTranslatableFields($laundry);
            
            $data = $this->getLaundryViewData($laundry);
            $laundryStats = $this->calculateLaundryStats($laundry);
            
            return view('admin.laundries.view', compact('laundry', 'data', 'laundryStats'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show edit laundry form
     */
    public function editLaundry(Laundry $laundry)
    {
        try {
            $laundry->load(['user', 'city']);
            $this->ensureTranslatableFields($laundry);
            
            $cities = \App\Models\City::all();
            foreach ($cities as $city) {
                $this->ensureTranslatableFields($city);
            }
            
            return view('admin.laundries.edit', compact('laundry', 'cities'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Update laundry information
     */
    public function updateLaundry(Request $request, Laundry $laundry)
    {
        try {
            $validated = $this->validateLaundryUpdate($request);
            $this->updateLaundryData($laundry, $validated);

            $successMessage = Helper::messageSuccess('Laundry updated');
            return redirect()->route('admin.laundries.view', $laundry)->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->withInput()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Approve pending laundry
     */
    public function approveLaundry(Request $request, Laundry $laundry)
    {
        try {
            $laundry->user->update(['status' => 'approved']);
            
            $successMessage = Helper::messageSuccess('Laundry approved');
            return redirect()->back()->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Reject pending laundry
     */
    public function rejectLaundry(Request $request, Laundry $laundry)
    {
        try {
            $laundry->user->update(['status' => 'rejected']);
            
            $successMessage = Helper::messageSuccess('Laundry rejected');
            return redirect()->back()->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show pending laundries
     */
    public function pendingLaundries()
    {
        try {
            $pendingLaundries = Laundry::whereHas('user', function($query) {
                $query->where('status', 'pending');
            })->with(['user', 'city'])->paginate(15);

            return view('admin.laundries.pending', compact('pendingLaundries'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show rejected laundries
     */
    public function rejectedLaundries()
    {
        try {
            $rejectedLaundries = Laundry::whereHas('user', function($query) {
                $query->where('status', 'rejected');
            })->with(['user', 'city'])->paginate(15);

            return view('admin.laundries.rejected', compact('rejectedLaundries'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show create laundry form
     */
    public function createLaundry()
    {
        try {
            $cities = \App\Models\City::all();
            return view('admin.laundries.create', compact('cities'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Store new laundry
     */
    public function storeLaundry(Request $request)
    {
        try {
            $validated = $this->validateLaundryCreation($request);
            $user = $this->createLaundryUser($validated);
            $laundry = $this->createLaundryProfile($user, $validated);

            $successMessage = Helper::messageSuccess('Laundry created');
            return redirect()->route('admin.laundries')->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->withInput()->with('error', $errorMessage['message']);
        }
    }

    // ==================== SERVICE MANAGEMENT FUNCTIONS ====================

    /**
     * View service details
     */
    public function viewService(Service $service)
    {
        try {
            return view('admin.services.view', compact('service'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show edit service form
     */
    public function editService(Service $service)
    {
        try {
            return view('admin.services.edit', compact('service'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Update service
     */
    public function updateService(Request $request, Service $service)
    {
        try {
            $validated = $this->validateServiceUpdate($request);
            $this->updateServiceData($service, $validated);

            $successMessage = Helper::messageSuccess('Service updated');
            return redirect()->route('admin.services.view', $service)->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->withInput()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Delete service
     */
    public function deleteService(Service $service)
    {
        try {
            $service->delete();
            
            $successMessage = Helper::messageSuccess('Service deleted');
            return redirect()->route('admin.services')->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    // ==================== ORDER MANAGEMENT FUNCTIONS ====================

    /**
     * View order details
     */
    public function viewOrder(Order $order)
    {
        try {
            return view('admin.orders.view', compact('order'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show edit order form
     */
    public function editOrder(Order $order)
    {
        try {
            return view('admin.orders.edit', compact('order'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Update order
     */
    public function updateOrder(Request $request, Order $order)
    {
        try {
            $validated = $this->validateOrderUpdate($request);
            $this->updateOrderData($order, $validated);

            $successMessage = Helper::messageSuccess('Order updated');
            return redirect()->route('admin.orders.view', $order)->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->withInput()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Delete order
     */
    public function deleteOrder(Order $order)
    {
        try {
            $order->delete();
            
            $successMessage = Helper::messageSuccess('Order deleted');
            return redirect()->route('admin.orders')->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $today = Carbon::now()->startOfDay();
        $yesterday = Carbon::yesterday()->startOfDay();
        $thisWeek = Carbon::now()->startOfWeek();
        $lastWeek = Carbon::now()->subWeek()->startOfWeek();
        $thisMonthStart = Carbon::now()->startOfMonth();
        $thisMonthEnd = Carbon::now()->endOfDay();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $todayOrders = Order::whereDate('created_at', $today)->count();
        $yesterdayOrders = Order::whereDate('created_at', $yesterday)->count();
        $ordersGrowth = $this->calculateGrowthPercentage($todayOrders, $yesterdayOrders);

        $thisMonthRevenue = $this->calculateMonthlyRevenue($thisMonthStart, $thisMonthEnd);
        $lastMonthRevenue = $this->calculateMonthlyRevenue($lastMonthStart, $lastMonthEnd);
        $revenueGrowth = $this->calculateGrowthPercentage($thisMonthRevenue, $lastMonthRevenue);

        $totalUsers = User::count();
        $thisWeekUsers = User::whereBetween('created_at', [$thisWeek, Carbon::now()])->count();
        $lastWeekUsers = User::whereBetween('created_at', [$lastWeek, $thisWeek->copy()->subSecond()])->count();
        $usersGrowth = $this->calculateGrowthPercentage($thisWeekUsers, $lastWeekUsers);

        $pendingOrders = Order::where('status', 'pending')->count();
        $yesterdayPending = Order::where('status', 'pending')->whereDate('created_at', $yesterday)->count();
        $pendingChange = $pendingOrders - $yesterdayPending;

        $recentOrders = $this->getRecentOrders();
        $monthlyRevenue = $this->getMonthlyRevenue();

        return [
            'today_orders' => $todayOrders,
            'orders_growth' => round($ordersGrowth, 1),
            'total_revenue' => $thisMonthRevenue,
            'revenue_growth' => round($revenueGrowth, 1),
            'total_users' => $totalUsers,
            'users_growth' => round($usersGrowth, 1),
            'pending_orders' => $pendingOrders,
            'pending_change' => $pendingChange,
            'this_month_revenue' => $thisMonthRevenue,
            'last_month_revenue' => $lastMonthRevenue,
            'total_orders' => Order::count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'in_process_orders' => Order::where('status', 'in_process')->count(),
            'total_laundries' => Laundry::count(),
            'total_customers' => Customer::count(),
            'total_agents' => Agent::count(),
            'recent_orders' => $recentOrders,
            'monthly_revenue' => $monthlyRevenue,
        ];
    }

    /**
     * Calculate growth percentage
     */
    private function calculateGrowthPercentage($current, $previous): float
    {
        if ($previous > 0) {
            return (($current - $previous) / $previous) * 100;
        } elseif ($current > 0) {
            return 100;
        }
        return 0;
    }

    /**
     * Calculate monthly revenue
     */
    private function calculateMonthlyRevenue($startDate, $endDate): float
    {
        return Invoice::where('status', 'paid')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('paid_at', [$startDate, $endDate])
                      ->orWhere(function($subQuery) use ($startDate, $endDate) {
                          $subQuery->whereNotNull('paid_at')
                                   ->whereBetween('updated_at', [$startDate, $endDate]);
                      });
            })
            ->sum('amount') ?? 0;
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders()
    {
        return Order::with(['user:id,name,email', 'provider:id,name,role'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($order) {
                $targetName = $this->getTargetName($order);
                return [
                    'id' => $order->id,
                    'price' => $order->price,
                    'target_name' => $targetName,
                    'target_type' => $order->target_type,
                    'status' => $order->status,
                    'customer_name' => $order->user->name ?? 'Unknown',
                    'created_at' => $order->created_at
                ];
            });
    }

    /**
     * Get target name for order
     */
    private function getTargetName($order): string
    {
        if ($order->target_type === 'service') {
            $service = Service::find($order->target_id);
            if ($service) {
                return is_array($service->name) 
                    ? ($service->name['ar'] ?? $service->name['en'] ?? 'خدمة')
                    : $service->name;
            }
        } elseif ($order->target_type === 'package') {
            $package = Package::find($order->target_id);
            if ($package) {
                return is_array($package->name) 
                    ? ($package->name['ar'] ?? $package->name['en'] ?? 'باقة')
                    : $package->name;
            }
        }
        return 'Unknown';
    }

    /**
     * Get monthly revenue breakdown
     */
    private function getMonthlyRevenue()
    {
        return Invoice::where('status', 'paid')
            ->selectRaw('MONTH(paid_at) as month, YEAR(paid_at) as year, SUM(amount) as total')
            ->whereBetween('paid_at', [Carbon::now()->subMonths(11)->startOfMonth(), Carbon::now()])
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * Get laundry counts
     */
    private function getLaundryCounts(): array
    {
        return [
            'pendingCount' => Laundry::whereHas('user', function($query) {
                $query->where('status', 'pending');
            })->count(),
            'rejectedCount' => Laundry::whereHas('user', function($query) {
                $query->where('status', 'rejected');
            })->count()
        ];
    }

    /**
     * Get order status counts
     */
    private function getOrderStatusCounts(): array
    {
        return [
            'pendingCount' => Order::where('status', 'pending')->count(),
            'inProgressCount' => Order::where('status', 'in_process')->count(),
            'onWayCount' => Order::where('status', 'on_way')->count(),
            'completedCount' => Order::where('status', 'completed')->count()
        ];
    }

    // ==================== VALIDATION METHODS ====================

    /**
     * Validate user update
     */
    private function validateUserUpdate(Request $request, User $user): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,agent,customer,laundry,worker',
            'status' => 'required|in:pending,approved,rejected',
            'coins' => 'nullable|integer|min:0',
            'fcm_tocken' => 'nullable|string|max:255',
            'email_verified_action' => 'nullable|in:verify,unverify',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
    }

    /**
     * Validate user creation
     */
    private function validateUserCreation(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,laundry,agent,worker,customer',
            'status' => 'required|in:pending,approved,rejected',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string|max:500',
            'laundry_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'laundry_id' => 'nullable|exists:laundries,id',
            'coins' => 'nullable|integer|min:0',
            'password' => 'required|string|min:6|confirmed',
            'fcm_tocken' => 'nullable|string|max:255',
            'email_verified' => 'nullable|boolean',
        ]);
    }

    /**
     * Validate agent update
     */
    private function validateAgentUpdate(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->agent->user_id,
            'phone' => 'nullable|string|max:20',
            'license_number' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:pending,approved,rejected',
            'is_active' => 'required|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
    }

    /**
     * Validate laundry update
     */
    private function validateLaundryUpdate(Request $request): array
    {
        return $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'address_ar' => 'required|string|max:500',
            'address_en' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'city_id' => 'required|exists:cities,id',
            'status' => 'required|in:online,offline,maintenance',
            'is_active' => 'required|boolean',
            'delivery_available' => 'required|boolean',
            'pickup_available' => 'required|boolean',
            'working_hours' => 'nullable|array',
        ]);
    }

    /**
     * Validate laundry creation
     */
    private function validateLaundryCreation(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|string|max:500',
            'address_en' => 'required|string|max:500',
            'delivery_available' => 'required|boolean',
            'pickup_available' => 'required|boolean',
            'working_hours' => 'required|array',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    }

    /**
     * Validate service update
     */
    private function validateServiceUpdate(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'coins' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean'
        ]);
    }

    /**
     * Validate order update
     */
    private function validateOrderUpdate(Request $request): array
    {
        return $request->validate([
            'status' => 'required|in:pending,in_process,completed,canceled',
            'price' => 'required|numeric|min:0',
            'coins' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500'
        ]);
    }

    // ==================== DATA UPDATE METHODS ====================

    /**
     * Update user data
     */
    private function updateUserData(User $user, array $validated): void
    {
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'fcm_tocken' => $validated['fcm_tocken'],
        ];

        if (isset($validated['email_verified_action'])) {
            if ($validated['email_verified_action'] === 'verify') {
                $userData['email_verified_at'] = now();
            } elseif ($validated['email_verified_action'] === 'unverify') {
                $userData['email_verified_at'] = null;
            }
        }

        if (isset($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        if ($user->role === 'customer' && isset($validated['coins'])) {
            if ($user->customer) {
                $user->customer->update(['coins' => $validated['coins']]);
            } else {
                $user->customer()->create([
                    'coins' => $validated['coins'],
                    'phone' => $validated['phone'],
                ]);
            }
        }
    }

    /**
     * Update agent data
     */
    private function updateAgentData(Agent $agent, array $validated): void
    {
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'status' => $validated['status'],
        ];

        if (isset($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $agent->user->update($userData);

        $agent->update([
            'license_number' => $validated['license_number'],
            'city_id' => $validated['city_id'],
            'address' => $validated['address'],
            'is_active' => $validated['is_active'],
        ]);
    }

    /**
     * Update laundry data
     */
    private function updateLaundryData(Laundry $laundry, array $validated): void
    {
        $laundry->update([
            'name' => [
                'ar' => $validated['name_ar'],
                'en' => $validated['name_en']
            ],
            'address' => [
                'ar' => $validated['address_ar'],
                'en' => $validated['address_en']
            ],
            'phone' => $validated['phone'],
            'city_id' => $validated['city_id'],
            'status' => $validated['status'],
            'is_active' => $validated['is_active'],
            'delivery_available' => $validated['delivery_available'],
            'pickup_available' => $validated['pickup_available'],
            'working_hours' => $validated['working_hours'],
        ]);
    }

    /**
     * Update service data
     */
    private function updateServiceData(Service $service, array $validated): void
    {
        $service->update([
            'name' => Helper::translateData(request(), 'name', 'name_en'),
            'description' => Helper::translateData(request(), 'description', 'description_en'),
            'price' => $validated['price'],
            'coins' => $validated['coins'],
            'is_active' => $validated['is_active']
        ]);
    }

    /**
     * Update order data
     */
    private function updateOrderData(Order $order, array $validated): void
    {
        $order->update([
            'status' => $validated['status'],
            'price' => $validated['price'],
            'coins' => $validated['coins'],
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes']
        ]);
    }

    // ==================== USER CREATION METHODS ====================

    /**
     * Create user account
     */
    private function createUserAccount(array $validated): User
    {
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'password' => Hash::make($validated['password']),
            'fcm_tocken' => $validated['fcm_tocken'],
        ];

        if (isset($validated['email_verified']) && $validated['email_verified']) {
            $userData['email_verified_at'] = now();
        }

        return User::create($userData);
    }

    /**
     * Create user profile based on role
     */
    private function createUserProfile(User $user, array $validated): void
    {
        switch ($validated['role']) {
            case 'laundry':
                $this->createLaundryProfile($user, $validated);
                break;
            case 'worker':
                $this->createWorkerProfile($user, $validated);
                break;
            case 'customer':
                $this->createCustomerProfile($user, $validated);
                break;
            case 'admin':
                $this->createAdminProfile($user, $validated);
                break;
            case 'agent':
                $this->createAgentProfile($user, $validated);
                break;
        }
    }

    /**
     * Create laundry profile
     */
    private function createLaundryProfile(User $user, array $validated): Laundry
    {
        $logoPath = null;
        if (request()->hasFile('logo')) {
            $logoPath = Helper::uploadFile(
                request()->file('logo'),
                'laundry_logos',
                ['jpeg', 'png', 'jpg', 'gif'],
                2048
            );
        }

        return Laundry::create([
            'user_id' => $user->id,
            'city_id' => $validated['city_id'],
            'name' => Helper::translateData(request(), 'name', 'name_en'),
            'address' => Helper::translateData(request(), 'address', 'address_en'),
            'phone' => $validated['phone'],
            'logo' => $logoPath,
            'delivery_available' => $validated['delivery_available'],
            'pickup_available' => $validated['pickup_available'],
            'working_hours' => $validated['working_hours'],
            'is_active' => true
        ]);
    }

    /**
     * Create worker profile
     */
    private function createWorkerProfile(User $user, array $validated): void
    {
        if (!isset($validated['laundry_id'])) {
            throw new \Exception(__('dashboard.laundry_id_required_for_workers'));
        }
        
        $user->worker()->create([
            'laundry_id' => $validated['laundry_id'],
            'position' => $validated['position'] ?? 'Worker',
            'salary' => $validated['salary'] ?? 0,
            'phone' => $validated['phone'] ?? null,
            'status' => 'pending',
            'is_active' => true
        ]);
    }

    /**
     * Create customer profile
     */
    private function createCustomerProfile(User $user, array $validated): void
    {
        $user->customer()->create([
            'address' => $validated['address'] ? [
                'ar' => $validated['address'],
                'en' => $validated['address']
            ] : '',
            'phone' => $validated['phone'] ?? '',
            'city_id' => $validated['city_id'],
            'coins' => $validated['coins'] ?? 100
        ]);
    }

    /**
     * Create admin profile
     */
    private function createAdminProfile(User $user, array $validated): void
    {
        $user->admin()->create([
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => true
        ]);
    }

    /**
     * Create agent profile
     */
    private function createAgentProfile(User $user, array $validated): void
    {
        $user->agent()->create([
            'name' => [
                'ar' => $user->name,
                'en' => $user->name
            ],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ? [
                'ar' => $validated['address'],
                'en' => $validated['address']
            ] : null,
            'city_id' => $validated['city_id'],
            'is_active' => true,
            'status' => 'online'
        ]);
    }

    // ==================== LAUNDRY CREATION METHODS ====================

    /**
     * Create laundry user account
     */
    private function createLaundryUser(array $validated): User
    {
        return User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'laundry',
            'status' => 'approved'
        ]);
    }

    // ==================== AGENT MANAGEMENT METHODS ====================

    /**
     * Validate agent approval
     */
    private function validateAgentApproval(User $user): void
    {
        if ($user->role !== 'agent') {
            throw new \Exception('User is not an agent');
        }

        if ($user->status !== 'pending') {
            throw new \Exception('Agent is not pending approval');
        }
    }

    /**
     * Approve agent user
     */
    private function approveAgentUser(User $user): void
    {
        $user->update(['status' => 'approved']);

        if ($user->agent) {
            $user->agent->update([
                'is_active' => true,
                'status' => 'online'
            ]);
        }
    }

    /**
     * Reject agent user
     */
    private function rejectAgentUser(User $user): void
    {
        $user->update(['status' => 'rejected']);

        if ($user->agent) {
            $user->agent->update([
                'is_active' => false,
                'status' => 'offline'
            ]);
        }
    }

    // ==================== LAUNDRY VIEW METHODS ====================

    /**
     * Get laundry view data
     */
    private function getLaundryViewData(Laundry $laundry): array
    {
        $services = Service::where('provider_id', $laundry->user_id)
            ->with(['orders'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($services as $service) {
            $this->ensureTranslatableFields($service);
        }

        $orders = Order::where('provider_id', $laundry->user_id)
            ->with(['user', 'target'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        foreach ($orders as $order) {
            if ($order->target && method_exists($order->target, 'getTranslatableAttributes')) {
                $this->ensureTranslatableFields($order->target);
            }
        }

        $servicePurchases = Order::where('provider_id', $laundry->user_id)
            ->where('target_type', 'service')
            ->with(['user', 'target'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($servicePurchases as $purchase) {
            if ($purchase->target && method_exists($purchase->target, 'getTranslatableAttributes')) {
                $this->ensureTranslatableFields($purchase->target);
            }
        }

        return [
            'services' => $services,
            'orders' => $orders,
            'servicePurchases' => $servicePurchases
        ];
    }

    // ==================== UTILITY METHODS ====================

    /**
     * Get default working hours
     */
    private function getDefaultWorkingHours(): array
    {
        return [
            'monday' => ['09:00', '18:00'],
            'tuesday' => ['09:00', '18:00'],
            'wednesday' => ['09:00', '18:00'],
            'thursday' => ['09:00', '18:00'],
            'friday' => ['09:00', '18:00'],
            'saturday' => ['09:00', '18:00'],
            'sunday' => ['09:00', '18:00']
        ];
    }

    /**
     * Validate user deletion
     */
    private function validateUserDeletion(User $user): void
    {
        if ($user->id === Auth::id()) {
            throw new \Exception('لا يمكنك حذف حسابك الخاص');
        }
    }

    /**
     * Ensure translatable fields are properly formatted as arrays
     */
    private function ensureTranslatableFields($model): void
    {
        if (!$model || !method_exists($model, 'getTranslatableAttributes')) {
            return;
        }

        $translatableAttributes = $model->getTranslatableAttributes();
        
        foreach ($translatableAttributes as $attribute) {
            $value = $model->getAttribute($attribute);
            $rawValue = $model->getRawOriginal($attribute);
            
            if ((is_string($value) || is_null($value)) && !empty($rawValue)) {
                $locale = app()->getLocale();
                $newValue = [
                    $locale => $rawValue,
                    'en' => $rawValue,
                    'ar' => $rawValue
                ];
                $model->setAttribute($attribute, $newValue);
            }
        }
    }

    /**
     * Calculate laundry statistics
     */
    private function calculateLaundryStats($laundry): array
    {
        $today = now()->startOfDay();
        
        $todayOrders = Order::where('provider_id', $laundry->user_id)
            ->whereDate('created_at', $today)
            ->count();
            
        $totalOrders = Order::where('provider_id', $laundry->user_id)->count();
        $completedOrders = Order::where('provider_id', $laundry->user_id)
            ->where('status', 'completed')
            ->count();
        $pendingOrders = Order::where('provider_id', $laundry->user_id)
            ->where('status', 'pending')
            ->count();
        $inProcessOrders = Order::where('provider_id', $laundry->user_id)
            ->where('status', 'in_process')
            ->count();
            
        $totalRevenue = Order::where('provider_id', $laundry->user_id)
            ->where('status', 'completed')
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->sum('price') ?? 0;
            
        $todayRevenue = Order::where('provider_id', $laundry->user_id)
            ->where('status', 'completed')
            ->whereDate('created_at', $today)
            ->whereNotNull('price')
            ->sum('price') ?? 0;
            
        $totalServices = Service::where('provider_id', $laundry->user_id)->count();
        $approvedServices = Service::where('provider_id', $laundry->user_id)
            ->where('status', 'approved')
            ->count();
            
        $validPriceOrders = Order::where('provider_id', $laundry->user_id)
            ->where('status', 'completed')
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->count();
            
        $avgOrderValue = ($validPriceOrders > 0 && $totalRevenue > 0) ? round($totalRevenue / $validPriceOrders, 2) : 0;
        
        $recentOrders = Order::where('provider_id', $laundry->user_id)
            ->with(['user', 'target'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return [
            'today_orders' => $todayOrders ?? 0,
            'total_orders' => $totalOrders ?? 0,
            'completed_orders' => $completedOrders ?? 0,
            'pending_orders' => $pendingOrders ?? 0,
            'in_process_orders' => $inProcessOrders ?? 0,
            'total_revenue' => $totalRevenue ?? 0,
            'today_revenue' => $todayRevenue ?? 0,
            'total_services' => $totalServices ?? 0,
            'active_services' => $approvedServices ?? 0,
            'avg_order_value' => $avgOrderValue ?? 0,
            'recent_orders' => $recentOrders,
        ];
    }

    /**
     * Send registration email notifications
     */
    private function sendRegistrationEmails(User $user): void
    {
        try {
            switch ($user->role) {
                case 'laundry':
                    \App\Mail\MailOrders::sendLaundryRegistrationNotification($user);
                    \App\Mail\MailOrders::sendLaundryWelcomeEmail($user);
                    break;
                    
                case 'agent':
                    \App\Mail\MailOrders::sendAgentRegistrationNotification($user);
                    \App\Mail\MailOrders::sendAgentWelcomeEmail($user);
                    break;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send registration emails: ' . $e->getMessage());
        }
    }

    /**
     * Get revenue for specific date range
     */
    public function getRevenueForPeriod(Request $request, Laundry $laundry)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);

            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $revenue = Order::where('provider_id', $laundry->user_id)
                ->where('status', 'completed')
                ->whereNotNull('price')
                ->where('price', '>', 0)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('price');

            $revenue = $revenue ?? 0;
            
            return response()->json([
                'revenue' => $revenue,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'formatted_revenue' => 'ر.س ' . number_format($revenue, 2)
            ]);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return response()->json(['error' => $errorMessage['message']], 500);
        }
    }

    /**
     * Send notification to laundry
     */
    public function sendLaundryNotification(Request $request, Laundry $laundry)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:500'
            ]);

            $successMessage = Helper::messageSuccess('Notification sent');
            return redirect()->back()->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->withInput()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Update user status (approve/reject)
     */
    public function updateUserStatus(Request $request, User $user)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,approved,rejected'
            ]);

            $user->update(['status' => $request->status]);

            $successMessage = Helper::messageSuccess('User status updated');
            return response()->json([
                'success' => true,
                'message' => $successMessage['message']
            ]);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return response()->json(['error' => $errorMessage['message']], 500);
        }
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,in_process,completed,canceled'
            ]);

            $order->update(['status' => $request->status]);

            $successMessage = Helper::messageSuccess('Order status updated');
            return response()->json([
                'success' => true,
                'message' => $successMessage['message']
            ]);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return response()->json(['error' => $errorMessage['message']], 500);
        }
    }

    /**
     * Get order details for AJAX
     */
    public function getOrderDetails(Order $order)
    {
        try {
            $order->load(['user', 'recipient', 'provider', 'target']);
            
            return response()->json([
                'success' => true,
                'order' => $order
            ]);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return response()->json(['error' => $errorMessage['message']], 500);
        }
    }

    /**
     * Get user details for AJAX
     */
    public function getUserDetails(User $user)
    {
        try {
            $user->load(['customer', 'admin', 'agent']);

            return response()->json([
                'success' => true,
                'user' => $user
            ]);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return response()->json(['error' => $errorMessage['message']], 500);
        }
    }

    /**
     * Block user
     */
    public function blockUser(Request $request, User $user)
    {
        try {
            $user->update(['status' => 'rejected']);
            
            $successMessage = Helper::messageSuccess('User blocked');
            return response()->json([
                'success' => true,
                'message' => $successMessage['message']
            ]);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return response()->json(['error' => $errorMessage['message']], 500);
        }
    }

    /**
     * Activate user
     */
    public function activateUser(Request $request, User $user)
    {
        try {
            $user->update(['status' => 'approved']);
            
            $successMessage = Helper::messageSuccess('User activated');
            return response()->json([
                'success' => true,
                'message' => $successMessage['message']
            ]);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return response()->json(['error' => $errorMessage['message']], 500);
        }
    }

    /**
     * Block agent
     */
    public function blockAgent(Request $request, Agent $agent)
    {
        try {
            $agent->user->update(['status' => 'rejected']);
            $agent->update(['is_active' => false, 'status' => 'offline']);
            
            $successMessage = Helper::messageSuccess('Agent blocked');
            return response()->json([
                'success' => true,
                'message' => $successMessage['message']
            ]);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return response()->json(['error' => $errorMessage['message']], 500);
        }
    }

    /**
     * Send notification to agent
     */
    public function sendAgentNotification(Request $request, Agent $agent)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:500'
            ]);

            $successMessage = Helper::messageSuccess('Notification sent');
            return response()->json([
                'success' => true,
                'message' => $successMessage['message']
            ]);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return response()->json(['error' => $errorMessage['message']], 500);
        }
    }

    /**
     * Deactivate laundry
     */
    public function deactivateLaundry(Request $request, Laundry $laundry)
    {
        try {
            $laundry->user->update(['status' => 'rejected']);
            
            $successMessage = Helper::messageSuccess('Laundry deactivated');
            return redirect()->back()->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Reactivate rejected laundry
     */
    public function reactivateLaundry(Request $request, Laundry $laundry)
    {
        try {
            $laundry->user->update(['status' => 'approved']);
            
            $successMessage = Helper::messageSuccess('Laundry reactivated');
            return redirect()->back()->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Change laundry status to pending
     */
    public function suspendLaundry(Request $request, Laundry $laundry)
    {
        try {
            $laundry->user->update(['status' => 'pending']);
            
            $successMessage = Helper::messageSuccess('Laundry suspended');
            return redirect()->back()->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }




}


