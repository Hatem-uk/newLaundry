<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailOrders extends Mailable
{
    use Queueable, SerializesModels;

    public $type;
    public $data;
    public $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct($type, $data, $recipient)
    {
        $this->type = $type;
        $this->data = $data;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getSubject(),
            to: $this->recipient->email,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->getView(),
            with: $this->data,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get the appropriate subject based on email type
     */
    private function getSubject(): string
    {
        switch ($this->type) {
            case 'service_approved':
                return 'Service Approved - ' . $this->data['service_name'];
            case 'service_purchased':
                return 'New Service Purchase - ' . $this->data['service_name'];
            case 'new_service_added':
                return 'New Service Added - ' . $this->data['service_name'];
            case 'order_completed':
                return 'Order Completed - ' . $this->data['order_id'];
            case 'package_purchased':
                return 'Package Purchased - ' . $this->data['package_name'];
            case 'package_gifted':
                return 'Package Gifted - ' . $this->data['package_name'];
            case 'package_received':
                return 'Package Received - ' . $this->data['package_name'];
            case 'laundry_registration':
                return 'New Laundry Registration - ' . $this->data['laundry_name'];
            case 'agent_registration':
                return 'New Agent Registration - ' . $this->data['agent_name'];
            case 'laundry_welcome':
                return 'Welcome to Laundry Service System - ' . $this->data['laundry_name'];
            case 'agent_welcome':
                return 'Welcome to Laundry Service System - ' . $this->data['agent_name'];
            default:
                return 'Laundry Service Notification';
        }
    }

    /**
     * Get the appropriate view based on email type
     */
    private function getView(): string
    {
        switch ($this->type) {
            case 'service_approved':
                return 'emails.service.approved';
            case 'service_purchased':
                return 'emails.service.purchased';
            case 'new_service_added':
                return 'emails.service.new';
            case 'order_completed':
                return 'emails.order.completed';
            case 'package_purchased':
                return 'emails.package.purchased';
            case 'package_gifted':
                return 'emails.package.gifted';
            case 'package_received':
                return 'emails.package.received';
            case 'laundry_registration':
                return 'emails.registration.laundry';
            case 'agent_registration':
                return 'emails.registration.agent';
            case 'laundry_welcome':
                return 'emails.welcome.laundry';
            case 'agent_welcome':
                return 'emails.welcome.agent';
            default:
                return 'emails.default';
        }
    }

    /**
     * Static method to send service approval email to laundry
     */
    public static function sendServiceApproval(Service $service)
    {
        $laundry = $service->provider;
        $data = [
            'service_name' => $service->name,
            'service_description' => $service->description,
            'service_type' => $service->type,
            'coin_cost' => $service->coin_cost,
            'price' => $service->price,
            'approved_at' => now()->format('Y-m-d H:i:s'),
            'service_id' => $service->id
        ];

        \Mail::to($laundry->email)->send(new self('service_approved', $data, $laundry));
    }

    /**
     * Static method to send service purchase notification to laundry
     */
    public static function sendServicePurchase(\App\Models\Order $order)
    {
        $laundry = $order->provider;
        $customer = $order->customer;
        $service = $order->target;
        
        $data = [
            'order_id' => $order->id,
            'service_name' => $service->getTranslation('name', app()->getLocale()) ?? $service->getTranslation('name', 'en'),
            'customer_name' => $customer->user->name,
            'customer_email' => $customer->user->email,
            'customer_phone' => $customer->phone,
            'order_amount' => $order->price,
            'coins_used' => abs($order->coins),
            'order_date' => $order->created_at->format('Y-m-d H:i:s'),
            'order_status' => $order->status
        ];

        \Mail::to($laundry->email)->send(new self('service_purchased', $data, $laundry));
    }

    /**
     * Static method to send new service notification to admin
     */
    public static function sendNewServiceNotification(Service $service)
    {
        $laundry = $service->provider;
        $data = [
            'service_name' => $service->getTranslation('name', app()->getLocale()) ?? $service->getTranslation('name', 'en'),
            'service_description' => $service->getTranslation('description', app()->getLocale()) ?? $service->getTranslation('description', 'en'),
            'service_type' => $service->type,
            'coin_cost' => $service->coin_cost,
            'price' => $service->price,
            'laundry_name' => $laundry->getTranslation('name', app()->getLocale()) ?? $laundry->getTranslation('name', 'en'),
            'laundry_email' => $laundry->email,
            'laundry_phone' => $laundry->phone,
            'added_at' => $service->created_at->format('Y-m-d H:i:s'),
            'service_id' => $service->id
        ];

        // Get admin users
        $adminUsers = \App\Models\User::where('role', 'admin')->get();
        
        foreach ($adminUsers as $admin) {
            \Mail::to($admin->email)->send(new self('new_service_added', $data, $admin));
        }
    }

    /**
     * Static method to send order completion notification to admin
     */
    public static function sendOrderCompletionNotification(\App\Models\Order $order)
    {
        $laundry = $order->provider;
        $customer = $order->customer;
        $service = $order->target;
        
        $data = [
            'order_id' => $order->id,
            'service_name' => $service->getTranslation('name', app()->getLocale()) ?? $service->getTranslation('name', 'en'),
            'laundry_name' => $laundry->getTranslation('name', app()->getLocale()) ?? $laundry->getTranslation('name', 'en'),
            'customer_name' => $customer->user->name,
            'customer_email' => $customer->user->email,
            'order_amount' => $order->price,
            'coins_used' => abs($order->coins),
            'order_date' => $order->created_at->format('Y-m-d H:i:s'),
            'completed_at' => $order->updated_at->format('Y-m-d H:i:s'),
            'order_status' => $order->status
        ];

        // Get admin users
        $adminUsers = \App\Models\User::where('role', 'admin')->get();
        
        foreach ($adminUsers as $admin) {
            \Mail::to($admin->email)->send(new self('order_completed', $data, $admin));
        }
    }

    /**
     * Static method to send package purchase notification to admin
     */
    public static function sendPackagePurchase(\App\Models\Order $order, \App\Models\User $user, \App\Models\Package $package)
    {
        $data = [
            'order_id' => $order->id,
            'package_name' => $package->getTranslation('name', app()->getLocale()) ?? $package->getTranslation('name', 'en'),
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'package_price' => $package->price,
            'coins_amount' => $package->coins_amount,
            'payment_method' => $order->meta['purchase_type'] ?? 'self',
            'order_date' => $order->created_at->format('Y-m-d H:i:s'),
            'order_status' => $order->status
        ];

        // Get admin users
        $adminUsers = \App\Models\User::where('role', 'admin')->get();
        
        foreach ($adminUsers as $admin) {
            \Mail::to($admin->email)->send(new self('package_purchased', $data, $admin));
        }
    }

    /**
     * Static method to send package gift notification
     */
    public static function sendPackageGift(\App\Models\Order $order, \App\Models\User $sender, \App\Models\User $recipient, \App\Models\Package $package)
    {
        $data = [
            'order_id' => $order->id,
            'package_name' => $package->getTranslation('name', app()->getLocale()) ?? $package->getTranslation('name', 'en'),
            'sender_name' => $sender->name,
            'sender_email' => $sender->email,
            'recipient_name' => $recipient->name,
            'recipient_email' => $recipient->email,
            'package_price' => $package->price,
            'coins_amount' => $package->coins_amount,
            'gift_date' => $order->created_at->format('Y-m-d H:i:s'),
            'order_status' => $order->status
        ];

        // Send to admin
        $adminUsers = \App\Models\User::where('role', 'admin')->get();
        foreach ($adminUsers as $admin) {
            \Mail::to($admin->email)->send(new self('package_gifted', $data, $admin));
        }

        // Send to recipient
        \Mail::to($recipient->email)->send(new self('package_received', $data, $recipient));
    }

    /**
     * Static method to send laundry registration notification to admin
     */
    public static function sendLaundryRegistrationNotification(\App\Models\User $laundryUser)
    {
        $laundry = $laundryUser->laundry;
        $data = [
            'user_name' => $laundryUser->name,
            'user_email' => $laundryUser->email,
            'user_phone' => $laundryUser->phone,
            'laundry_name' => $laundry ? $laundry->name : $laundryUser->name,
            'laundry_address' => $laundry ? $laundry->address : 'Not specified',
            'laundry_phone' => $laundry ? $laundry->phone : $laundryUser->phone,
            'city_id' => $laundry ? $laundry->city_id : null,
            'registration_date' => $laundryUser->created_at->format('Y-m-d H:i:s'),
            'user_id' => $laundryUser->id
        ];

        // Get admin users
        $adminUsers = \App\Models\User::where('role', 'admin')->get();
        
        foreach ($adminUsers as $admin) {
            \Mail::to($admin->email)->send(new self('laundry_registration', $data, $admin));
        }
    }

    /**
     * Static method to send agent registration notification to admin
     */
    public static function sendAgentRegistrationNotification(\App\Models\User $agentUser)
    {
        $agent = $agentUser->agent;
        $data = [
            'user_name' => $agentUser->name,
            'user_email' => $agentUser->email,
            'user_phone' => $agentUser->phone,
            'agent_name' => $agent ? $agent->name : $agentUser->name,
            'agent_address' => $agent ? $agent->address : 'Not specified',
            'agent_phone' => $agent ? $agent->phone : $agentUser->phone,
            'registration_date' => $agentUser->created_at->format('Y-m-d H:i:s'),
            'user_id' => $agentUser->id
        ];

        // Get admin users
        $adminUsers = \App\Models\User::where('role', 'admin')->get();
        
        foreach ($adminUsers as $admin) {
            \Mail::to($admin->email)->send(new self('agent_registration', $data, $admin));
        }
    }

    /**
     * Static method to send welcome email to newly registered laundry
     */
    public static function sendLaundryWelcomeEmail(\App\Models\User $laundryUser)
    {
        $laundry = $laundryUser->laundry;
        $data = [
            'user_name' => $laundryUser->name,
            'laundry_name' => $laundry ? $laundry->name : $laundryUser->name,
            'registration_date' => $laundryUser->created_at->format('Y-m-d H:i:s'),
            'status' => $laundryUser->status,
            'next_steps' => $laundryUser->status === 'pending' ? 'Your account is pending admin approval. You will receive an email once approved.' : 'Your account is approved and ready to use.'
        ];

        \Mail::to($laundryUser->email)->send(new self('laundry_welcome', $data, $laundryUser));
    }

    /**
     * Static method to send welcome email to newly registered agent
     */
    public static function sendAgentWelcomeEmail(\App\Models\User $agentUser)
    {
        $agent = $agentUser->agent;
        $data = [
            'user_name' => $agentUser->name,
            'agent_name' => $agent ? $agent->name : $agentUser->name,
            'registration_date' => $agentUser->created_at->format('Y-m-d H:i:s'),
            'status' => $agentUser->status,
            'next_steps' => $agentUser->status === 'pending' ? 'Your account is pending admin approval. You will receive an email once approved.' : 'Your account is approved and ready to use.'
        ];

        \Mail::to($agentUser->email)->send(new self('agent_welcome', $data, $agentUser));
    }
}
