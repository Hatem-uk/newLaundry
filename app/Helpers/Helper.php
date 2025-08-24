<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Helper
{
    /**
     * Translate data for multilingual support
     *
     * @param \Illuminate\Http\Request $request
     * @param string $arabic
     * @param string $english
     * @return array|null
     */
    public static function translateData($request, string $arabic, string $english): ?array
    {
        $arabicValue = $request->input($arabic);
        $englishValue = $request->input($english);

        // Return null if both values are empty
        if (empty($arabicValue) && empty($englishValue)) {
            return null;
        }

        return [
            'ar' => $arabicValue ?: '',
            'en' => $englishValue ?: ''
        ];
    }

    /**
     * Generate success message
     *
     * @param string $type
     * @return array
     */
    public static function messageSuccess(string $type): array
    {
        return [
            'type' => 'success',
            'title' => __('Success!'),
            'message' => __("$type successfully.")
        ];
    }

    /**
     * Generate custom message
     *
     * @param string $type
     * @param string $key
     * @param string $msg
     * @return array
     */
    public static function customMessage(string $type, string $key, string $msg): array
    {
        return [
            'type' => $type,
            'title' => __($key),
            'message' => __($msg)
        ];
    }

    /**
     * Generate error message
     *
     * @return array
     */
    public static function messageError(): array
    {
        return [
            'type' => 'error',
            'title' => __('Error!'),
            'message' => __('Something went wrong.')
        ];
    }

    /**
     * Generate error message from exception
     *
     * @param \Exception $ex
     * @return array
     */
    public static function messageErrorException(\Exception $ex): array
    {
        Log::error('Helper error exception', [
            'message' => $ex->getMessage(),
            'file' => $ex->getFile(),
            'line' => $ex->getLine()
        ]);

        return [
            'type' => 'danger',
            'title' => __('Error!'),
            'message' => config('app.debug') ? $ex->getMessage() : __('Something went wrong.')
        ];
    }

    /**
     * Generate status display HTML
     *
     * @param string|int $status
     * @return string
     */
    public static function statusShow($status): string
    {
        $statusMap = [
            '1' => ['class' => 'badge-success', 'text' => 'Active'],
            '0' => ['class' => 'badge-danger', 'text' => 'Inactive'],
            'approved' => ['class' => 'badge-success', 'text' => 'Approved'],
            'pending' => ['class' => 'badge-warning', 'text' => 'Pending'],
            'rejected' => ['class' => 'badge-danger', 'text' => 'Rejected'],
            'disapproved' => ['class' => 'badge-danger', 'text' => 'Disapproved']
        ];

        $statusConfig = $statusMap[$status] ?? ['class' => 'badge-secondary', 'text' => 'Unknown'];

        return sprintf(
            '<span class="badge %s">%s</span>',
            $statusConfig['class'],
            __($statusConfig['text'])
        );
    }

    /**
     * Upload file with validation and error handling
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param array $allowedMimes
     * @param int $maxSize
     * @return string|null
     */
    public static function uploadFile(
        UploadedFile $file, 
        string $directory, 
        array $allowedMimes = ['jpeg', 'png', 'jpg', 'gif', 'pdf'],
        int $maxSize = 2048
    ): ?string {
        try {
            // Validate file
            if (!$file->isValid()) {
                Log::warning('File upload failed - invalid file', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'error' => $file->getError()
                ]);
                return null;
            }

            // Check file size (in KB)
            if ($file->getSize() > ($maxSize * 1024)) {
                Log::warning('File upload failed - file too large', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'max_size' => $maxSize * 1024
                ]);
                return null;
            }

            // Check MIME type
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, $allowedMimes)) {
                Log::warning('File upload failed - invalid MIME type', [
                    'original_name' => $file->getClientOriginalName(),
                    'extension' => $extension,
                    'allowed_types' => $allowedMimes
                ]);
                return null;
            }

            // Generate unique filename
            $filename = Str::uuid() . '.' . $extension;
            
            // Ensure directory exists
            $fullDirectory = "public/{$directory}";
            if (!Storage::exists($fullDirectory)) {
                Storage::makeDirectory($fullDirectory);
            }

            // Store file
            $path = $file->storeAs($fullDirectory, $filename);
            
            // Return relative path
            $relativePath = str_replace('public/', '', $path);
            
            Log::info('File uploaded successfully', [
                'original_name' => $file->getClientOriginalName(),
                'stored_path' => $relativePath,
                'size' => $file->getSize()
            ]);

            return $relativePath;

        } catch (\Exception $e) {
            Log::error('File upload exception', [
                'original_name' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Delete file safely
     *
     * @param string|null $filePath
     * @return bool
     */
    public static function deleteFile(?string $filePath): bool
    {
        if (empty($filePath)) {
            return true;
        }

        try {
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                Log::info('File deleted successfully', ['path' => $filePath]);
                return true;
            }
            return true; // File doesn't exist, consider it deleted
        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate pagination metadata
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @return array
     */
    public static function paginationMeta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem()
        ];
    }
}
