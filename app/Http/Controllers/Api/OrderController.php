<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\User;
use App\Mail\MailOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Get user's orders with advanced filtering
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $status = $request->get('status');
        $type = $request->get('type');
        $limit = $request->get('limit', 20);
        
        $query = Order::where('user_id', $user->id)
            ->orWhere('recipient_id', $user->id);
            
        $this->applyOrderFilters($query, $request);
        
        $orders = $query->with(['target', 'provider', 'recipient', 'invoice'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
            
        return response()->json($orders);
    }

    /**
     * Get order details with full information
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::with(['target', 'provider', 'recipient', 'invoice'])
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('recipient_id', $user->id);
            })
            ->findOrFail($id);
            
        return response()->json(['order' => $order]);
    }

    /**
     * Purchase service using coins (Customer Service Purchase)
     */
    public function purchaseService(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1',
            'recipient_id' => 'nullable|exists:users,id'
        ]);

        $user = $request->user();
        $service = Service::findOrFail($request->service_id);
        $quantity = $request->quantity;
        $recipientId = $request->recipient_id ?? $user->id;

        // Validate service is available
        if (!$service->isActive()) {
            return response()->json(['message' => 'Service is not available'], 400);
        }

        // Check if service can be purchased with coins
        if (!$service->canBePurchasedWithCoins()) {
            return response()->json(['message' => 'Service cannot be purchased with coins'], 400);
        }

        // Calculate total cost
        $totalCost = $service->getCostForQuantity($quantity, true);
        
        // Check if user has enough coins
        if (!$user->hasEnoughCoins($totalCost)) {
            return response()->json([
                'message' => 'Insufficient coins',
                'required' => $totalCost,
                'available' => $user->coins
            ], 400);
        }

        // Validate recipient (typically same as payer for services)
        if ($recipientId !== $user->id) {
            return response()->json(['message' => 'Cannot purchase services for others'], 400);
        }

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'recipient_id' => $recipientId,
                'provider_id' => $service->provider_id,
                'target_id' => $service->id,
                'target_type' => Service::class,
                'coins' => -$totalCost, // Negative for spending
                'price' => $service->price * $quantity,
                'status' => 'pending',
                'meta' => [
                    'quantity' => $quantity,
                    'service_type' => $service->type
                ]
            ]);

            // Deduct coins from user
            $user->decrement('coins', $totalCost);

            // Create invoice
            $invoice = Invoice::create([
                'order_id' => $order->id,
                'user_id' => $recipientId,
                'payer_id' => $user->id,
                'provider_id' => $service->provider_id,
                'amount' => $order->price,
                'payment_method' => 'coins',
                'status' => 'paid',
                'meta' => [
                    'coins_used' => $totalCost,
                    'service_quantity' => $quantity
                ]
            ]);

            DB::commit();

            // Send email notification to laundry
            $this->sendServicePurchaseNotification($order);

            return response()->json([
                'message' => 'Service purchased successfully',
                'order' => $order->load(['target', 'provider', 'invoice']),
                'coins_remaining' => $user->fresh()->coins
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Service purchase failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Purchase failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Cancel order (only customer can cancel pending orders)
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $user = $request->user();
        $order = Order::findOrFail($id);

        // Check if user is the customer
        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'Only the customer can cancel orders'], 403);
        }

        // Check if order can be cancelled
        if (!$order->isPending()) {
            return response()->json(['message' => 'Order cannot be cancelled in current status'], 400);
        }

        try {
            DB::beginTransaction();

            // Update order status
            $order->update([
                'status' => 'canceled',
                'meta' => array_merge($order->meta ?? [], [
                    'canceled_at' => now(),
                    'cancel_reason' => $request->reason
                ])
            ]);

            // Refund coins if paid with coins
            if ($order->coins < 0) {
                $user->increment('coins', abs($order->coins));
            }

            // Update invoice status
            if ($order->invoice) {
                $order->invoice->update(['status' => 'refunded']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Order cancelled successfully',
                'order' => $order->fresh(),
                'coins_refunded' => $order->coins < 0 ? abs($order->coins) : 0
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Cancellation failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Update order status (only provider can move forward)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:in_process,completed'
        ]);

        $user = $request->user();
        $order = Order::findOrFail($id);
        $newStatus = $request->status;

        // Check if user is the provider
        if ($order->provider_id !== $user->id) {
            return response()->json(['message' => 'Only the service provider can update status'], 403);
        }

        // Validate status transition
        if ($newStatus === 'in_process' && !$order->isPending()) {
            return response()->json([
                'message' => 'Invalid status transition. Order must be pending to start processing.',
                'current_status' => $order->status,
                'requested_status' => $newStatus
            ], 400);
        }

        if ($newStatus === 'completed' && !$order->isInProcess() && !$order->isPending()) {
            return response()->json([
                'message' => 'Invalid status transition. Order must be in process or pending to complete.',
                'current_status' => $order->status,
                'requested_status' => $newStatus
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Update order status
            $order->update(['status' => $newStatus]);

            // If completed, update invoice status
            if ($newStatus === 'completed' && $order->invoice) {
                $order->invoice->markAsPaid();
            }

            DB::commit();

            // Send email notification to admin when order is completed
            if ($newStatus === 'completed') {
                $this->sendOrderCompletionNotification($order);
            }

            return response()->json([
                'message' => 'Order status updated successfully',
                'order' => $order->fresh(),
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order status update failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Status update failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Get orders for provider (laundry/agent)
     */
    public function providerOrders(Request $request)
    {
        $user = $request->user();
        $status = $request->get('status');
        $limit = $request->get('limit', 20);
        
        $query = Order::where('provider_id', $user->id);
        
        if ($status) {
            $query->byStatus($status);
        }
        
        $orders = $query->with(['target', 'user', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
            
        return response()->json($orders);
    }

    /**
     * Get customer's order history with advanced filtering
     */
    public function purchaseHistory(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'limit' => 'nullable|integer|min:1|max:100',
            'status' => 'nullable|in:pending,in_process,completed,canceled',
            'type' => 'nullable|in:package,service'
        ]);

        $query = Order::where('user_id', $user->id)
            ->orWhere('recipient_id', $user->id);

        $this->applyOrderFilters($query, $request);

        $orders = $query->with(['target', 'provider', 'recipient', 'invoice'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('limit', 20));

        return response()->json($orders);
    }

    /**
     * Get order statistics for user
     */
    public function statistics(Request $request)
    {
        $user = $request->user();
        
        $stats = $this->calculateOrderStats($user->id);

        return response()->json(['statistics' => $stats]);
    }

    /**
     * Apply order filters to query
     */
    private function applyOrderFilters($query, Request $request): void
    {
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('type')) {
            $query->byTargetType($request->type);
        }
    }

    /**
     * Calculate order statistics
     */
    private function calculateOrderStats(int $userId): array
    {
        return [
            'total_orders' => Order::where('user_id', $userId)->count(),
            'pending_orders' => Order::where('user_id', $userId)->byStatus('pending')->count(),
            'completed_orders' => Order::where('user_id', $userId)->byStatus('completed')->count(),
            'total_spent_coins' => abs(Order::where('user_id', $userId)->where('coins', '<', 0)->sum('coins')),
            'total_received_coins' => Order::where('user_id', $userId)->where('coins', '>', 0)->sum('coins'),
            'total_cash_spent' => Order::where('user_id', $userId)->sum('price'),
            'orders_by_type' => [
                'packages' => Order::where('user_id', $userId)->byTargetType('package')->count(),
                'services' => Order::where('user_id', $userId)->byTargetType('service')->count()
            ]
        ];
    }

    /**
     * Send service purchase notification
     */
    private function sendServicePurchaseNotification(Order $order): void
    {
        try {
            MailOrders::sendServicePurchase($order);
        } catch (\Exception $e) {
            Log::error('Failed to send service purchase email: ' . $e->getMessage());
        }
    }

    /**
     * Send order completion notification
     */
    private function sendOrderCompletionNotification(Order $order): void
    {
        try {
            MailOrders::sendOrderCompletionNotification($order);
        } catch (\Exception $e) {
            Log::error('Failed to send order completion email: ' . $e->getMessage());
        }
    }

    /**
     * Get all orders for admin
     */
    public function adminOrders(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'status' => 'nullable|in:pending,in_process,completed,canceled',
                'type' => 'nullable|in:package,service',
                'limit' => 'nullable|integer|min:1|max:100'
            ]);

            $query = Order::with(['target', 'provider', 'recipient', 'invoice']);

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('type')) {
                $query->where('target_type', $request->type === 'package' ? Package::class : Service::class);
            }

            $orders = $query->orderBy('created_at', 'desc')
                ->paginate($request->get('limit', 20));

            return response()->json([
                'orders' => $orders->items(),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get admin orders: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to get orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
