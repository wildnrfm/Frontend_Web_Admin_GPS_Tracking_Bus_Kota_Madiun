<?php

namespace App\Services;

/**
 * Example Usage of ApiClient Service
 * 
 * Ini adalah contoh bagaimana menggunakan ApiClient di aplikasi Anda.
 * Hapus file ini setelah Anda memahami cara menggunakannya.
 */

class ApiClientExample
{
    /**
     * Example: Fetch passengers from API
     */
    public static function getPassengers()
    {
        $response = ApiClient::get('/passengers');
        
        if ($response->successful()) {
            return $response->json();
        }
        
        // Handle error
        return [
            'error' => true,
            'status' => $response->status(),
            'message' => $response->body()
        ];
    }

    /**
     * Example: Create new passenger
     */
    public static function createPassenger($data)
    {
        $response = ApiClient::post('/passengers', $data);
        
        if ($response->successful()) {
            return $response->json();
        }
        
        return $response->throw();
    }

    /**
     * Example: Update passenger
     */
    public static function updatePassenger($id, $data)
    {
        $response = ApiClient::put("/passengers/{$id}", $data);
        
        if ($response->successful()) {
            return $response->json();
        }
        
        return $response->throw();
    }

    /**
     * Example: Delete passenger
     */
    public static function deletePassenger($id)
    {
        $response = ApiClient::delete("/passengers/{$id}");
        
        if ($response->successful()) {
            return $response->json();
        }
        
        return $response->throw();
    }

    /**
     * Example: Using authentication token
     */
    public static function getProtectedData($token)
    {
        $response = ApiClient::withToken($token)
            ->get('/protected-endpoint');
        
        if ($response->successful()) {
            return $response->json();
        }
        
        if ($response->unauthorized()) {
            return ['error' => 'Token expired or invalid'];
        }
        
        return $response->throw();
    }

    /**
     * Example: Get API base URL for frontend
     */
    public static function getApiBaseUrl()
    {
        return ApiClient::baseUrl();
    }

    /**
     * Example: Build full URL
     */
    public static function getFullUrl($endpoint)
    {
        return ApiClient::url($endpoint);
    }
}
