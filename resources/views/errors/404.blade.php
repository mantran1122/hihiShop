<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>404 - Look like you're lost | HIHI Shop</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Arvo:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* =========================================================
           Original 404 Caveman Animated Page (InfiWeb Craft Style)
           ========================================================= */
        body {
            font-family: 'Arvo', serif;
            background-color: #ffffff;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            margin: 0;
            padding: 0;
        }

        .page_404 {
            padding: 20px 0 40px;
            background: #fff;
            text-align: center;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .four_zero_four_bg {
            background-image: url('{{ asset('images/404-caveman.gif') }}');
            height: 400px;
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        .four_zero_four_bg h1 {
            font-size: clamp(60px, 10vw, 84px);
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            padding-top: 10px;
            letter-spacing: -2px;
            line-height: 1;
        }

        .contant_box_404 {
            margin-top: -50px;
        }

        .contant_box_404 h3 {
            font-size: clamp(24px, 4vw, 36px);
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 10px 0;
        }

        .contant_box_404 p {
            font-size: 15px;
            color: #64748b;
            margin: 0 0 24px 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 500;
        }

        .link_404 {
            color: #ffffff !important;
            padding: 12px 28px;
            background: #39ac31;
            margin: 0 6px;
            display: inline-block;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 15px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(57, 172, 49, 0.35);
        }

        .link_404:hover {
            background: #2f8e28;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(57, 172, 49, 0.45);
        }

        .link_404:active {
            transform: translateY(0);
        }

        .link_secondary {
            color: #475569 !important;
            padding: 12px 24px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            margin: 0 6px;
            display: inline-block;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.2s ease;
        }

        .link_secondary:hover {
            background: #e2e8f0;
            color: #0f172a !important;
        }

        @media (max-width: 640px) {
            .four_zero_four_bg {
                height: 280px;
            }
            .contant_box_404 {
                margin-top: -30px;
            }
        }
    </style>
</head>
<body>

    <!-- Header bar -->
    <header class="w-full max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2 group text-decoration-none">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-rose-600 to-rose-400 text-white flex items-center justify-center font-black text-lg shadow-sm">
                H
            </span>
            <span class="text-xl font-black tracking-tight text-slate-900" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                HIHI<span class="text-rose-600">Shop</span>
            </span>
        </a>

        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500" style="font-family: 'Plus Jakarta Sans', sans-serif;">
            <span>Hotline hỗ trợ:</span>
            <a href="tel:19000129" class="text-rose-600 font-bold hover:underline">1900 0129</a>
        </div>
    </header>

    <!-- Main Section (Original InfiWeb Craft Animated 404) -->
    <section class="page_404">
        <div class="max-w-3xl mx-auto px-4 w-full">
            
            <!-- Animated Background with Moving Caveman and "404" -->
            <div class="four_zero_four_bg">
                <h1>404</h1>
            </div>

            <!-- Content Box -->
            <div class="contant_box_404">
                <h3>Look like you're lost</h3>
                <p>The page you are looking for is not available!</p>

                <!-- Actions -->
                <div class="flex items-center justify-center gap-2 flex-wrap">
                    <a href="{{ route('home') }}" class="link_404">
                        Go to Home
                    </a>
                    <a href="{{ route('product.index') }}" class="link_secondary">
                        Xem sản phẩm khác →
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer copyright -->
    <footer class="w-full py-4 text-center text-xs text-slate-400 border-t border-slate-100" style="font-family: 'Plus Jakarta Sans', sans-serif;">
        <p>&copy; {{ date('Y') }} HIHI-SHOP. Phụ kiện & Thời trang cao cấp.</p>
    </footer>

</body>
</html>
