<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

// Models
use App\Models\User;
use App\Models\Order;
use App\Models\Laundry;
use App\Models\Service;
use App\Models\Agent;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Rating;

// Helpers
use App\Helpers\Helper;

/**
 * Admin Controller
 * 
 * Handles all admin panel functionality including:
 * - Dashboard management
 * - User management (customers, agents, laundries)
 * - Order management
 * - Service management
 * - Statistics and reporting
 */
class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CONTROLLER SECTIONS
    |--------------------------------------------------------------------------
    | 1. DASHBOARD - Main admin dashboard and statistics
    | 2. USER MANAGEMENT - Customer, admin, and general user management
    | 3. SERVICE MANAGEMENT - Service CRUD operations
    | 4. LAUNDRY MANAGEMENT - Laundry CRUD and status management
    | 5. ORDER MANAGEMENT - Order CRUD and status management
    | 6. AGENT MANAGEMENT - Agent CRUD and status management
    | 7. HELPER & UTILITY METHODS - Dashboard stats and calculations
    | 8. VALIDATION METHODS - Request validation rules
    | 9. DATA UPDATE & CREATION METHODS - Model update operations
    | 10. USER CREATION & PROFILE METHODS - User account creation
    | 11. LAUNDRY & SERVICE CREATION METHODS - Laundry/service creation
    | 12. AGENT STATUS MANAGEMENT METHODS - Agent approval/rejection
    | 13. LAUNDRY VIEW & STATISTICS METHODS - Laundry detail views
    | 14. UTILITY & NOTIFICATION METHODS - General utilities
    | 15. NOTIFICATION & STATUS UPDATE METHODS - Status changes
    | 16. IMAGE HANDLING METHODS - File upload management
    |--------------------------------------------------------------------------
    */

    // ==================== DASHBOARD ====================
    
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

    // ==================== USER MANAGEMENT ====================
    
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
            
            // Ensure translatable fields are properly set for each laundry
            foreach ($laundries as $laundry) {
                $this->ensureTranslatableFields($laundry);
                if ($laundry->city) {
                    $this->ensureTranslatableFields($laundry->city);
                }
            }
                
            $counts = $this->getLaundryCounts();
                
            return view('admin.laundries', compact('laundries', 'counts'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    // ==================== SERVICE MANAGEMENT ====================
    
    /**
     * Show services management page
     */
    public function services()
    {
        try {
            $services = Service::with(['provider'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            return view('admin.services', compact('services'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Show create service form
     */
    public function createService(Request $request)
    {
        try {
            $providerId = $request->query('provider_id');
            $type = $request->query('type', 'laundry');
            
            $providers = User::where('role', $type)->get();
            
            return view('admin.services.create', compact('providers', 'providerId', 'type'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Store new service
     */
    public function storeService(Request $request)
    {
        try {
            $validated = $this->validateServiceCreation($request);
            $service = $this->createServiceData($validated);

            $successMessage = Helper::messageSuccess('Service created');
            return redirect()->route('admin.services.view', $service)->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->withInput()->with('error', $errorMessage['message']);
        }
    }

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

    // ==================== LAUNDRY MANAGEMENT ====================

    /**
     * Show laundry details
     */
    public function viewLaundry(Laundry $laundry)
    {
        try {
            // Load basic relationships
            $laundry->load(['user', 'city']);
            
            $this->ensureTranslatableFields($laundry);
            
            // Load translatable fields for related models
            if ($laundry->city) {
                $this->ensureTranslatableFields($laundry->city);
            }
            
            // Get comprehensive data
            $data = $this->getLaundryViewData($laundry);
            $laundryStats = $this->calculateLaundryStats($laundry);
            
            // Add counts to the laundry model for easy access in the view
            $laundry->orders_count = $data['orders']->count();
            $laundry->services_count = $data['services']->count();
            $laundry->total_ratings = $data['ratings']->count();
            
            // Calculate average rating
            if ($data['ratings']->count() > 0) {
                $laundry->average_rating = $data['ratings']->avg('rating');
            } else {
                $laundry->average_rating = 0;
            }
            
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
     * Update laundry status
     */
    public function updateLaundryStatus(Request $request, Laundry $laundry)
    {
        try {
            $request->validate([
                'status' => 'required|in:approved,pending,rejected,suspended'
            ]);

            $oldStatus = $laundry->user->status;
            $newStatus = $request->status;
            
            $laundry->user->update(['status' => $newStatus]);
            
            $statusMessages = [
                'approved' => 'تم تفعيل المغسلة بنجاح',
                'pending' => 'تم وضع المغسلة في قائمة الانتظار',
                'rejected' => 'تم رفض المغسلة',
                'suspended' => 'تم تعليق المغسلة'
            ];
            
            $successMessage = Helper::messageSuccess($statusMessages[$newStatus] ?? 'تم تحديث حالة المغسلة بنجاح');
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

            // Ensure translatable fields are properly set for each laundry
            foreach ($pendingLaundries as $laundry) {
                $this->ensureTranslatableFields($laundry);
                if ($laundry->city) {
                    $this->ensureTranslatableFields($laundry->city);
                }
            }

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

            // Ensure translatable fields are properly set for each laundry
            foreach ($rejectedLaundries as $laundry) {
                $this->ensureTranslatableFields($laundry);
                if ($laundry->city) {
                    $this->ensureTranslatableFields($laundry->city);
                }
            }

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

    // ==================== ORDER MANAGEMENT ====================

    /**
     * Show orders management page
     */
    public function orders()
    {
        try {
            $orders = Order::with(['user', 'recipient', 'provider'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            
            // Note: Translatable fields will be handled in the Blade template
            // to avoid potential model loading issues
                
            $counts = $this->getOrderStatusCounts();
                
            return view('admin.orders', compact('orders', 'counts'));
        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    // ==================== AGENT MANAGEMENT ====================

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
            return redirect()->back()->with('error', $errorMessage['message']);
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

    /**
     * Suspend agent
     */
    public function suspendAgent(Request $request, User $user)
    {
        try {
            $this->validateAgentApproval($user);
            $this->suspendAgentUser($user);

            $successMessage = Helper::messageSuccess('Agent suspended');
            return redirect()->back()->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Reactivate agent
     */
    public function reactivateAgent(Request $request, User $user)
    {
        try {
            $this->validateAgentApproval($user);
            $this->reactivateAgentUser($user);

            $successMessage = Helper::messageSuccess('Agent reactivated');
            return redirect()->back()->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
        }
    }

    /**
     * Destroy agent
     */
    public function destroyAgent(Agent $agent)
    {
        try {
            $agentName = $agent->user->name;
            $agent->delete();

            $successMessage = Helper::messageSuccess("Agent '{$agentName}' deleted");
            return redirect()->route('admin.agents')->with('success', $successMessage['message']);

        } catch (\Exception $ex) {
            $errorMessage = Helper::messageErrorException($ex);
            return redirect()->back()->with('error', $errorMessage['message']);
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
     * Activate agent
     */
    public function activateAgent(Request $request, Agent $agent)
    {
        try {
            $agent->user->update(['status' => 'approved']);
            
            $successMessage = Helper::messageSuccess('Agent activated');
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
            return redirect()->route('admin.orders')->with('success', $successMessage['message']);

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

    // ==================== HELPER & UTILITY METHODS ====================

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
                $locale = app()->getLocale();
                return is_array($service->name) 
                    ? ($service->name[$locale] ?? $service->name['en'] ?? $service->name['ar'] ?? 'Service')
                    : $service->name;
            }
        } elseif ($order->target_type === 'package') {
            $package = Package::find($order->target_id);
            if ($package) {
                $locale = app()->getLocale();
                return is_array($package->name) 
                    ? ($package->name[$locale] ?? $package->name['en'] ?? $package->name['ar'] ?? 'Package')
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
            'canceledCount' => Order::where('status', 'canceled')->count(),
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
            'admin_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'customer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'laundry_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'agent_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'admin_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'customer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'laundry_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'agent_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    }

    /**
     * Validate agent update
     */
    private function validateAgentUpdate(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->route('agent')->user_id,
            'phone' => 'nullable|string|max:20',
            'license_number' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:pending,approved,rejected',
            'is_active' => 'required|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'description' => 'required|string|max:1000',
            'description_en' => 'required|string|max:1000',
            'price' => 'nullable|numeric|min:0',
            'coin_cost' => 'nullable|integer|min:0',
            'type' => 'required|string|in:washing,ironing,dry_cleaning,agent_supply,laundry_service',
            'status' => 'required|string|in:pending,active,inactive,approved,rejected',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    }

    /**
     * Validate order update
     */
    private function validateOrderUpdate(Request $request): array
    {
        return $request->validate([
            'status' => 'required|in:pending,in_process,completed,canceled',
            'price' => 'nullable|numeric|min:0',
            'coins' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:500',
            'target_id' => 'nullable|integer',
            'target_type' => 'nullable|in:service,package',
            'user_id' => 'nullable|exists:users,id',
            'provider_id' => 'nullable|exists:users,id',
            'recipient_id' => 'nullable|exists:users,id'
        ]);
    }

    // ==================== DATA UPDATE & CREATION METHODS ====================

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

        // Handle image uploads for different user types
        $this->handleUserImageUploads($user, $validated);

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

        // Handle logo upload
        $logoPath = $agent->logo; // Keep existing logo by default
        if (request()->hasFile('logo')) {
            // Delete old logo if exists
            if ($agent->logo) {
                Helper::deleteFile($agent->logo);
            }
            // Upload new logo
            $logoPath = Helper::uploadFile(
                request()->file('logo'),
                'agent_logos',
                ['jpeg', 'png', 'jpg', 'gif'],
                2048
            );
        }

        $agent->update([
            'license_number' => $validated['license_number'],
            'city_id' => $validated['city_id'],
            'address' => $validated['address'],
            'is_active' => $validated['is_active'],
            'logo' => $logoPath,
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
            'coin_cost' => $validated['coin_cost'],
            'type' => $validated['type'],
            'status' => $validated['status']
        ]);

        if (request()->hasFile('image')) {
            $this->updateServiceImage($service, request()->file('image'));
        }
    }

    /**
     * Update service image
     */
    private function updateServiceImage(Service $service, $image): void
    {
        if ($service->image) {
            Storage::delete('public/' . $service->image);
        }

        $path = $image->store('services', 'public');
        $service->update(['image' => $path]);
    }

    /**
     * Update order data
     */
    private function updateOrderData(Order $order, array $validated): void
    {
        $updateData = [
            'status' => $validated['status']
        ];

        // Only update fields if they are provided
        if (isset($validated['price'])) {
            $updateData['price'] = $validated['price'];
        }
        if (isset($validated['coins'])) {
            $updateData['coins'] = $validated['coins'];
        }
        if (isset($validated['quantity'])) {
            $updateData['quantity'] = $validated['quantity'];
        }
        if (isset($validated['notes'])) {
            $updateData['notes'] = $validated['notes'];
        }
        if (isset($validated['target_id'])) {
            $updateData['target_id'] = $validated['target_id'];
        }
        if (isset($validated['target_type'])) {
            $updateData['target_type'] = $validated['target_type'];
        }
        if (isset($validated['user_id'])) {
            $updateData['user_id'] = $validated['user_id'];
        }
        if (isset($validated['provider_id'])) {
            $updateData['provider_id'] = $validated['provider_id'];
        }
        if (isset($validated['recipient_id'])) {
            $updateData['recipient_id'] = $validated['recipient_id'];
        }

        $order->update($updateData);
    }

    // ==================== USER CREATION & PROFILE METHODS ====================

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
        if (request()->hasFile('laundry_logo')) {
            $logoPath = Helper::uploadFile(
                request()->file('laundry_logo'),
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
        $imagePath = null;
        if (request()->hasFile('customer_image')) {
            $imagePath = Helper::uploadFile(
                request()->file('customer_image'),
                'customer_images',
                ['jpeg', 'png', 'jpg', 'gif'],
                2048
            );
        }

        $user->customer()->create([
            'address' => $validated['address'] ? [
                'ar' => $validated['address'],
                'en' => $validated['address']
            ] : '',
            'phone' => $validated['phone'] ?? '',
            'city_id' => $validated['city_id'],
            'coins' => $validated['coins'] ?? 100,
            'image' => $imagePath
        ]);
    }

    /**
     * Create admin profile
     */
    private function createAdminProfile(User $user, array $validated): void
    {
        $imagePath = null;
        if (request()->hasFile('admin_image')) {
            $imagePath = Helper::uploadFile(
                request()->file('admin_image'),
                'admin_images',
                ['jpeg', 'png', 'jpg', 'gif'],
                2048
            );
        }

        $user->admin()->create([
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'image' => $imagePath,
            'is_active' => true
        ]);
    }

    /**
     * Create agent profile
     */
    private function createAgentProfile(User $user, array $validated): void
    {
        $logoPath = null;
        if (request()->hasFile('agent_logo')) {
            $logoPath = Helper::uploadFile(
                request()->file('agent_logo'),
                'agent_logos',
                ['jpeg', 'png', 'jpg', 'gif'],
                2048
            );
        }

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
            'logo' => $logoPath,
            'is_active' => true,
            'status' => 'online'
        ]);
    }

    // ==================== LAUNDRY & SERVICE CREATION METHODS ====================

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

    /**
     * Create service data
     */
    private function createServiceData(array $validated): Service
    {
        $serviceData = [
            'name' => Helper::translateData(request(), 'name', 'name_en'),
            'description' => Helper::translateData(request(), 'description', 'description_en'),
            'price' => $validated['price'],
            'coins' => $validated['coins'],
            'is_active' => true,
            'provider_id' => $validated['provider_id'],
            'target_type' => $validated['type'] === 'laundry' ? 'laundry' : 'package',
            'target_id' => $validated['type'] === 'laundry' ? $validated['laundry_id'] : $validated['package_id'],
        ];

        return Service::create($serviceData);
    }

    /**
     * Validate service creation
     */
    private function validateServiceCreation(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'description_en' => 'required|string|max:1000',
            'price' => 'nullable|numeric|min:0',
            'coin_cost' => 'nullable|integer|min:0',
            'type' => 'required|string|in:washing,ironing,dry_cleaning,agent_supply,laundry_service',
            'provider_id' => 'required|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    }

    // ==================== AGENT STATUS MANAGEMENT METHODS ====================

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

    /**
     * Suspend agent user
     */
    private function suspendAgentUser(User $user): void
    {
        $user->update(['status' => 'suspended']);

        if ($user->agent) {
            $user->agent->update([
                'is_active' => false,
                'status' => 'offline'
            ]);
        }
    }

    /**
     * Reactivate agent user
     */
    private function reactivateAgentUser(User $user): void
    {
        $user->update(['status' => 'approved']);

        if ($user->agent) {
            $user->agent->update([
                'is_active' => true,
                'status' => 'online'
            ]);
        }
    }

    // ==================== LAUNDRY VIEW & STATISTICS METHODS ====================

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
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $servicePurchases = Order::where('provider_id', $laundry->user_id)
            ->where('target_type', 'service')
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $ratings = Rating::where('laundry_id', $laundry->id)
            ->with(['customer.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'services' => $services,
            'orders' => $orders,
            'servicePurchases' => $servicePurchases,
            'ratings' => $ratings
        ];
    }

    // ==================== UTILITY & NOTIFICATION METHODS ====================

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
            ->with(['user'])
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



    // ==================== NOTIFICATION & STATUS UPDATE METHODS ====================
    
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
     * Send notification to agent
     */
    public function sendAgentNotification(Request $request, Agent $agent)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:500'
            ]);

            // Here you would implement the actual notification logic
            // For now, we'll just return a success message
            
            $successMessage = Helper::messageSuccess('Notification sent to agent');
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

    // ==================== IMAGE HANDLING METHODS ====================
    
    /**
     * Handle image uploads for different user types
     */
    private function handleUserImageUploads(User $user, array $validated): void
    {
        // Handle admin image upload
        if (isset($validated['admin_image']) && $user->admin) {
            $this->handleImageUpload($user->admin, 'admin_image', 'admin_images', 'image');
        }

        // Handle customer image upload
        if (isset($validated['customer_image']) && $user->customer) {
            $this->handleImageUpload($user->customer, 'customer_image', 'customer_images', 'image');
        }

        // Handle laundry logo upload
        if (isset($validated['laundry_logo']) && $user->laundry) {
            $this->handleImageUpload($user->laundry, 'laundry_logo', 'laundry_logos', 'logo');
        }

        // Handle agent logo upload
        if (isset($validated['agent_logo']) && $user->agent) {
            $this->handleImageUpload($user->agent, 'agent_logo', 'agent_logos', 'logo');
        }
    }

    /**
     * Handle individual image upload
     */
    private function handleImageUpload($model, string $fieldName, string $directory, string $attribute): void
    {
        if (request()->hasFile($fieldName)) {
            // Delete old image if exists
            if ($model->$attribute) {
                Helper::deleteFile($model->$attribute);
            }
            // Upload new image
            $imagePath = Helper::uploadFile(
                request()->file($fieldName),
                $directory,
                ['jpeg', 'png', 'jpg', 'gif'],
                2048
            );
            // Update model
            $model->update([$attribute => $imagePath]);
        }
    }


}


