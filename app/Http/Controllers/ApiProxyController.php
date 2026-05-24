<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * ApiProxyController
 *
 * Proxy semua request API dari browser melalui backend web admin.
 * Browser → /api-proxy/* (same origin, no CORS)
 * Web admin → API backend (server-to-server, no CORS)
 *
 * Juga: /storage-proxy/* untuk gambar/file dari API storage.
 */
class ApiProxyController extends Controller
{
    /**
     * Dapatkan root URL API (tanpa /api di akhir).
     * Contoh: https://xxxx.ngrok-free.dev/api → https://xxxx.ngrok-free.dev
     */
    private function apiRootUrl(): string
    {
        $apiBase = rtrim(env('API_BASE_URL', 'http://localhost:8000/api'), '/');
        return preg_replace('#/api$#', '', $apiBase);
    }

    /**
     * Proxy untuk file storage (gambar bus, foto profil, dll).
     * Browser:  GET /storage-proxy/storage/buses/photo.jpg
     * Forwarded: GET https://api-server/storage/buses/photo.jpg
     */
    public function storageProxy(Request $request, string $path)
    {
        $root = $this->apiRootUrl();
        $url  = $root . '/' . ltrim($path, '/');

        $headers = [];
        if (str_contains($root, 'ngrok')) {
            $headers['ngrok-skip-browser-warning'] = 'true';
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(15)
                ->withoutVerifying()
                ->get($url);

            if (!$response->successful()) {
                abort(404);
            }

            return response($response->body(), 200, [
                'Content-Type'  => $response->header('Content-Type', 'application/octet-stream'),
                'Cache-Control' => 'public, max-age=86400',
            ]);

        } catch (\Exception $e) {
            abort(502);
        }
    }

    /**
     * Proxy untuk semua API endpoint.
     * Browser:  ANY /api-proxy/{path}
     * Forwarded: ANY {API_BASE_URL}/{path}
     */
    public function proxy(Request $request, string $path)
    {
        $apiBase = rtrim(env('API_BASE_URL', 'http://localhost:8000/api'), '/');
        $url = $apiBase . '/' . $path;

        if ($request->getQueryString()) {
            $url .= '?' . $request->getQueryString();
        }

        $headers = ['Accept' => 'application/json'];

        if ($request->hasHeader('Authorization')) {
            $headers['Authorization'] = $request->header('Authorization');
        }

        if (str_contains($apiBase, 'ngrok')) {
            $headers['ngrok-skip-browser-warning'] = 'true';
        }

        $method = strtolower($request->method());

        try {
            $httpRequest = Http::withHeaders($headers)
                ->timeout(30)
                ->withoutVerifying();

            // Multipart/file upload
            if ($request->hasFile('photo') || $request->hasFile('image') || $request->hasFile('file')) {
                $httpRequest = $httpRequest->asMultipart();

                foreach ($request->allFiles() as $key => $file) {
                    if (is_array($file)) {
                        foreach ($file as $f) {
                            $httpRequest = $httpRequest->attach($key . '[]', $f->getContent(), $f->getClientOriginalName());
                        }
                    } else {
                        $httpRequest = $httpRequest->attach($key, $file->getContent(), $file->getClientOriginalName());
                    }
                }

                foreach ($request->except(array_keys($request->allFiles())) as $key => $value) {
                    if (!is_null($value)) {
                        $httpRequest = $httpRequest->attach($key, (string) $value);
                    }
                }

                $response = $httpRequest->post($url);

            } elseif (in_array($method, ['post', 'put', 'patch', 'delete'])) {
                $contentType = $request->header('Content-Type', '');

                if (str_contains($contentType, 'json') || $request->isJson()) {
                    $response = $httpRequest->withBody($request->getContent(), 'application/json')->$method($url);
                } else {
                    $response = $httpRequest->$method($url, $request->all());
                }
            } else {
                $response = $httpRequest->$method($url);
            }

            $responseHeaders = [];
            if ($ct = $response->header('Content-Type')) {
                $responseHeaders['Content-Type'] = $ct;
            }

            return response($response->body(), $response->status(), $responseHeaders);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Proxy error: tidak bisa menghubungi API backend',
                'error'   => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 502);
        }
    }
}
