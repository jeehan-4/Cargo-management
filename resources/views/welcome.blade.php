<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة الشحنات</title>
    <!-- Bootstrap 5 RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <!-- FontAwesome للأيقونات -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- خط Tajawal الأنيق -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #202833;
            --secondary-blue: #415A66;
            --accent-gold: #C7A667;
            --bg-main: #F7F5EF;
            --surface-card: #EAE0C8;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--primary-dark);
            color: var(--primary-dark);
            margin: 0;
            padding-top: 55px; /* مساحة لشريط المعاينة العلوي */
            overflow-x: hidden;
        }

        /* شريط معاينة المقاسات على شكل أيقونات (خاص ببيئة التطوير) */
        .dev-preview-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 55px;
            background-color: var(--primary-dark);
            color: var(--accent-gold);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            border-bottom: 2px solid var(--accent-gold);
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .dev-preview-group {
            background-color: #161c23;
            border: 1px solid rgba(199, 166, 103, 0.3);
            border-radius: 12px;
            padding: 4px;
            display: flex;
            gap: 4px;
        }
        .dev-preview-bar button {
            background: transparent;
            border: none;
            color: #a0aec0;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            transition: all 0.2s;
        }
        .dev-preview-bar button.active, .dev-preview-bar button:hover {
            background-color: var(--accent-gold);
            color: var(--primary-dark);
        }

        /* حاوية المعاينة المتغيرة */
        .preview-wrapper {
            background-color: var(--bg-main);
            min-height: calc(100vh - 55px);
            width: 100%;
            margin: 0 auto;
            transition: all 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .preview-desktop { max-width: 100%; }
        .preview-tablet { max-width: 768px; border-left: 6px solid var(--secondary-blue); border-right: 6px solid var(--secondary-blue); box-shadow: 0 0 30px rgba(0,0,0,0.3); margin: 0 auto; border-radius: 12px; overflow: hidden; }
        .preview-mobile { max-width: 390px; border-left: 10px solid var(--primary-dark); border-right: 10px solid var(--primary-dark); border-top: 15px solid var(--primary-dark); border-bottom: 15px solid var(--primary-dark); border-radius: 24px; box-shadow: 0 0 40px rgba(0,0,0,0.4); margin: 20px auto; overflow: hidden; }

        /* الشريط العلوي */
        .navbar-custom {
            background-color: var(--bg-main);
            border-bottom: 1px solid rgba(199, 166, 103, 0.3);
            padding: 15px 0;
        }
        .brand-title {
            font-weight: 800;
            font-size: 18px;
            color: var(--primary-dark);
            text-decoration: none;
        }

        /* القسم الرئيسي والترحيبي */
        .hero-section {
            padding: 40px 20px;
            text-align: center;
        }
        .hero-title {
            font-weight: 800;
            font-size: 28px;
            color: var(--primary-dark);
            margin-bottom: 15px;
            line-height: 1.4;
        }
        @media (min-width: 768px) {
            .hero-title { font-size: 32px; }
        }
        .hero-desc {
            font-size: 15px;
            color: var(--secondary-blue);
            max-width: 550px;
            margin: 0 auto 30px auto;
            line-height: 1.6;
        }

        /* الأزرار الراقية */
        .btn-login-custom {
            background-color: var(--accent-gold);
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 15px;
            padding: 12px 30px;
            border-radius: 10px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(199, 166, 103, 0.25);
            text-decoration: none;
        }
        .btn-login-custom:hover {
            background-color: #b39359;
            color: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* بطاقات مزايا النظام الأنيقة */
        .feature-card {
            background-color: var(--surface-card);
            border: 1px solid rgba(199, 166, 103, 0.2);
            border-radius: 12px;
            padding: 20px 15px;
            height: 100%;
            transition: transform 0.2s;
            box-shadow: 0 2px 8px rgba(32, 40, 51, 0.03);
            text-align: center;
        }
        .feature-card:hover {
            transform: translateY(-3px);
        }
        .feature-icon {
            width: 45px;
            height: 45px;
            background-color: var(--primary-dark);
            color: var(--accent-gold);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px auto;
            font-size: 18px;
        }
        .feature-title {
            font-weight: 700;
            font-size: 16px;
            color: var(--primary-dark);
            margin-bottom: 8px;
        }
        .feature-text {
            font-size: 13px;
            color: var(--secondary-blue);
            margin-bottom: 0;
            line-height: 1.5;
        }

        /* التذييل */
        .footer-custom {
            background-color: #eee9dc;
            padding: 15px 0;
            font-size: 12px;
            color: var(--secondary-blue);
            text-align: center;
            border-top: 1px solid rgba(199, 166, 103, 0.2);
        }
    </style>
</head>
<body>

    <!-- شريط المعاينة بأيقونات عصرية (خاص ببيئة التطوير) -->
    <div class="dev-preview-bar">
        <span class="small fw-bold" style="color: var(--accent-gold);"><i class="fa-solid fa-laptop-code ms-1"></i> معاينة التطوير</span>
        <div class="dev-preview-group">
            <button onclick="setPreviewSize('mobile')" id="btn-mobile" title="عرض الجوال"><i class="fa-solid fa-mobile-screen"></i></button>
            <button onclick="setPreviewSize('tablet')" id="btn-tablet" title="عرض التابلت"><i class="fa-solid fa-tablet-screen-button"></i></button>
            <button onclick="setPreviewSize('desktop')" id="btn-desktop" class="active" title="عرض الكمبيوتر"><i class="fa-solid fa-desktop"></i></button>
        </div>
    </div>

    <!-- حاوية المعاينة الرئيسية -->
    <div id="previewWrapper" class="preview-wrapper preview-desktop">
        
        <!-- الشريط العلوي -->
        <nav class="navbar navbar-custom px-4">
            <div class="container-fluid max-w-lg">
                <a class="brand-title d-flex align-items-center gap-2" href="{{ url('/') }}">
                    <i class="fa-solid fa-truck-fast" style="color: var(--accent-gold);"></i>
                    <span>نظام إدارة الشحنات</span>
                </a>
                <div>
                    @auth
                        <a href="{{ route('home') }}" class="btn btn-sm" style="background-color: var(--accent-gold); color: var(--primary-dark); font-weight: 700; font-size: 13px; padding: 6px 16px; border-radius: 8px;">
                            <i class="fa-solid fa-gauge-high ms-1"></i> لوحة التحكم
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm" style="background-color: var(--primary-dark); color: #fff; font-weight: 600; font-size: 13px; padding: 6px 16px; border-radius: 8px;">
                            <i class="fa-solid fa-right-to-bracket ms-1"></i> تسجيل الدخول
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- القسم الترحيبي الرئيسي -->
        <div class="container hero-section my-auto">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <!-- شارة خفيفة راقية (بدون كلمة ذكية) -->
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background-color: var(--surface-card); border: 1px solid rgba(199,166,103,0.4);">
                        <i class="fa-solid fa-shield-halved" style="color: var(--accent-gold); font-size: 12px;"></i>
                        <span style="font-size: 12px; font-weight: 700; color: var(--primary-dark);">منظومة لوجستية وموثوقة</span>
                    </div>

                    <h1 class="hero-title">
                        إدارة شحناتك بكل سهولة <br>
                        <span style="color: var(--accent-gold);">وتنظيم دقيق للسائقين</span>
                    </h1>

                    <p class="hero-desc">
                        نظام متكامل لتنظيم الشحنات، وتوزيع المهام على السائقين، ومتابعة عمليات التسليم من مكان واحد بكفاءة عالية واحترافية.
                    </p>

                    <!-- زر تسجيل الدخول الرئيسي -->
                    <div class="mb-5">
                        @auth
                            <a href="{{ route('home') }}" class="btn-login-custom">
                                <span>الانتقال إلى لوحة التحكم</span>
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-login-custom">
                                <span>تسجيل الدخول إلى النظام</span>
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                        @endauth
                    </div>

                </div>
            </div>

            <!-- بطاقات مزايا النظام الثلاثة -->
            <div class="row g-3 text-start justify-content-center mt-2">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <h3 class="feature-title">تنظيم الشحنات</h3>
                        <p class="feature-text">أرشفة ومتابعة دقيقة لكل الشحنات الواردة ومنع التداخل بكل مرونة.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon" style="color: var(--accent-gold);">
                            <i class="fa-solid fa-id-card-clip"></i>
                        </div>
                        <h3 class="feature-title">إدارة السائقين</h3>
                        <p class="feature-text">توزيع ذكي للمهام ومنع تكليف السائق برحلات متعارضة في نفس الوقت.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">تقارير العمليات</h3>
                        <p class="feature-text">متابعة حالات التوصيل وسجل العمليات بلحظات معدودة وبشكل فوري.</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- تذييل الصفحة -->
        <footer class="footer-custom">
            جميع الحقوق محفوظة &copy; {{ date('Y') }} - نظام إدارة الشحنات
        </footer>

    </div>

    <!-- سكربت أداة المعاينة الفورية للأيقونات -->
    <script>
        function setPreviewSize(mode) {
            const wrapper = document.getElementById('previewWrapper');
            document.querySelectorAll('.dev-preview-bar button').forEach(btn => btn.classList.remove('active'));
            
            wrapper.className = 'preview-wrapper';
            if(mode === 'desktop') {
                wrapper.classList.add('preview-desktop');
                document.getElementById('btn-desktop').classList.add('active');
            } else if(mode === 'tablet') {
                wrapper.classList.add('preview-tablet');
                document.getElementById('btn-tablet').classList.add('active');
            } else if(mode === 'mobile') {
                wrapper.classList.add('preview-mobile');
                document.getElementById('btn-mobile').classList.add('active');
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>