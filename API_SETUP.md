# Setup API Configuration untuk Frontend Web Admin

## Overview
Frontend Web Admin sudah dikonfigurasi dengan environment-based API URL. Ini memudahkan perubahan domain API tanpa perlu mengubah kode, hanya perlu mengubah environment variable.

## Initial Setup (Development)

### 1. Copy .env.example ke .env
```bash
cp .env.example .env
```

### 2. Generate Application Key
```bash
php artisan key:generate
```

### 3. Update API_BASE_URL di .env
Secara default sudah diset ke ngrok URL untuk testing:
```env
API_BASE_URL=https://passenger-pleat-footgear.ngrok-free.dev/api
```

Jika menggunakan API lokal, ubah menjadi:
```env
API_BASE_URL=http://localhost:8000/api
```

### 4. Install Dependencies
```bash
composer install
npm install
```

### 5. Build Assets
```bash
npm run dev        # untuk development
npm run build      # untuk production
```

## Menggunakan API Client

### Di Backend PHP
Gunakan `ApiClient` service class untuk membuat request ke API:

```php
<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;

class ExampleController extends Controller
{
    public function getData()
    {
        // GET request
        $response = ApiClient::get('/endpoint');
        
        // POST request
        $response = ApiClient::post('/endpoint', [
            'name' => 'John',
            'email' => 'john@example.com'
        ]);
        
        // Dengan authentication token
        $response = ApiClient::withToken($token)
            ->get('/protected-endpoint');
        
        // Handle response
        if ($response->successful()) {
            return $response->json();
        }
        
        return $response->throw();
    }
}
```

### Konfigurasi API di config/api.php
File `config/api.php` berisi konfigurasi lengkap:
- `base_url`: URL base API (dari env var API_BASE_URL)
- `timeout`: Timeout request (default 30 detik)
- `retry`: Konfigurasi retry untuk request yang gagal
- `headers`: Default headers untuk setiap request

## Deployment

### Production Environment
Ketika deploy ke production, cukup ubah environment variable `API_BASE_URL`:

#### 1. Menggunakan .env file
Edit `.env` di server production:
```env
API_BASE_URL=https://api.yourdomain.com/api
```

#### 2. Menggunakan Environment Variable di Server
Jika menggunakan container/cloud platform (Docker, AWS, Heroku, etc.):
```bash
export API_BASE_URL=https://api.yourdomain.com/api
```

#### 3. Untuk AWS EC2/Elastic Beanstalk
Set environment variable di configuration:
```
API_BASE_URL = https://api.yourdomain.com/api
```

#### 4. Untuk Docker
Di `Dockerfile` atau `docker-compose.yml`:
```dockerfile
ENV API_BASE_URL=https://api.yourdomain.com/api
```

Atau di `docker-compose.yml`:
```yaml
environment:
  - API_BASE_URL=https://api.yourdomain.com/api
```

## Testing

Untuk testing dengan API lokal, pastikan:
1. API server berjalan di `http://localhost:8000`
2. Update `.env` dengan:
   ```env
   API_BASE_URL=http://localhost:8000/api
   ```
3. Jalankan Laravel dev server:
   ```bash
   php artisan serve
   ```

## Environment Variables Reference

| Variable | Default | Keterangan |
|----------|---------|-----------|
| `API_BASE_URL` | `https://passenger-pleat-footgear.ngrok-free.dev/api` | Base URL untuk semua API request |
| `API_TIMEOUT` | `30` | Timeout untuk API request (dalam detik) |
| `API_RETRY_TIMES` | `3` | Jumlah retry untuk request yang gagal |
| `API_RETRY_DELAY` | `1000` | Delay antar retry (dalam milliseconds) |

## Tips & Best Practices

1. **Jangan commit .env ke repository** - .env sudah ada di `.gitignore`
2. **Selalu gunakan ApiClient** - Jangan hardcode API URL di controller/model
3. **Handle error response** - Selalu check apakah response successful sebelum mengakses data
4. **Use .env.example sebagai dokumentasi** - Jangan lupa update `.env.example` ketika menambah env variable baru

## Troubleshooting

### API request timeout
Ubah `API_TIMEOUT` di `.env`:
```env
API_TIMEOUT=60  # Naikkan menjadi 60 detik
```

### Connection refused
- Pastikan API URL benar di `.env`
- Pastikan API server sudah running
- Cek firewall/network configuration

### 401 Unauthorized
- Pastikan authentication token valid
- Check apakah token sudah expired
- Verify API credentials
