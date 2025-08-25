<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Order;
use App\Models\Customer;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    /**
     * Display a listing of packages
     */
    public function index()
    {
        $packages = Package::orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new package
     */
    public function create()
    {
        return view('admin.packages.create');
    }

    /**
     * Store a newly created package
     */
    public function store(Request $request)
    {
        \Log::info('Package creation started', ['request_data' => $request->all()]);
        
        try {
            $validated = $this->validatePackageCreation($request);
            \Log::info('Package validation passed', ['validated_data' => $validated]);
            
            $package = $this->createPackage($validated);
            \Log::info('Package created successfully', ['package_id' => $package->id, 'package_data' => $package->toArray()]);
            
            return redirect()
                ->route('admin.packages')
                ->with('success', 'تم إنشاء الباقة بنجاح');
                
        } catch (\Exception $e) {
            \Log::error('Package creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return back()
                ->withInput()
                ->with('error', Helper::messageErrorException($e));
        }
    }

    /**
     * Display the specified package
     */
    public function show(Package $package)
    {
        $package->load(['orders.user', 'orders.customer']);
        
        // Get customers who bought this package
        $customers = $this->getPackageCustomers($package);
        
        return view('admin.packages.show', compact('package', 'customers'));
    }

    /**
     * Show the form for editing the specified package
     */
    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    /**
     * Update the specified package
     */
    public function update(Request $request, Package $package)
    {
        $validated = $this->validatePackageUpdate($request, $package);
        
        try {
            $this->updatePackage($package, $validated);
            
            return redirect()
                ->route('admin.packages.show', $package)
                ->with('success', 'تم تحديث الباقة بنجاح');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', Helper::messageErrorException($e));
        }
    }

    /**
     * Remove the specified package
     */
    public function destroy(Package $package)
    {
        try {
            $package->delete();
            
            return redirect()
                ->route('admin.packages')
                ->with('success', 'تم حذف الباقة بنجاح');
                
        } catch (\Exception $e) {
            return back()->with('error', Helper::messageErrorException($e));
        }
    }

    /**
     * Change package status
     */
    public function changeStatus(Request $request, Package $package)
    {
        $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])]
        ]);

        try {
            $package->update(['status' => $request->status]);
            
            return back()->with('success', 'تم تغيير حالة الباقة بنجاح');
            
        } catch (\Exception $e) {
            return back()->with('error', Helper::messageErrorException($e));
        }
    }

    /**
     * Show customers who purchased this package
     */
    public function customers(Package $package)
    {
        $customers = $this->getPackageCustomers($package);
        
        return view('admin.packages.customers', compact('package', 'customers'));
    }

    /**
     * Get customers who purchased a specific package
     */
    private function getPackageCustomers(Package $package)
    {
        return Order::where('target_type', Package::class)
            ->where('target_id', $package->id)
            ->with(['user', 'customer.user'])
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'order_id' => $order->id,
                    'customer_name' => $order->user->name ?? 'غير محدد',
                    'customer_email' => $order->user->email ?? 'غير محدد',
                    'customer_phone' => $order->user->phone ?? 'غير محدد',
                    'purchase_date' => $order->created_at,
                    'amount_paid' => $order->price > 0 ? $order->price : $order->coins,
                    'payment_type' => $order->price > 0 ? 'نقدي' : 'نقاط',
                    'order_status' => $order->status,
                    'customer_id' => $order->user_id,
                ];
            });
    }

    /**
     * Validate package creation
     */
    private function validatePackageCreation(Request $request): array
    {
        \Log::info('Starting package validation', ['request_data' => $request->all()]);
        
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'type' => 'required|string|max:100',
            'coins_amount' => 'required|integer|min:1',
            'status' => 'nullable|in:active,inactive',
        ]);
        
        \Log::info('Package validation completed', ['validated_data' => $validated]);
        
        return $validated;
    }

    /**
     * Validate package update
     */
    private function validatePackageUpdate(Request $request, Package $package): array
    {
        return $request->validate([
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'price' => 'nullable|numeric|min:0',
            'type' => 'nullable|string|max:100',
            'coins_amount' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,inactive',
        ]);
    }

    /**
     * Create package
     */
    private function createPackage(array $validated): Package
    {
        \Log::info('Creating package with data', ['validated_data' => $validated]);
        
        $packageData = [
            'name' => [
                'ar' => $validated['name_ar'],
                'en' => $validated['name_en']
            ],
            'description' => [
                'ar' => $validated['description_ar'] ?? null,
                'en' => $validated['description_en'] ?? null
            ],
            'price' => $validated['price'],
            'type' => $validated['type'],
            'coins_amount' => $validated['coins_amount'],
            'status' => $validated['status'] ?? 'active',
        ];
        
        \Log::info('Package data prepared', ['package_data' => $packageData]);
        
        $package = Package::create($packageData);
        
        \Log::info('Package created in database', ['package_id' => $package->id]);
        
        return $package;
    }

    /**
     * Update package
     */
    private function updatePackage(Package $package, array $validated): void
    {
        $updateData = [];
        
        // Update name if provided
        if (isset($validated['name_ar']) || isset($validated['name_en'])) {
            $currentName = $package->getRawOriginal('name') ?: [];
            $updateData['name'] = [
                'ar' => $validated['name_ar'] ?? $currentName['ar'] ?? '',
                'en' => $validated['name_en'] ?? $currentName['en'] ?? ''
            ];
        }
        
        // Update description if provided
        if (isset($validated['description_ar']) || isset($validated['description_en'])) {
            $currentDescription = $package->getRawOriginal('description') ?: [];
            $updateData['description'] = [
                'ar' => $validated['description_ar'] ?? $currentDescription['ar'] ?? null,
                'en' => $validated['description_en'] ?? $currentDescription['en'] ?? null
            ];
        }
        
        // Update other fields if provided
        if (isset($validated['price'])) $updateData['price'] = $validated['price'];
        if (isset($validated['type'])) $updateData['type'] = $validated['type'];
        if (isset($validated['coins_amount'])) $updateData['coins_amount'] = $validated['coins_amount'];
        if (isset($validated['status'])) $updateData['status'] = $validated['status'];
        
        $package->update($updateData);
    }
}
