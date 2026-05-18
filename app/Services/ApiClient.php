<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Client\Response;

class ApiClient
{
    /**
     * Get the base URL for API requests
     */
    public static function baseUrl(): string
    {
        return config('api.base_url');
    }

    /**
     * Get headers with authentication token if available
     */
    private static function getHeaders(bool $withAuth = true): array
    {
        $headers = config('api.headers');

        if ($withAuth) {
            $token = Session::get('api_token');
            if ($token) {
                $headers['Authorization'] = 'Bearer ' . $token;
            }
        }

        return $headers;
    }

    /**
     * Get base HTTP client with common configuration
     */
    private static function client()
    {
        $http = Http::timeout(config('api.timeout'));
        
        // Skip SSL verification for development/ngrok
        if (config('app.env') === 'local' || config('app.debug')) {
            $http = $http->withoutVerifying();
        }
        
        return $http;
    }

    /**
     * Make a GET request to the API
     */
    public static function get(string $endpoint, array $query = [], bool $withAuth = true): Response
    {
        $response = self::client()
            ->withHeaders(self::getHeaders($withAuth))
            ->get(self::baseUrl() . $endpoint, $query);

        self::handleUnauthorized($response);
        return $response;
    }

    /**
     * Make a POST request to the API
     */
    public static function post(string $endpoint, array $data = [], bool $withAuth = true): Response
    {
        $response = self::client()
            ->withHeaders(self::getHeaders($withAuth))
            ->post(self::baseUrl() . $endpoint, $data);

        self::handleUnauthorized($response);
        return $response;
    }

    /**
     * Make a PUT request to the API
     */
    public static function put(string $endpoint, array $data = [], bool $withAuth = true): Response
    {
        $response = self::client()
            ->withHeaders(self::getHeaders($withAuth))
            ->put(self::baseUrl() . $endpoint, $data);

        self::handleUnauthorized($response);
        return $response;
    }

    /**
     * Make a DELETE request to the API
     */
    public static function delete(string $endpoint, bool $withAuth = true): Response
    {
        $response = self::client()
            ->withHeaders(self::getHeaders($withAuth))
            ->delete(self::baseUrl() . $endpoint);

        self::handleUnauthorized($response);
        return $response;
    }

    /**
     * Make a PATCH request to the API
     */
    public static function patch(string $endpoint, array $data = [], bool $withAuth = true): Response
    {
        $response = self::client()
            ->withHeaders(self::getHeaders($withAuth))
            ->patch(self::baseUrl() . $endpoint, $data);

        self::handleUnauthorized($response);
        return $response;
    }

    /**
     * Make a request with explicit authentication token
     */
    public static function withToken(string $token): \Illuminate\Http\Client\PendingRequest
    {
        $http = self::client()->withHeaders(config('api.headers'));
        return $http->withToken($token);
    }

    /**
     * Build full URL for API endpoint
     */
    public static function url(string $endpoint): string
    {
        return self::baseUrl() . $endpoint;
    }

    /**
     * Handle unauthorized responses (401)
     * Clears session and triggers logout
     */
    private static function handleUnauthorized(Response $response): void
    {
        if ($response->status() === 401) {
            // Clear session data if token is invalid
            Session::forget('api_token');
            Session::forget('admin_user');
            Session::forget('login_time');
            Session::forget('device_id');
            Session::flush();
        }
    }
}
