<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Helpers\Helper;
use App\Mail\MailOrders;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\User;
use App\Models\Order;
use App\Models\Package;
use App\Models\Rating;
use Illuminate\Support\Facades\Log;

class AdminServiceController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of all pending services.
     */
    public function pendingServices(Request $request)
    {
        try {
            $services = Service::with(['provider'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse([
                'services' => $services
            ], 200, 'Pending services retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting pending services: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get pending services', $ex->getMessage());
        }
    }

    /**
     * Display a listing of all services with filters.
     */
    public function index(Request $request)
    {
        try {
            $query = Service::with(['provider']);

            // Apply filters
            $this->applyServiceFilters($query, $request);

            $services = $query->orderBy('created_at', 'desc')->get();

            return $this->successResponse([
                'services' => $services,
                'filters_applied' => $request->only(['status', 'provider_id', 'type', 'min_price', 'max_price'])
            ], 200, 'Services retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting services: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get services', $ex->getMessage());
        }
    }

    /**
     * Approve a service.
     */
    public function approve(Request $request, $id)
    {
        return $this->updateServiceStatus($id, 'approved', 'approve');
    }

    /**
     * Reject a service.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string'
        ]);

        return $this->updateServiceStatus($id, 'rejected', 'reject', [
            'meta' => ['rejection_reason' => $request->reason]
        ]);
    }

    /**
     * Display the specified service.
     */
    public function show($id)
    {
        try {
            $service = Service::with(['provider'])->find($id);

            if (!$service) {
                return $this->notFoundResponse('Service not found');
            }

            return $this->successResponse($service, 200, 'Service retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting service: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get service', $ex->getMessage());
        }
    }

    /**
     * Get service statistics for admin.
     */
    public function statistics(Request $request)
    {
        try {
            $stats = [
                'total_services' => Service::count(),
                'pending_services' => Service::where('status', 'pending')->count(),
                'approved_services' => Service::where('status', 'approved')->count(),
                'rejected_services' => Service::where('status', 'rejected')->count(),
                'services_by_type' => Service::selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->get(),
                'services_by_provider_type' => Service::join('users', 'services.provider_id', '=', 'users.id')
                    ->selectRaw('users.role, COUNT(*) as count')
                    ->groupBy('users.role')
                    ->get(),
                'coin_services' => Service::whereNotNull('coin_cost')->count(),
                'cash_services' => Service::whereNotNull('price')->count()
            ];

            return $this->successResponse($stats, 200, 'Service statistics retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting service statistics: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get statistics', $ex->getMessage());
        }
    }

    /**
     * Bulk approve services.
     */
    public function bulkApprove(Request $request)
    {
        return $this->bulkUpdateServiceStatus($request, 'approved', 'approve');
    }

    /**
     * Bulk reject services.
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'reason' => 'nullable|string'
        ]);

        $updateData = ['status' => 'rejected'];
        if ($request->reason) {
            $updateData['meta'] = ['rejection_reason' => $request->reason];
        }

        return $this->bulkUpdateServiceStatus($request, 'rejected', 'reject', $updateData);
    }

    /**
     * Apply service filters to query
     */
    private function applyServiceFilters($query, Request $request): void
    {
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
    }

    /**
     * Update service status with common logic
     */
    private function updateServiceStatus($id, $newStatus, $action, $additionalData = []): \Illuminate\Http\JsonResponse
    {
        try {
            $service = Service::find($id);

            if (!$service) {
                return $this->notFoundResponse('Service not found');
            }

            if ($service->status === $newStatus) {
                return $this->errorResponse(null, 400, "Service is already {$newStatus}");
            }

            $updateData = array_merge(['status' => $newStatus], $additionalData);
            $service->update($updateData);

            // Send email notification if approving
            if ($newStatus === 'approved') {
                try {
                    MailOrders::sendServiceApproval($service);
                } catch (\Exception $e) {
                    Log::error("Failed to send service {$action} email: " . $e->getMessage());
                }
            }

            return $this->successResponse([
                'service' => $service->fresh()->load('provider')
            ], 200, "Service {$action}d successfully");

        } catch (\Exception $ex) {
            Log::error("Error {$action}ing service: ", ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, "Failed to {$action} service", $ex->getMessage());
        }
    }

    /**
     * Bulk update service status with common logic
     */
    private function bulkUpdateServiceStatus(Request $request, $newStatus, $action, $additionalData = []): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'service_ids' => 'required|array',
                'service_ids.*' => 'exists:services,id'
            ]);

            $services = Service::whereIn('id', $request->service_ids)
                ->where('status', 'pending');

            $count = $services->count();
            
            $updateData = array_merge(['status' => $newStatus], $additionalData);
            $services->update($updateData);

            // Send email notifications if approving
            if ($newStatus === 'approved') {
                $approvedServices = Service::whereIn('id', $request->service_ids)->get();
                foreach ($approvedServices as $service) {
                    try {
                        MailOrders::sendServiceApproval($service);
                    } catch (\Exception $e) {
                        Log::error("Failed to send bulk service {$action} email: " . $e->getMessage());
                    }
                }
            }

            return $this->successResponse([
                'updated_count' => $count
            ], 200, "Successfully {$action}d {$count} services");

        } catch (\Exception $ex) {
            Log::error("Error bulk {$action}ing services: ", ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, "Failed to bulk {$action} services", $ex->getMessage());
        }
    }

    /**
     * Get all users with filtering
     */
    public function getUsers(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'role' => 'nullable|in:admin,laundry,agent,worker,customer',
                'status' => 'nullable|in:pending,approved,rejected',
                'limit' => 'nullable|integer|min:1|max:100'
            ]);

            $query = User::query();

            if ($request->has('role')) {
                $query->where('role', $request->role);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $users = $query->with(['customer', 'laundry', 'agent'])
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('limit', 20));

            return $this->successResponse([
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total()
                ]
            ]);

        } catch (\Exception $ex) {
            Log::error("Error getting users: ", ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, "Failed to get users", $ex->getMessage());
        }
    }

    /**
     * Update user status
     */
    public function updateUserStatus(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:approved,rejected',
                'notes' => 'nullable|string|max:500'
            ]);

            $user = User::find($id);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            if ($user->status === $request->status) {
                return $this->errorResponse(null, 400, "User is already {$request->status}");
            }

            $user->update([
                'status' => $request->status,
                'meta' => array_merge($user->meta ?? [], [
                    'admin_notes' => $request->notes,
                    'status_updated_at' => now()
                ])
            ]);

            return $this->successResponse([
                'user' => $user->fresh()
            ], 200, "User status updated successfully");

        } catch (\Exception $ex) {
            Log::error("Error updating user status: ", ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, "Failed to update user status", $ex->getMessage());
        }
    }

    /**
     * Get platform statistics
     */
    public function platformStatistics(): \Illuminate\Http\JsonResponse
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'users_by_role' => [
                    'customers' => User::where('role', 'customer')->count(),
                    'laundries' => User::where('role', 'laundry')->count(),
                    'agents' => User::where('role', 'agent')->count(),
                    'workers' => User::where('role', 'worker')->count(),
                    'admins' => User::where('role', 'admin')->count()
                ],
                'users_by_status' => [
                    'pending' => User::where('status', 'pending')->count(),
                    'approved' => User::where('status', 'approved')->count(),
                    'rejected' => User::where('status', 'rejected')->count()
                ],
                'total_services' => Service::count(),
                'services_by_status' => [
                    'pending' => Service::where('status', 'pending')->count(),
                    'approved' => Service::where('status', 'approved')->count(),
                    'rejected' => Service::where('status', 'rejected')->count()
                ],
                'total_orders' => Order::count(),
                'orders_by_status' => [
                    'pending' => Order::where('status', 'pending')->count(),
                    'in_process' => Order::where('status', 'in_process')->count(),
                    'completed' => Order::count(),
                    'canceled' => Order::where('status', 'canceled')->count()
                ],
                'total_packages' => Package::count(),
                'total_ratings' => Rating::count(),
                'revenue_stats' => [
                    'total_revenue' => Order::sum('price'),
                    'monthly_revenue' => Order::whereMonth('created_at', now()->month)->sum('price'),
                    'weekly_revenue' => Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('price')
                ]
            ];

            return $this->successResponse(['statistics' => $stats]);

        } catch (\Exception $ex) {
            Log::error("Error getting platform statistics: ", ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, "Failed to get platform statistics", $ex->getMessage());
        }
    }
}

