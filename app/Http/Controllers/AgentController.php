<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agent;
use App\Models\City;
use App\Models\Laundry;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AgentController extends Controller
{
    /**
     * Show agent dashboard
     */
    public function dashboard()
    {
        $agent = auth()->user();
        $agentProfile = $agent->agent;
        
        // Check if agent profile exists
        if (!$agentProfile) {
            return redirect()->route('agent.profile')->with('error', __('dashboard.Please complete your profile first'));
        }
        
        // Get agent's cities - ensure it's always an array
        $agentCities = $agentProfile->service_areas ? $agentProfile->service_areas : [];
        $cities = !empty($agentCities) ? City::whereIn('id', $agentCities)->get() : collect();
        
        // Get laundries in agent's cities
        $laundries = !empty($agentCities) ? Laundry::whereIn('city_id', $agentCities)
                           ->with('user')
                           ->where('is_active', true)
                           ->get() : collect();
        
        // Get statistics
        $stats = [
            'total_cities' => count($agentCities),
            'total_laundries' => $laundries->count(),
            'active_laundries' => $laundries->where('status', 'online')->count(),
            'offline_laundries' => $laundries->where('status', 'offline')->count()
        ];

        return view('agent.dashboard', compact('agent', 'agentProfile', 'cities', 'laundries', 'stats'));
    }

    /**
     * Show agent profile
     */
    public function profile()
    {
        $agent = auth()->user();
        $agentProfile = $agent->agent;
        
        // Create agent profile if it doesn't exist
        if (!$agentProfile) {
            $agentProfile = $agent->agent()->create([
                'name' => $agent->name,
                'phone' => $agent->phone,
                'status' => 'offline',
                'is_active' => true,
                'working_hours' => $this->getDefaultWorkingHours(),
                'service_areas' => [],
                'specializations' => []
            ]);
        }
        
        $allCities = City::all();
        $agentCities = $agentProfile->service_areas ? 
            City::whereIn('id', $agentProfile->service_areas)->get() : collect();

        return view('agent.profile', compact('agent', 'agentProfile', 'allCities', 'agentCities'));
    }

    /**
     * Update agent profile
     */
    public function updateProfile(Request $request)
    {
        $agent = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($agent->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'license_number' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'working_hours' => 'nullable|array',
            'service_areas' => 'nullable|array',
            'specializations' => 'nullable|array',
            'status' => 'required|in:online,offline,maintenance'
        ]);

        // Update user
        $agent->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Handle logo upload
        $logoPath = $agent->agent ? $agent->agent->logo : null;
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $request->file('logo')->store('agent-logos', 'public');
        }

        // Update or create agent profile
        if ($agent->agent) {
            $agent->agent->update([
                'name' => $request->name,
                'license_number' => $request->license_number,
                'phone' => $request->phone,
                'address' => $request->address,
                'logo' => $logoPath,
                'status' => $request->status,
                'working_hours' => $request->working_hours ?? $agent->agent->working_hours,
                'service_areas' => $request->service_areas ?? [],
                'specializations' => $request->specializations ?? []
            ]);
        } else {
            // Create agent profile if it doesn't exist
            $agent->agent()->create([
                'name' => $request->name,
                'license_number' => $request->license_number,
                'phone' => $request->phone,
                'address' => $request->address,
                'logo' => $logoPath,
                'status' => $request->status,
                'is_active' => true,
                'working_hours' => $request->working_hours ?? $this->getDefaultWorkingHours(),
                'service_areas' => $request->service_areas ?? [],
                'specializations' => $request->specializations ?? []
            ]);
        }

        return redirect()->route('agent.profile')->with('success', __('dashboard.Profile updated successfully'));
    }

    /**
     * Show laundries in agent's cities
     */
    public function laundries(Request $request)
    {
        $agent = auth()->user();
        $agentProfile = $agent->agent;
        
        if (!$agentProfile) {
            return redirect()->route('agent.profile')->with('error', __('dashboard.Please complete your profile first'));
        }

        $agentCities = $agentProfile->service_areas ?? [];
        
        if (empty($agentCities)) {
            return redirect()->route('agent.profile')->with('error', __('dashboard.Please add cities to your service areas first'));
        }

        $query = Laundry::whereIn('city_id', $agentCities)
                       ->with(['user', 'city']);

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $laundries = $query->orderBy('created_at', 'desc')->paginate(20);
        $cities = City::whereIn('id', $agentCities)->get();

        return view('agent.laundries', compact('laundries', 'cities', 'agentCities'));
    }

    /**
     * Show specific laundry details
     */
    public function showLaundry($id)
    {
        $agent = auth()->user();
        $agentProfile = $agent->agent;
        
        // Check if agent profile exists
        if (!$agentProfile) {
            return redirect()->route('agent.profile')->with('error', __('dashboard.Please complete your profile first'));
        }
        
        $agentCities = $agentProfile->service_areas ? $agentProfile->service_areas : [];

        $laundry = Laundry::whereIn('city_id', $agentCities)
                         ->with(['user', 'city', 'services'])
                         ->findOrFail($id);

        return view('agent.laundry-details', compact('laundry'));
    }

    /**
     * Show cities management
     */
    public function cities()
    {
        $agent = auth()->user();
        $agentProfile = $agent->agent;
        
        // Check if agent profile exists
        if (!$agentProfile) {
            return redirect()->route('agent.profile')->with('error', __('dashboard.Please complete your profile first'));
        }
        
        $allCities = City::all();
        $agentCities = $agentProfile->service_areas ? 
            City::whereIn('id', $agentProfile->service_areas)->get() : collect();

        return view('agent.cities', compact('allCities', 'agentCities'));
    }

    /**
     * Update agent cities
     */
    public function updateCities(Request $request)
    {
        $agent = auth()->user();
        $agentProfile = $agent->agent;

        $request->validate([
            'service_areas' => 'required|array|min:1',
            'service_areas.*' => 'exists:cities,id'
        ]);

        if (!$agentProfile) {
            return redirect()->route('agent.profile')->with('error', __('dashboard.Please complete your profile first'));
        }

        $agentProfile->update([
            'service_areas' => $request->service_areas
        ]);

        return redirect()->route('agent.cities')->with('success', __('dashboard.Cities updated successfully'));
    }

    /**
     * Get default working hours
     */
    private function getDefaultWorkingHours()
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
}
