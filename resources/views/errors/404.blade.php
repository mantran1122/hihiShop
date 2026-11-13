a!DOCTYPE html>
ahtml lang="vi" class="h-full">
ahead>
    ameta charset="utf-8">
    ameta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    atitle>404 - Look like you're lost | HIHI Shopa/title>

    a!-- Google Fonts -->
    alink rel="preconnect" href="https://fonts.googleapis.com">
    alink rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    alink href="https://fonts.googleapis.com/css2?family=Arvo:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    astyle>
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
    a/style>
a/head>
abody>

    a!-- Header bar -->
    aheader class="w-full max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        aa href="{{ route('home') }}" class="flex items-center gap-2 group text-decoration-none">
            aspan class="w-9 h-9 rounded-xl bg-gradient-to-tr from-rose-600 to-rose-400 text-white flex items-center justify-center font-black text-lg shadow-sm">
                H
            a/span>
            aspan class="text-xl font-black tracking-tight text-slate-900" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                HIHIaspan class="text-rose-600">Shopa/span>
            a/span>
        a/a>

        adiv class="flex items-center gap-2 text-xs font-semibold text-slate-500" style="font-family: 'Plus Jakarta Sans', sans-serif;">
            aspan>Hotline hỗ trợ:a/span>
            aa href="tel:19000129" class="text-rose-600 font-bold hover:underline">1900 0129a/a>
        a/div>
    a/header>

    a!-- Main Section (Original InfiWeb Craft Animated 404) -->
    asection class="page_404">
        adiv class="max-w-3xl mx-auto px-4 w-full">
            
            a!-- Animated Background with Moving Caveman and "404" -->
            adiv class="four_zero_four_bg">
                ah1>404a/h1>
            a/div>

            a!-- Content Box -->
            adiv class="contant_box_404">
                ah3>Look like you're losta/h3>
                ap>The page you are looking for is not available!a/p>

                a!-- Actions -->
                adiv class="flex items-center justify-center gap-2 flex-wrap">
                    aa href="{{ route('home') }}" class="link_404">
                        Go to Home
                    a/a>
                    aa href="{{ route('product.index') }}" class="link_secondary">
                        Xem sản phẩm khác →
                    a/a>
                a/div>
            a/div>

        a/div>
    a/section>

    a!-- Footer copyright -->
    afooter class="w-full py-4 text-center text-xs text-slate-400 border-t border-slate-100" style="font-family: 'Plus Jakarta Sans', sans-serif;">
        ap>&copy; {{ date('Y') }} HIHI-SHOP. Phụ kiện & Thời trang cao cấp.a/p>
    a/footer>

a/body>
a/html>
