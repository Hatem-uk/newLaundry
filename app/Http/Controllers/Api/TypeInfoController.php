<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypeInfo;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Log;
use App\Traits\ResponseTrait;

class TypeInfoController extends Controller
{
    use ResponseTrait;

    /**
     * Get all records (optionally filtered by type).
     */
    public function index(Request $request)
    {
        try {
            $request->validate([
                'type' => 'nullable|in:terms,whoWeAre,condition'
            ]);
        
            $locale = app()->getLocale();
            $query = TypeInfo::query();
        
            if ($request->filled('type')) {
                $typeInfo = $query->where('type', $request->type)->first();
                
                if (!$typeInfo) {
                    return $this->notFoundResponse('No records found for this type');
                }
        
                $formattedData = $this->formatTypeInfoData($typeInfo, $locale);
        
                return $this->successResponse($formattedData, 200, 'Type info retrieved successfully');
            }
        
            $allTypeInfos = $query->latest()->paginate(10)->through(function ($item) use ($locale) {
                return $this->formatTypeInfoData($item, $locale);
            });
        
            return $allTypeInfos->isNotEmpty()
                ? $this->successResponse($allTypeInfos, 200, 'All type infos retrieved successfully')
                : $this->notFoundResponse('No type infos found');

        } catch (\Exception $ex) {
            Log::error('Error getting TypeInfo: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get type infos', $ex->getMessage());
        }
    }

    /**
     * Store new record.
     */
    public function store(Request $request)
    {
        try {
            $data = $this->checkData($request);
            $typeInfo = TypeInfo::create($data);

            return $this->successResponse($typeInfo, 201, 'Record created successfully');

        } catch (\Exception $ex) {
            Log::error('Error storing TypeInfo: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to create record', $ex->getMessage());
        }
    }

    /**
     * Show single record.
     */
    public function show($id)
    {
        try {
            $typeInfo = $this->getTypeInfoOrFail($id);

            return $this->successResponse($typeInfo, 200, 'Record retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting TypeInfo: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get record', $ex->getMessage());
        }
    }

    /**
     * Update record.
     */
    public function update(Request $request, $id)
    {
        try {
            $typeInfo = $this->getTypeInfoOrFail($id);
            $data = $this->checkData($request, $typeInfo);
            $typeInfo->update($data);

            return $this->successResponse($typeInfo->fresh(), 200, 'Record updated successfully');

        } catch (\Exception $ex) {
            Log::error('Error updating TypeInfo: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to update record', $ex->getMessage());
        }
    }

    /**
     * Delete record.
     */
    public function destroy($id)
    {
        try {
            $typeInfo = $this->getTypeInfoOrFail($id);
            $typeInfo->delete();

            return $this->successResponse(null, 200, 'Record deleted successfully');

        } catch (\Exception $ex) {
            Log::error('Error deleting TypeInfo: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to delete record', $ex->getMessage());
        }
    }

    /**
     * Get TypeInfo or fail
     */
    private function getTypeInfoOrFail($id)
    {
        $typeInfo = TypeInfo::find($id);

        if (!$typeInfo) {
            throw new \Exception('Record not found');
        }
        
        return $typeInfo;
    }

    /**
     * Format TypeInfo data for response
     */
    private function formatTypeInfoData(TypeInfo $typeInfo, string $locale): array
    {
        return [
            'id' => $typeInfo->id,
            'type' => $typeInfo->type,
            'name' => $typeInfo->getTranslation('name', $locale, false) ?? $typeInfo->getTranslation('name', 'en'),
            'description' => $typeInfo->getTranslation('description', $locale, false) ?? $typeInfo->getTranslation('description', 'en')
        ];
    }

    /**
     * Validate and prepare TypeInfo data with translations
     */
    private function checkData(Request $request, ?TypeInfo $typeInfo = null): array
    {
        $rules = [
            'type' => 'required|in:terms,whoWeAre,condition',
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
        ];

        // For update, make type optional
        if ($typeInfo !== null) {
            $rules['type'] = 'sometimes|'.$rules['type'];
        }

        $validated = $request->validate($rules);

        return [
            'name' => Helper::translateData($request, 'name_ar', 'name_en') 
                     ?? optional($typeInfo)->name, // Keep existing if not provided
            'description' => Helper::translateData($request, 'description_ar', 'description_en') 
                           ?? optional($typeInfo)->description,
            'type' => $validated['type'] ?? $typeInfo->type,
        ];
    }
}