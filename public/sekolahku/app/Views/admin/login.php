<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin – <?= $settings['site_name'] ?? 'SekolahKu' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.5.0/dist/css/coreui.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        * { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #dbeafe 50%, #e0e7ff 100%);
            position: relative;
            min-height: 100vh;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(59,130,246,0.06) 0%, transparent 70%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(99,102,241,0.05) 0%, transparent 70%);
            pointer-events: none;
        }
        .login-container {
            position: relative;
            z-index: 1;
        }
        .login-card {
            border: none;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.03), 0 20px 60px rgba(0,0,0,0.08), 0 8px 24px rgba(0,0,0,0.05);
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
        }
        .login-card .accent-bar {
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #6366f1);
        }
        .login-card .card-body {
            padding: 2.5rem 2.5rem 2rem;
        }
        .login-logo {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 12px rgba(59,130,246,0.25);
        }
        .login-logo i {
            font-size: 24px;
            color: #fff;
        }
        .login-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        .login-subtitle {
            font-size: 0.85rem;
            color: #94a3b8;
            font-weight: 400;
            margin-bottom: 1.75rem;
        }
        .form-floating-custom {
            position: relative;
            margin-bottom: 1rem;
        }
        .form-floating-custom .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
            z-index: 3;
            pointer-events: none;
            transition: color .2s;
        }
        .form-floating-custom .form-control {
            padding-left: 40px;
            height: 48px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            font-size: 0.9rem;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .form-floating-custom .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
            background: #fff;
        }
        .form-floating-custom .form-control:focus ~ .input-icon { color: #3b82f6; }
        .form-floating-custom label {
            position: absolute;
            left: 40px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.87rem;
            pointer-events: none;
            transition: all .2s;
            z-index: 2;
            background: transparent;
        }
        .form-floating-custom .form-control:focus ~ label,
        .form-floating-custom .form-control:not(:placeholder-shown) ~ label {
            top: 0;
            transform: translateY(-50%) scale(0.85);
            left: 12px;
            padding: 0 6px;
            background: #fff;
            color: #3b82f6;
        }
        .form-floating-custom .form-control::placeholder { color: transparent; }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            z-index: 3;
            font-size: 0.9rem;
        }
        .password-toggle:hover { color: #64748b; }
        .btn-login {
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border: none;
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            transition: transform .15s, box-shadow .2s;
            box-shadow: 0 4px 14px rgba(59,130,246,0.3);
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59,130,246,0.4);
        }
        .btn-login:active { transform: translateY(0); }
        .login-footer {
            font-size: 0.78rem;
            color: #94a3b8;
            margin-top: 1.5rem;
        }
        .alert-custom {
            border-radius: 12px;
            border: none;
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
            background: #fef2f2;
            color: #dc2626;
        }
        .alert-custom i { margin-right: 6px; }
    </style>
</head>
<body>
    <div class="login-container min-vh-100 d-flex align-items-center justify-content-center px-3">
        <div class="login-card" style="width:100%;max-width:420px">
            <div class="accent-bar"></div>
            <div class="card-body">
                <div class="login-logo">
                    <i class="fas <?= $settings['site_logo_icon'] ?? 'fa-graduation-cap' ?>"></i>
                </div>
                <h1 class="login-title text-center">Login Admin</h1>
                <p class="login-subtitle text-center">Masuk ke panel <?= $settings['site_name'] ?? 'SekolahKu' ?></p>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-custom d-flex align-items-center">
                        <i class="fas fa-circle-exclamation"></i>
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                <?php endif; ?>

                <form action="/login/attempt" method="post">
                    <?= csrf_field() ?>
                    <div class="form-floating-custom">
                        <input type="text" name="username" class="form-control" id="username" placeholder="Username" required autofocus autocomplete="username">
                        <i class="fas fa-user input-icon"></i>
                        <label for="username">Username atau Email</label>
                    </div>
                    <div class="form-floating-custom">
                        <input type="password" name="password" class="form-control" id="password" placeholder="Password" required autocomplete="current-password">
                        <i class="fas fa-lock input-icon"></i>
                        <label for="password">Kata Sandi</label>
                        <button type="button" class="password-toggle" onclick="togglePassword()" tabindex="-1" aria-label="Toggle password visibility">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    <button type="submit" class="btn btn-login w-100">
                        <i class="fas fa-arrow-right-to-bracket me-2"></i>Masuk
                    </button>
                </form>

                <p class="login-footer text-center">
                    &copy; <?= date('Y') ?> <?= $settings['site_name'] ?? 'SekolahKu' ?>. All rights reserved.<br>
                    CMS by <a href="https://sekolahku.web.id" target="_blank" class="text-decoration-none fw-medium" style="color:#6366f1">sekolahku.web.id</a>
                </p>
            </div>
        </div>
    </div>

    <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.querySelector('.password-toggle i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'far fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'far fa-eye';
        }
    }
    </script>
</body>
</html>