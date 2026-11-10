<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول — نظام إدارة الشحنات</title>
    <link rel="stylesheet" href="{{ asset("vendor/bootstrap/bootstrap.rtl.min.css") }}">
    <link rel="stylesheet" href="{{ asset("vendor/bootstrap-icons/bootstrap-icons.min.css") }}">
    <link rel="stylesheet" href="{{ asset("css/design-system.css") }}">
    <style>
        


        body {
            font-family: 'Cairo', 'IBM Plex Sans Arabic', sans-serif;
            background-color: var(--bg);
            color: var(--primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }

        /* الحاوية المركزية لصفحة الدخول */
        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(32, 40, 51, 0.08);
            border: 1px solid rgba(199, 166, 103, 0.25);
            padding: 40px 32px;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--secondary), var(--accent));
        }

        /* الشعار ورأس الصفحة */
        .brand-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-icon {
            width: 54px;
            height: 54px;
            background: var(--primary);
            color: var(--accent);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 14px;
            box-shadow: 0 4px 14px rgba(32, 40, 51, 0.15);
        }

        .brand-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .brand-subtitle {
            font-size: 0.85rem;
            color: var(--muted);
            font-weight: 500;
        }

        /* نموذج الإدخال */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--primary);
            margin-bottom: 8px;
            display: block;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .input-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 1rem;
            pointer-events: none;
            transition: var(--tr);
        }

        .input-wrap input {
            width: 100%;
            padding: 12px 42px 12px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            font-family: 'Cairo', 'IBM Plex Sans Arabic', sans-serif;
            font-size: 0.9rem;
            color: var(--primary);
            background: #ffffff;
            transition: var(--tr);
            outline: none;
        }

        .input-wrap input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(199, 166, 103, 0.15);
        }

        .input-wrap input:focus + .input-icon {
            color: var(--accent);
        }

        .pw-toggle {
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            font-size: 0.95rem;
            cursor: pointer;
            padding: 6px;
            min-width: 36px;
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--tr);
        }

        .pw-toggle:hover {
            color: var(--secondary);
        }

        /* خيارات التذكر ونسيان كلمة المرور */
        .login-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .remember-check {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--secondary);
            cursor: pointer;
        }

        .remember-check span {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--muted);
        }

        /* زر تسجيل الدخول */
        .btn-login {
            width: 100%;
            padding: 13px;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: var(--r-sm);
            font-family: 'Cairo', 'IBM Plex Sans Arabic', sans-serif;
            font-size: 0.95rem;
            font-weight: 800;
            cursor: pointer;
            transition: var(--tr);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(32, 40, 51, 0.2);
        }

        /* روابط التذييل والعودة */
        .back-home {
            text-align: center;
            margin-top: 20px;
        }

        .back-home a {
            color: var(--secondary);
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            transition: var(--tr);
        }

        .back-home a:hover {
            color: var(--accent);
        }

        .login-footer {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid var(--border);
            text-align: center;
            font-size: 0.75rem;
            color: var(--muted);
        }

        .alert-login {
            background: #FEF2F2;
            border-right: 4px solid #EF4444;
            border-radius: var(--r-sm);
            padding: 10px 14px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #991B1B;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        
        <!-- الهيدر الشعار والعنوان -->
        <div class="brand-header">
            <div class="brand-icon">
                <i class="bi bi-truck-front-fill"></i>
            </div>
            <h1 class="brand-title">نظام إدارة الشحنات</h1>
            <p class="brand-subtitle">أدخل بيانات حسابك للوصول إلى لوحة التحكم</p>
        </div>

        <!-- التنبيه في حالة الأخطاء -->
        @if($errors->any())
        <div class="alert-login">
            <i class="bi bi-shield-x-fill"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <!-- النموذج -->
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <!-- البريد الإلكتروني -->
            <div class="form-group">
                <label class="form-label" for="email">البريد الإلكتروني</label>
                <div class="input-wrap">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="" required autocomplete="email" autofocus class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
                    <i class="bi bi-envelope input-icon"></i>
                </div>
            </div>

            <!-- كلمة المرور -->
            <div class="form-group">
                <label class="form-label" for="password">كلمة المرور</label>
                <div class="input-wrap">
                    <input id="password" type="password" name="password" placeholder="••••••••" required autocomplete="current-password" class="{{ $errors->has('password') ? 'is-invalid' : '' }}">
                    <i class="bi bi-lock input-icon"></i>
                    <button type="button" class="pw-toggle" onclick="togglePw()" tabindex="-1">
                        <i class="bi bi-eye" id="pwEyeIcon"></i>
                    </button>
                </div>
            </div>

            <!-- خيار التذكر ونسيان كلمة المرور -->
            <div class="login-options">
                <label class="remember-check">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>تذكرني</span>
                </label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="font-size:.82rem; font-weight:700; color:var(--accent); text-decoration:none;">
                        نسيت كلمة المرور؟
                    </a>
                @endif
            </div>

            <!-- زر تسجيل الدخول -->
            <button type="submit" class="btn-login" id="loginBtn">
                <i class="bi bi-box-arrow-in-right" id="loginIcon"></i>
                <span id="loginText">دخول إلى النظام</span>
            </button>
        </form>

        <!-- رابط العودة للرئيسية -->
        <div class="back-home">
            <a href="{{ url('/') }}">
                <i class="bi bi-arrow-right ms-1"></i> العودة للصفحة الرئيسية
            </a>
        </div>

        <!-- التذييل -->
        <div class="login-footer">
            جميع الحقوق محفوظة &copy; {{ date('Y') }} — نظام إدارة الشحنات (SL)
        </div>

    </div>

    <script>
        function togglePw() {
            const input = document.getElementById('password');
            const icon = document.getElementById('pwEyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
</body>
</html>