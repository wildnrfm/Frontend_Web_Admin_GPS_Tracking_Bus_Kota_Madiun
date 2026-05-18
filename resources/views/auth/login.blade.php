<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Admin Login - {{ config('app.name', 'Bus Tracking System') }}</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }

.ml-page {
  min-height: 100vh;
  background: #EEF0EE;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 32px 24px;
}

.ml-wrap {
  width: 100%;
  max-width: 420px;
  background: #fff;
  border-radius: 24px;
  padding: 40px 36px 32px;
  box-shadow: 0 8px 40px rgba(0,0,0,.10);
}

/* Logo */
.ml-logo {
  display: flex;
  justify-content: center;
  margin-bottom: 36px;
}
.ml-logo img {
  width: 90px;
  height: 90px;
  object-fit: contain;
}

/* Title */
.ml-title {
  font-size: 36px;
  font-weight: 800;
  line-height: 1.15;
  color: #1a1a1a;
  margin-bottom: 10px;
  letter-spacing: -0.5px;
}
.ml-title span { color: #1B5E37; }

.ml-sub {
  font-size: 14px;
  color: #888;
  margin-bottom: 36px;
  font-weight: 400;
}

/* Alert error */
.ml-alert {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #FFF0F0;
  border: 1.5px solid #FFD5D5;
  border-radius: 12px;
  padding: 12px 16px;
  margin-bottom: 20px;
  font-size: 13px;
  color: #c0392b;
  font-weight: 500;
}

/* Form groups */
.ml-group {
  margin-bottom: 16px;
}
.ml-label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

/* Input wrapper */
.ml-input-wrap {
  position: relative;
}
.ml-input {
  width: 100%;
  background: #F4F6F5;
  border: 1.5px solid #F4F6F5;
  border-radius: 14px;
  padding: 15px 18px;
  font-size: 15px;
  color: #1a1a1a;
  outline: none;
  transition: border-color .2s ease, background .2s ease;
  font-family: inherit;
}
.ml-input::placeholder { color: #bbb; }
.ml-input:focus {
  background: #fff;
  border-color: #1B5E37;
}

/* Password eye toggle */
.ml-input.has-toggle { padding-right: 52px; }
.ml-eye {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #aaa;
  display: flex;
  align-items: center;
  padding: 4px;
  transition: color .2s;
}
.ml-eye:hover { color: #1B5E37; }
.ml-eye .material-icons { font-size: 20px; }

/* Validation error */
.ml-err { font-size: 12px; color: #e74c3c; margin-top: 5px; display: block; }

/* Submit button */
.ml-btn {
  width: 100%;
  background: #1B5E37;
  color: #fff;
  border: none;
  border-radius: 14px;
  padding: 17px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 8px;
  transition: background .2s, transform .1s;
  font-family: inherit;
  letter-spacing: 0.3px;
}
.ml-btn:hover { background: #154d2c; }
.ml-btn:active { transform: scale(.98); }
.ml-btn:disabled {
  background: #9dbdac;
  cursor: not-allowed;
  transform: none;
}
.ml-btn .material-icons { font-size: 20px; }

/* Spinner */
.ml-spinner {
  width: 18px; height: 18px;
  border: 2.5px solid rgba(255,255,255,.4);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Footer */
.ml-footer {
  text-align: center;
  font-size: 12px;
  color: #aaa;
  margin-top: 28px;
  font-weight: 400;
}
</style>

<div class="ml-page">
  <div class="ml-wrap">

    {{-- Logo --}}
    <div class="ml-logo">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
    </div>

    {{-- Heading --}}
    <h1 class="ml-title">Selamat Datang<br><span>Kembali</span></h1>
    <p class="ml-sub">Masuk ke akun admin Anda untuk melanjutkan</p>

    {{-- Error --}}
    @if(session('error'))
    <div class="ml-alert">
      <span class="material-icons" style="font-size:18px">error_outline</span>
      {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="ml-alert">
      <span class="material-icons" style="font-size:18px">error_outline</span>
      @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('login.post') }}" method="POST" id="login-form">
      @csrf

      <div class="ml-group">
        <label class="ml-label" for="email">Email</label>
        <div class="ml-input-wrap">
          <input id="email" class="ml-input" type="email" name="email"
            placeholder="admin@example.com"
            value="{{ old('email') }}" required autocomplete="email">
        </div>
        @error('email')<span class="ml-err">{{ $message }}</span>@enderror
      </div>

      <div class="ml-group">
        <label class="ml-label" for="password">Password</label>
        <div class="ml-input-wrap">
          <input id="password" class="ml-input has-toggle" type="password"
            name="password" placeholder="Masukkan password"
            required autocomplete="current-password">
          <button type="button" class="ml-eye" id="toggle-pw" onclick="togglePw()" tabindex="-1">
            <span class="material-icons" id="eye-icon">visibility</span>
          </button>
        </div>
        @error('password')<span class="ml-err">{{ $message }}</span>@enderror
      </div>

      <button type="submit" class="ml-btn" id="submit-btn">
        <span class="material-icons">login</span>
        Masuk
      </button>
    </form>

    <p class="ml-footer">Hanya untuk akun dengan role Admin</p>
  </div>
</div>

<script>
function togglePw() {
  const inp = document.getElementById('password');
  const ico = document.getElementById('eye-icon');
  if (inp.type === 'password') {
    inp.type = 'text';
    ico.textContent = 'visibility_off';
  } else {
    inp.type = 'password';
    ico.textContent = 'visibility';
  }
}
document.getElementById('login-form').addEventListener('submit', function() {
  const btn = document.getElementById('submit-btn');
  btn.disabled = true;
  btn.innerHTML = '<div class="ml-spinner"></div> Memuat...';
});
</script>

</body>
</html>
