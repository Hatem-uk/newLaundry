<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Laundry;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RatingController extends Controller
{
    use ResponseTrait;

    /**
     * Store a newly created rating.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'laundry_id' => 'required|exists:laundries,id',
                'rating' => 'required|integer|between:1,5',
                'comment' => 'nullable|string|max:1000',
                'service_type' => 'nullable|string|in:washing,ironing,cleaning,agent_supply,other',
                'order_id' => 'nullable|exists:orders,id'
            ]);

            $customer = $this->getCustomerOrFail();
            
            if (Rating::hasCustomerRated($customer->id, $request->laundry_id, $request->order_id)) {
                return $this->errorResponse(null, 422, 'You have already rated this laundry for this order');
            }

            // Validate order if provided
            if ($request->order_id) {
                $this->validateOrderForRating($request->order_id, $request->user()->id, $request->laundry_id);
            }

            DB::beginTransaction();
            try {
                $rating = Rating::create([
                    'customer_id' => $customer->id,
                    'laundry_id' => $request->laundry_id,
                    'order_id' => $request->order_id,
                    'rating' => $request->rating,
                    'comment' => $request->comment,
                    'service_type' => $request->service_type
                ]);
                DB::commit();
                
                return $this->successResponse([
                    'rating' => $rating->load('customer.user')
                ], 201, 'Rating submitted successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $ex) {
            Log::error('Error storing rating: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to submit rating', $ex->getMessage());
        }
    }

    /**
     * Update the specified rating.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'rating' => 'required|integer|between:1,5',
                'comment' => 'nullable|string|max:1000',
                'service_type' => 'nullable|string|in:washing,ironing,cleaning,agent_supply,other'
            ]);

            $customer = $this->getCustomerOrFail();
            $rating = $this->getRatingOrFail($id, $customer->id);

            // Check if rating is within 24 hours
            if ($rating->created_at->diffInHours(now()) > 24) {
                return $this->errorResponse(null, 422, 'Rating can only be updated within 24 hours');
            }

            $rating->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
                'service_type' => $request->service_type
            ]);

            return $this->successResponse([
                'rating' => $rating->fresh()->load('customer.user')
            ], 200, 'Rating updated successfully');

        } catch (\Exception $ex) {
            Log::error('Error updating rating: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to update rating', $ex->getMessage());
        }
    }

    /**
     * Destroy the specified rating.
     */
    public function destroy($id)
    {
        try {
            $customer = auth()->user()->customer;
            if (!$customer) {
                return $this->notFoundResponse('Customer profile not found');
            }

            $rating = Rating::where('id', $id)
                ->where('customer_id', $customer->id)
                ->first();

            if (!$rating) {
                return $this->notFoundResponse('Rating not found');
            }

            // Check if rating is within 24 hours
            if ($rating->created_at->diffInHours(now()) > 24) {
                return $this->errorResponse(null, 422, 'Rating can only be deleted within 24 hours');
            }

            $rating->delete();

            return $this->successResponse(null, 200, 'Rating deleted successfully');

        } catch (\Exception $ex) {
            Log::error('Error deleting rating: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to delete rating', $ex->getMessage());
        }
    }

    public function getLaundryRatings(Request $request, $laundryId)
    {
        try {
            $laundry = Laundry::find($laundryId);
            if (!$laundry) {
                return $this->notFoundResponse('Laundry not found');
            }

            $query = Rating::where('laundry_id', $laundryId)
                ->with(['customer.user']);

            // Apply filters
            if ($request->has('rating')) {
                $query->where('rating', $request->rating);
            }

            if ($request->has('service_type')) {
                $query->where('service_type', $request->service_type);
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $ratings = $query->paginate($request->get('per_page', 15));

            return $this->successResponse([
                'ratings' => $ratings->items(),
                'pagination' => Helper::paginationMeta($ratings)
            ], 200, 'Laundry ratings retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting laundry ratings: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get ratings', $ex->getMessage());
        }
    }

    public function getCustomerRatings(Request $request)
    {
        try {
            $customer = $request->user()->customer;
            if (!$customer) {
                return $this->notFoundResponse('Customer profile not found');
            }

            $ratings = Rating::where('customer_id', $customer->id)
                ->with(['laundry.user', 'order'])
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));

            return $this->successResponse([
                'ratings' => $ratings->items(),
                'pagination' => Helper::paginationMeta($ratings)
            ], 200, 'Customer ratings retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting customer ratings: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get ratings', $ex->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $rating = Rating::with(['customer.user', 'laundry.user', 'order'])->find($id);

            if (!$rating) {
                return $this->notFoundResponse('Rating not found');
            }

            return $this->successResponse($rating, 200, 'Rating retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting rating: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get rating', $ex->getMessage());
        }
    }

    public function getRatingStats($laundryId)
    {
        try {
            $laundry = Laundry::find($laundryId);
            if (!$laundry) {
                return $this->notFoundResponse('Laundry not found');
            }

            $stats = [
                'average_rating' => Rating::getAverageRating($laundryId),
                'total_ratings' => Rating::getRatingCount($laundryId),
                'rating_distribution' => Rating::getRatingDistribution($laundryId),
                'recent_ratings' => Rating::where('laundry_id', $laundryId)
                    ->with(['customer.user'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
            ];

            return $this->successResponse($stats, 200, 'Rating statistics retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting rating stats: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get rating statistics', $ex->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {
            $request->validate([
                'query' => 'required|string|min:2',
                'laundry_id' => 'nullable|exists:laundries,id'
            ]);

            $query = Rating::with(['customer.user', 'laundry.user']);

            if ($request->laundry_id) {
                $query->where('laundry_id', $request->laundry_id);
            }

            $ratings = $query->where('comment', 'like', '%' . $request->query . '%')
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));

            return $this->successResponse([
                'ratings' => $ratings->items(),
                'pagination' => Helper::paginationMeta($ratings)
            ], 200, 'Search results retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error searching ratings: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to search ratings', $ex->getMessage());
        }
    }

    /**
     * Get customer profile or fail
     */
    private function getCustomerOrFail()
    {
        $customer = request()->user()->customer;
        
        if (!$customer) {
            throw new \Exception('Customer profile not found');
        }
        
        return $customer;
    }

    /**
     * Get rating or fail
     */
    private function getRatingOrFail($id, $customerId)
    {
        $rating = Rating::where('id', $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$rating) {
            throw new \Exception('Rating not found');
        }
        
        return $rating;
    }

    /**
     * Validate order for rating
     */
    private function validateOrderForRating($orderId, $userId, $laundryId): void
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->where('provider_id', $laundryId)
            ->first();

        if (!$order) {
            throw new \Exception('Invalid order for this laundry');
        }
    }
}
