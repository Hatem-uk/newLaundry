<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\Worker;
use App\Models\Agent;
use App\Models\Admin;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_relationships()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $customer = Customer::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Customer::class, $user->customer);
        $this->assertEquals($customer->id, $user->customer->id);
    }

    public function test_customer_model_relationships()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $customer = Customer::factory()->create(['user_id' => $user->id]);

        // Test user relationship
        $this->assertInstanceOf(User::class, $customer->user);
        $this->assertEquals($user->id, $customer->user->id);

        // Test transactions relationship
        $transaction = Transaction::factory()->create(['customer_id' => $customer->id]);
        $this->assertCount(1, $customer->transactions);
        $this->assertEquals($transaction->id, $customer->transactions->first()->id);

        // Test service orders relationship
        $order = ServiceOrder::factory()->create(['customer_id' => $customer->id]);
        $this->assertCount(1, $customer->serviceOrders);
        $this->assertEquals($order->id, $customer->serviceOrders->first()->id);
    }

    public function test_company_model_relationships()
    {
        $user = User::factory()->create(['role' => 'company']);
        $company = Company::factory()->create(['user_id' => $user->id]);

        // Test user relationship
        $this->assertInstanceOf(User::class, $company->user);
        $this->assertEquals($user->id, $company->user->id);

        // Test services relationship
        $service = Service::factory()->create(['company_id' => $company->id]);
        $this->assertCount(1, $company->services);
        $this->assertEquals($service->id, $company->services->first()->id);

        // Test workers relationship
        $worker = Worker::factory()->create(['company_id' => $company->id]);
        $this->assertCount(1, $company->workers);
        $this->assertEquals($worker->id, $company->workers->first()->id);
    }

    public function test_service_model_relationships()
    {
        $company = Company::factory()->create();
        $service = Service::factory()->create(['company_id' => $company->id]);

        // Test company relationship
        $this->assertInstanceOf(Company::class, $service->company);
        $this->assertEquals($company->id, $service->company->id);

        // Test orders relationship
        $order = ServiceOrder::factory()->create(['service_id' => $service->id]);
        $this->assertCount(1, $service->orders);
        $this->assertEquals($order->id, $service->orders->first()->id);
    }

    public function test_service_order_model_relationships()
    {
        $customer = Customer::factory()->create();
        $service = Service::factory()->create();
        $order = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'service_id' => $service->id
        ]);

        // Test customer relationship
        $this->assertInstanceOf(Customer::class, $order->customer);
        $this->assertEquals($customer->id, $order->customer->id);

        // Test service relationship
        $this->assertInstanceOf(Service::class, $order->service);
        $this->assertEquals($service->id, $order->service->id);

        // Test company relationship (via service)
        $this->assertInstanceOf(Company::class, $order->company);
        $this->assertEquals($service->company_id, $order->company->id);
    }

    public function test_transaction_model_relationships()
    {
        $customer = Customer::factory()->create();
        $transaction = Transaction::factory()->create(['customer_id' => $customer->id]);

        // Test customer relationship
        $this->assertInstanceOf(Customer::class, $transaction->customer);
        $this->assertEquals($customer->id, $transaction->customer->id);
    }

    public function test_worker_model_relationships()
    {
        $company = Company::factory()->create();
        $worker = Worker::factory()->create(['company_id' => $company->id]);

        // Test company relationship
        $this->assertInstanceOf(Company::class, $worker->company);
        $this->assertEquals($company->id, $worker->company->id);

        // Test user relationship
        $this->assertInstanceOf(User::class, $worker->user);
    }

    public function test_service_model_scopes()
    {
        // Create services with different statuses
        Service::factory()->create(['status' => 'pending']);
        Service::factory()->create(['status' => 'approved']);
        Service::factory()->create(['status' => 'rejected']);

        // Test pending scope
        $pendingServices = Service::pending()->get();
        $this->assertCount(1, $pendingServices);
        $this->assertEquals('pending', $pendingServices->first()->status);

        // Test approved scope
        $approvedServices = Service::approved()->get();
        $this->assertCount(1, $approvedServices);
        $this->assertEquals('approved', $approvedServices->first()->status);

        // Test rejected scope
        $rejectedServices = Service::rejected()->get();
        $this->assertCount(1, $rejectedServices);
        $this->assertEquals('rejected', $rejectedServices->first()->status);
    }

    public function test_service_order_model_scopes()
    {
        // Create orders with different statuses
        ServiceOrder::factory()->create(['status' => 'pending']);
        ServiceOrder::factory()->create(['status' => 'completed']);
        ServiceOrder::factory()->create(['status' => 'cancelled']);

        // Test pending scope
        $pendingOrders = ServiceOrder::pending()->get();
        $this->assertCount(1, $pendingOrders);
        $this->assertEquals('pending', $pendingOrders->first()->status);

        // Test completed scope
        $completedOrders = ServiceOrder::completed()->get();
        $this->assertCount(1, $completedOrders);
        $this->assertEquals('completed', $completedOrders->first()->status);

        // Test cancelled scope
        $cancelledOrders = ServiceOrder::cancelled()->get();
        $this->assertCount(1, $cancelledOrders);
        $this->assertEquals('cancelled', $cancelledOrders->first()->status);
    }

    public function test_transaction_model_scopes()
    {
        $customer = Customer::factory()->create();

        // Create transactions with different types
        Transaction::factory()->create([
            'customer_id' => $customer->id,
            'type' => 'credit'
        ]);
        Transaction::factory()->create([
            'customer_id' => $customer->id,
            'type' => 'debit'
        ]);

        // Test credit scope
        $credits = Transaction::credits()->get();
        $this->assertCount(1, $credits);
        $this->assertEquals('credit', $credits->first()->type);

        // Test debit scope
        $debits = Transaction::debits()->get();
        $this->assertCount(1, $debits);
        $this->assertEquals('debit', $debits->first()->type);

        // Test completed scope
        $completed = Transaction::completed()->get();
        $this->assertCount(2, $completed);
    }

    public function test_customer_model_accessors()
    {
        $customer = Customer::factory()->create(['coins' => 1500]);

        // Test formatted coins accessor
        $this->assertEquals('1,500', $customer->formatted_coins);
    }

    public function test_transaction_model_accessors()
    {
        $transaction = Transaction::factory()->create([
            'type' => 'credit',
            'amount' => 500,
            'status' => 'completed'
        ]);

        // Test formatted amount accessor
        $this->assertEquals('500', $transaction->formatted_amount);

        // Test type label accessor
        $this->assertEquals('Credit', $transaction->type_label);

        // Test status label accessor
        $this->assertEquals('Completed', $transaction->status_label);
    }

    public function test_service_order_model_accessors()
    {
        $order = ServiceOrder::factory()->create(['status' => 'pending']);

        // Test status label accessor
        $this->assertEquals('Pending', $order->status_label);
    }

    public function test_customer_model_methods()
    {
        $customer = Customer::factory()->create(['coins' => 1000]);

        // Test hasEnoughCoins method
        $this->assertTrue($customer->hasEnoughCoins(500));
        $this->assertTrue($customer->hasEnoughCoins(1000));
        $this->assertFalse($customer->hasEnoughCoins(1001));

        // Test addCoins method
        $initialBalance = $customer->coins;
        $customer->addCoins(200, 'Bonus');
        $customer->refresh();
        $this->assertEquals($initialBalance + 200, $customer->coins);

        // Test deductCoins method
        $result = $customer->deductCoins(100, 'Purchase');
        $this->assertTrue($result);
        $customer->refresh();
        $this->assertEquals($initialBalance + 200 - 100, $customer->coins);
    }

    public function test_service_order_model_methods()
    {
        $order = ServiceOrder::factory()->create(['status' => 'pending']);

        // Test canBeCancelled method
        $this->assertTrue($order->canBeCancelled());

        // Test canBeCompleted method
        $this->assertTrue($order->canBeCompleted());

        // Test confirm method
        $order->confirm();
        $this->assertEquals('confirmed', $order->status);

        // Test startProgress method
        $order->startProgress();
        $this->assertEquals('in_progress', $order->status);

        // Test complete method
        $order->complete();
        $this->assertEquals('completed', $order->status);

        // Test cancel method
        $order->cancel();
        $this->assertEquals('cancelled', $order->status);
    }

    public function test_model_factory_states()
    {
        // Test Service factory states
        $pendingService = Service::factory()->pending()->create();
        $this->assertEquals('pending', $pendingService->status);

        $approvedService = Service::factory()->approved()->create();
        $this->assertEquals('approved', $approvedService->status);

        $rejectedService = Service::factory()->rejected()->create();
        $this->assertEquals('rejected', $rejectedService->status);

        // Test Customer factory states
        $wealthyCustomer = Customer::factory()->wealthy()->create();
        $this->assertGreaterThan(1000, $wealthyCustomer->coins);

        $noCoinsCustomer = Customer::factory()->noCoins()->create();
        $this->assertEquals(0, $noCoinsCustomer->coins);

        // Test ServiceOrder factory states
        $pendingOrder = ServiceOrder::factory()->pending()->create();
        $this->assertEquals('pending', $pendingOrder->status);

        $completedOrder = ServiceOrder::factory()->completed()->create();
        $this->assertEquals('completed', $completedOrder->status);

        $cancelledOrder = ServiceOrder::factory()->cancelled()->create();
        $this->assertEquals('cancelled', $cancelledOrder->status);

        // Test Transaction factory states
        $creditTransaction = Transaction::factory()->credit()->create();
        $this->assertEquals('credit', $creditTransaction->type);

        $debitTransaction = Transaction::factory()->debit()->create();
        $this->assertEquals('debit', $debitTransaction->type);

        $pendingTransaction = Transaction::factory()->pending()->create();
        $this->assertEquals('pending', $pendingTransaction->status);
    }

    public function test_model_validation()
    {
        // Test Customer model validation
        $this->expectException(\Illuminate\Database\QueryException::class);
        Customer::factory()->create(['coins' => 'invalid']);

        // Test Service model validation
        $this->expectException(\Illuminate\Database\QueryException::class);
        Service::factory()->create(['price' => 'invalid']);

        // Test Transaction model validation
        $this->expectException(\Illuminate\Database\QueryException::class);
        Transaction::factory()->create(['amount' => 'invalid']);
    }

    public function test_model_constants()
    {
        // Test Service model constants
        $this->assertEquals('pending', Service::STATUS_PENDING);
        $this->assertEquals('approved', Service::STATUS_APPROVED);
        $this->assertEquals('rejected', Service::STATUS_REJECTED);

        // Test ServiceOrder model constants
        $this->assertEquals('pending', ServiceOrder::STATUS_PENDING);
        $this->assertEquals('confirmed', ServiceOrder::STATUS_CONFIRMED);
        $this->assertEquals('in_progress', ServiceOrder::STATUS_IN_PROGRESS);
        $this->assertEquals('completed', ServiceOrder::STATUS_COMPLETED);
        $this->assertEquals('cancelled', ServiceOrder::STATUS_CANCELLED);

        // Test Transaction model constants
        $this->assertEquals('credit', Transaction::TYPE_CREDIT);
        $this->assertEquals('debit', Transaction::TYPE_DEBIT);
        $this->assertEquals('refund', Transaction::TYPE_REFUND);
        $this->assertEquals('bonus', Transaction::TYPE_BONUS);
        $this->assertEquals('purchase', Transaction::TYPE_PURCHASE);
    }

    public function test_model_events()
    {
        $customer = Customer::factory()->create(['coins' => 1000]);

        // Test that adding coins creates a transaction
        $initialTransactionCount = Transaction::where('customer_id', $customer->id)->count();
        $customer->addCoins(100, 'Test bonus');
        $newTransactionCount = Transaction::where('customer_id', $customer->id)->count();

        $this->assertEquals($initialTransactionCount + 1, $newTransactionCount);

        // Test that deducting coins creates a transaction
        $customer->deductCoins(50, 'Test purchase');
        $finalTransactionCount = Transaction::where('customer_id', $customer->id)->count();

        $this->assertEquals($initialTransactionCount + 2, $finalTransactionCount);
    }

    public function test_model_soft_deletes()
    {
        // Test that models can be soft deleted if they support it
        // This would depend on your implementation
        $this->assertTrue(true); // Placeholder for soft delete tests
    }

    public function test_model_mass_assignment()
    {
        // Test fillable fields
        $customerData = [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'coins' => 500
        ];

        $customer = Customer::create($customerData);

        $this->assertDatabaseHas('customers', $customerData);
        $this->assertEquals('Test Customer', $customer->name);
    }

    public function test_model_casting()
    {
        $customer = Customer::factory()->create(['coins' => 1000]);

        // Test that coins is cast to integer
        $this->assertIsInt($customer->coins);
        $this->assertEquals(1000, $customer->coins);

        $service = Service::factory()->create(['price' => 150.50]);

        // Test that price is cast to decimal
        $this->assertIsString($service->price); // Laravel casts decimal to string
        $this->assertEquals('150.50', $service->price);
    }
}
