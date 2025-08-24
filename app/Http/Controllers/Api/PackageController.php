<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\User;
use App\Mail\MailOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PackageController extends Controller
{
    /**
     * Get all active packages
     */
    public function index()
    {
        $packages = Package::active()->get();
        
        return response()->json([
            'packages' => $packages,
            'total' => $packages->count()
        ]);
    }

    /**
     * Get package by ID
     */
    public function show($id)
    {
        $package = Package::findOrFail($id);
        
        if (!$package->isActive()) {
            return response()->json(['message' => 'Package is not available'], 400);
        }
        
        return response()->json(['package' => $package]);
    }

    /**
     * Purchase package for self
     */
    public function purchase(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'payment_method' => 'required|in:online,cash'
        ]);

        $user = $request->user();
        $package = Package::findOrFail($request->package_id);

        if (!$package->isActive()) {
            return response()->json(['message' => 'Package is not available'], 400);
        }

        try {
            DB::beginTransaction();

            $order = $this->createPackageOrder($user, $package, $user, $request->payment_method, 'self');
            $this->addCoinsToUser($user, $package->coins_amount);
            $this->createPackageInvoice($order, $user, $package, $request->payment_method);

            DB::commit();

            // Send email notification to admin
            $this->sendPackagePurchaseNotification($order, $user, $package);

            return response()->json([
                'message' => 'Package purchased successfully',
                'order' => $order->load('target'),
                'new_balance' => $user->fresh()->coins,
                'package' => $package
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Package purchase failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Purchase failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Gift package to another customer
     */
    public function gift(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'recipient_email' => 'required|email|exists:users,email',
            'payment_method' => 'required|in:online,cash'
        ]);

        $user = $request->user();
        $package = Package::findOrFail($request->package_id);
        $recipient = User::where('email', $request->recipient_email)->first();

        if (!$package->isActive()) {
            return response()->json(['message' => 'Package is not available'], 400);
        }

        if ($recipient->id === $user->id) {
            return response()->json(['message' => 'Cannot gift to yourself. Use purchase instead.'], 400);
        }

        if ($recipient->role !== 'customer') {
            return response()->json(['message' => 'Can only gift to customers'], 400);
        }

        try {
            DB::beginTransaction();

            $order = $this->createPackageOrder($user, $package, $recipient, $request->payment_method, 'gift');
            $this->addCoinsToUser($recipient, $package->coins_amount);
            $this->createPackageInvoice($order, $recipient, $package, $request->payment_method);

            DB::commit();

            // Send email notifications
            $this->sendPackageGiftNotification($order, $user, $recipient, $package);

            return response()->json([
                'message' => 'Package gifted successfully',
                'order' => $order->load(['target', 'recipient']),
                'recipient_balance' => $recipient->fresh()->coins,
                'package' => $package
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Package gift failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Gift failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Get packages by type
     */
    public function byType($type)
    {
        $packages = Package::active()->byType($type)->get();
        
        return response()->json([
            'packages' => $packages,
            'type' => $type,
            'total' => $packages->count()
        ]);
    }

    /**
     * Create package order
     */
    private function createPackageOrder(User $user, Package $package, User $recipient, string $paymentMethod, string $purchaseType): Order
    {
        $adminUser = $this->getAdminUser();

        return Order::create([
            'user_id' => $user->id,
            'recipient_id' => $recipient->id,
            'provider_id' => $adminUser->id,
            'target_id' => $package->id,
            'target_type' => Package::class,
            'coins' => $package->coins_amount,
            'price' => $package->price,
            'status' => 'completed',
            'meta' => ['purchase_type' => $purchaseType]
        ]);
    }

    /**
     * Add coins to user
     */
    private function addCoinsToUser(User $user, int $amount): void
    {
        $user->addCoins($amount);
    }

    /**
     * Create package invoice
     */
    private function createPackageInvoice(Order $order, User $recipient, Package $package, string $paymentMethod): void
    {
        $adminUser = $this->getAdminUser();

        Invoice::create([
            'order_id' => $order->id,
            'user_id' => $recipient->id,
            'payer_id' => $order->user_id,
            'provider_id' => $adminUser->id,
            'amount' => $package->price,
            'payment_method' => $paymentMethod,
            'status' => 'paid',
            'meta' => ['package_name' => $package->name]
        ]);
    }

    /**
     * Get admin user
     */
    private function getAdminUser(): User
    {
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            throw new \Exception('Admin user not found');
        }
        return $adminUser;
    }

    /**
     * Send package purchase notification to admin
     */
    private function sendPackagePurchaseNotification(Order $order, User $user, Package $package): void
    {
        try {
            MailOrders::sendPackagePurchase($order, $user, $package);
        } catch (\Exception $e) {
            Log::error('Failed to send package purchase email: ' . $e->getMessage());
        }
    }

    /**
     * Send package gift notification
     */
    private function sendPackageGiftNotification(Order $order, User $sender, User $recipient, Package $package): void
    {
        try {
            MailOrders::sendPackageGift($order, $sender, $recipient, $package);
        } catch (\Exception $e) {
            Log::error('Failed to send package gift email: ' . $e->getMessage());
        }
    }
}
