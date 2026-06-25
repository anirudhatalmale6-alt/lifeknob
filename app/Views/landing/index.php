<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeKnob - Peace of Mind for Families, Independence for Elders</title>
    <meta name="description" content="LifeKnob keeps elderly loved ones safe with a simple daily check-in. One tap says 'I'm OK'. Missed check-in? Family gets alerted instantly.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ============================================
           CSS RESET & VARIABLES
           ============================================ */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --green-50: #f0fdf4;
            --green-100: #dcfce7;
            --green-200: #bbf7d0;
            --green-300: #86efac;
            --green-400: #4ade80;
            --green-500: #22c55e;
            --green-600: #16a34a;
            --green-700: #15803d;
            --green-800: #166534;
            --green-900: #14532d;

            --coral-50: #fff5f5;
            --coral-100: #ffe3e3;
            --coral-400: #f87171;
            --coral-500: #ef4444;
            --coral-600: #dc2626;

            --warm-50: #fffbeb;
            --warm-100: #fef3c7;

            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;

            --white: #ffffff;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.04);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.08), 0 8px 10px -6px rgba(0,0,0,0.04);

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --radius-full: 9999px;
        }

        html {
            scroll-behavior: smooth;
            font-size: 16px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--slate-800);
            line-height: 1.7;
            background: var(--white);
            overflow-x: hidden;
        }

        img {
            max-width: 100%;
            display: block;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* ============================================
           ANIMATIONS
           ============================================ */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        @keyframes pulse-soft {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.9; }
        }

        @keyframes shimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }

        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }
        .reveal-delay-5 { transition-delay: 0.5s; }

        /* ============================================
           LAYOUT
           ============================================ */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        section {
            padding: 100px 0;
        }

        /* ============================================
           NAVIGATION
           ============================================ */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            transition: box-shadow 0.3s ease;
        }

        .navbar.scrolled {
            box-shadow: var(--shadow-md);
        }

        .navbar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--green-700);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--green-500), var(--green-700));
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.1rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 32px;
            list-style: none;
        }

        .nav-links a {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--slate-600);
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--green-700);
        }

        .nav-cta {
            background: var(--green-600);
            color: var(--white) !important;
            padding: 10px 24px;
            border-radius: var(--radius-full);
            font-weight: 600;
            transition: background 0.2s, transform 0.2s;
        }

        .nav-cta:hover {
            background: var(--green-700);
            transform: translateY(-1px);
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--slate-700);
            cursor: pointer;
        }

        /* ============================================
           HERO SECTION
           ============================================ */
        .hero {
            padding: 140px 0 100px;
            background: linear-gradient(168deg, var(--white) 0%, var(--green-50) 50%, var(--warm-50) 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(34,197,94,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(254,243,199,0.5) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--green-100);
            color: var(--green-800);
            padding: 8px 16px;
            border-radius: var(--radius-full);
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 24px;
            animation: fadeIn 0.6s ease;
        }

        .hero-badge i {
            font-size: 0.75rem;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.15;
            color: var(--slate-900);
            margin-bottom: 24px;
            animation: fadeInUp 0.8s ease;
        }

        .hero h1 span {
            color: var(--green-600);
        }

        .hero-text {
            font-size: 1.2rem;
            color: var(--slate-500);
            line-height: 1.8;
            margin-bottom: 36px;
            max-width: 520px;
            animation: fadeInUp 0.8s ease 0.15s both;
        }

        .hero-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease 0.3s both;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--green-500), var(--green-600));
            color: var(--white);
            padding: 16px 36px;
            border-radius: var(--radius-full);
            font-size: 1.05rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(22,163,74,0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(22,163,74,0.35);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--white);
            color: var(--slate-700);
            padding: 16px 32px;
            border-radius: var(--radius-full);
            font-size: 1.05rem;
            font-weight: 600;
            border: 2px solid var(--slate-200);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            border-color: var(--green-300);
            color: var(--green-700);
            transform: translateY(-2px);
        }

        .hero-stats {
            display: flex;
            gap: 40px;
            margin-top: 48px;
            animation: fadeInUp 0.8s ease 0.45s both;
        }

        .hero-stat {
            text-align: left;
        }

        .hero-stat-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--green-700);
        }

        .hero-stat-label {
            font-size: 0.85rem;
            color: var(--slate-400);
            font-weight: 500;
        }

        /* Phone Mockup */
        .hero-visual {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            animation: fadeIn 1s ease 0.3s both;
        }

        .phone-mockup {
            width: 300px;
            height: 600px;
            background: var(--white);
            border-radius: 40px;
            box-shadow: var(--shadow-xl), 0 0 0 8px var(--slate-800), 0 0 0 10px var(--slate-600);
            overflow: hidden;
            position: relative;
            animation: float 6s ease-in-out infinite;
        }

        .phone-notch {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 140px;
            height: 28px;
            background: var(--slate-800);
            border-radius: 0 0 20px 20px;
            z-index: 5;
        }

        .phone-screen {
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, var(--green-50) 0%, var(--white) 40%);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 50px 24px 30px;
        }

        .phone-greeting {
            font-size: 0.85rem;
            color: var(--slate-400);
            margin-bottom: 4px;
            margin-top: 16px;
        }

        .phone-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--slate-800);
            margin-bottom: 8px;
        }

        .phone-date {
            font-size: 0.75rem;
            color: var(--slate-400);
            margin-bottom: 30px;
        }

        .phone-status {
            width: 100%;
            background: var(--green-50);
            border: 2px solid var(--green-200);
            border-radius: var(--radius-md);
            padding: 12px;
            text-align: center;
            margin-bottom: 28px;
        }

        .phone-status-text {
            font-size: 0.8rem;
            color: var(--green-700);
            font-weight: 600;
        }

        .phone-btn-ok {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green-400), var(--green-600));
            border: none;
            color: var(--white);
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            box-shadow: 0 8px 30px rgba(22,163,74,0.35);
            margin-bottom: 24px;
            animation: pulse-soft 3s ease-in-out infinite;
            cursor: pointer;
        }

        .phone-btn-ok i {
            font-size: 2rem;
            margin-bottom: 2px;
        }

        .phone-bottom-btns {
            display: flex;
            gap: 12px;
            width: 100%;
        }

        .phone-btn-help {
            flex: 1;
            padding: 14px 8px;
            border-radius: var(--radius-md);
            background: var(--warm-100);
            border: 2px solid #fcd34d;
            color: #92400e;
            font-size: 0.75rem;
            font-weight: 700;
            text-align: center;
            cursor: pointer;
        }

        .phone-btn-help i {
            display: block;
            font-size: 1.2rem;
            margin-bottom: 4px;
        }

        .phone-btn-emergency {
            flex: 1;
            padding: 14px 8px;
            border-radius: var(--radius-md);
            background: var(--coral-50);
            border: 2px solid var(--coral-400);
            color: var(--coral-600);
            font-size: 0.75rem;
            font-weight: 700;
            text-align: center;
            cursor: pointer;
        }

        .phone-btn-emergency i {
            display: block;
            font-size: 1.2rem;
            margin-bottom: 4px;
        }

        /* Floating badges around phone */
        .floating-badge {
            position: absolute;
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 14px 18px;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
            animation: float 5s ease-in-out infinite;
        }

        .floating-badge-1 {
            top: 60px;
            right: -30px;
            animation-delay: 0s;
        }

        .floating-badge-1 .fb-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--green-100);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--green-600);
        }

        .floating-badge-2 {
            bottom: 120px;
            left: -20px;
            animation-delay: 2s;
        }

        .floating-badge-2 .fb-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--coral-100);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--coral-500);
        }

        .floating-badge-3 {
            bottom: 40px;
            right: -10px;
            animation-delay: 4s;
        }

        .floating-badge-3 .fb-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--warm-100);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #d97706;
        }

        /* ============================================
           SECTION HEADERS
           ============================================ */
        .section-header {
            text-align: center;
            max-width: 640px;
            margin: 0 auto 64px;
        }

        .section-label {
            display: inline-block;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--green-600);
            margin-bottom: 12px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--slate-900);
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: var(--slate-500);
            line-height: 1.7;
        }

        /* ============================================
           HOW IT WORKS
           ============================================ */
        .how-it-works {
            background: var(--white);
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            position: relative;
        }

        .steps-grid::before {
            content: '';
            position: absolute;
            top: 48px;
            left: 15%;
            right: 15%;
            height: 2px;
            background: linear-gradient(90deg, var(--green-200), var(--green-400), var(--green-200));
            z-index: 0;
        }

        .step-card {
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .step-number {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green-50), var(--green-100));
            border: 3px solid var(--green-300);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            position: relative;
        }

        .step-number i {
            font-size: 2rem;
            color: var(--green-600);
        }

        .step-number-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 32px;
            height: 32px;
            background: var(--green-600);
            color: var(--white);
            border-radius: 50%;
            font-size: 0.85rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid var(--white);
        }

        .step-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--slate-800);
            margin-bottom: 12px;
        }

        .step-card p {
            font-size: 1rem;
            color: var(--slate-500);
            max-width: 300px;
            margin: 0 auto;
        }

        /* ============================================
           FEATURES
           ============================================ */
        .features {
            background: linear-gradient(180deg, var(--slate-50) 0%, var(--white) 100%);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .feature-card {
            background: var(--white);
            border: 1px solid var(--slate-100);
            border-radius: var(--radius-lg);
            padding: 36px 28px;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--green-200);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 20px;
        }

        .feature-icon-green {
            background: var(--green-100);
            color: var(--green-600);
        }

        .feature-icon-coral {
            background: var(--coral-100);
            color: var(--coral-500);
        }

        .feature-icon-amber {
            background: var(--warm-100);
            color: #d97706;
        }

        .feature-icon-blue {
            background: #dbeafe;
            color: #2563eb;
        }

        .feature-card h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--slate-800);
            margin-bottom: 10px;
        }

        .feature-card p {
            font-size: 0.95rem;
            color: var(--slate-500);
            line-height: 1.65;
        }

        /* ============================================
           FOR ELDERS VS FOR FAMILY
           ============================================ */
        .comparison {
            background: var(--white);
        }

        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .comparison-card {
            border-radius: var(--radius-xl);
            padding: 48px 40px;
            position: relative;
            overflow: hidden;
        }

        .comparison-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .comparison-elders {
            background: linear-gradient(180deg, var(--green-50) 0%, var(--white) 100%);
            border: 1px solid var(--green-200);
        }

        .comparison-elders::before {
            background: linear-gradient(90deg, var(--green-400), var(--green-600));
        }

        .comparison-family {
            background: linear-gradient(180deg, #eff6ff 0%, var(--white) 100%);
            border: 1px solid #bfdbfe;
        }

        .comparison-family::before {
            background: linear-gradient(90deg, #60a5fa, #2563eb);
        }

        .comparison-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 24px;
        }

        .comparison-elders .comparison-icon {
            background: var(--green-100);
            color: var(--green-600);
        }

        .comparison-family .comparison-icon {
            background: #dbeafe;
            color: #2563eb;
        }

        .comparison-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--slate-800);
            margin-bottom: 8px;
        }

        .comparison-card .card-sub {
            font-size: 1rem;
            color: var(--slate-400);
            margin-bottom: 28px;
        }

        .comparison-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .comparison-list li {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            font-size: 1rem;
            color: var(--slate-600);
            line-height: 1.5;
        }

        .comparison-list li i {
            margin-top: 4px;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .comparison-elders .comparison-list li i {
            color: var(--green-500);
        }

        .comparison-family .comparison-list li i {
            color: #3b82f6;
        }

        /* ============================================
           TESTIMONIALS
           ============================================ */
        .testimonials {
            background: linear-gradient(180deg, var(--green-50) 0%, var(--warm-50) 100%);
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .testimonial-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 36px 28px;
            box-shadow: var(--shadow-md);
            position: relative;
        }

        .testimonial-stars {
            color: #f59e0b;
            font-size: 0.9rem;
            margin-bottom: 16px;
            display: flex;
            gap: 3px;
        }

        .testimonial-text {
            font-size: 1.05rem;
            color: var(--slate-600);
            line-height: 1.75;
            margin-bottom: 24px;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .testimonial-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--white);
        }

        .avatar-green { background: linear-gradient(135deg, var(--green-400), var(--green-600)); }
        .avatar-blue { background: linear-gradient(135deg, #60a5fa, #2563eb); }
        .avatar-amber { background: linear-gradient(135deg, #fbbf24, #d97706); }

        .testimonial-name {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--slate-800);
        }

        .testimonial-role {
            font-size: 0.85rem;
            color: var(--slate-400);
        }

        .testimonial-quote-mark {
            position: absolute;
            top: 20px;
            right: 24px;
            font-size: 3rem;
            color: var(--green-100);
            font-family: Georgia, serif;
            line-height: 1;
        }

        /* ============================================
           PRICING
           ============================================ */
        .pricing {
            background: var(--white);
        }

        .pricing-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 32px;
            max-width: 800px;
            margin: 0 auto;
        }

        .pricing-card {
            border-radius: var(--radius-xl);
            padding: 44px 36px;
            text-align: center;
            position: relative;
            transition: transform 0.3s ease;
        }

        .pricing-card:hover {
            transform: translateY(-4px);
        }

        .pricing-free {
            background: var(--white);
            border: 2px solid var(--slate-200);
        }

        .pricing-premium {
            background: linear-gradient(135deg, var(--green-600), var(--green-800));
            color: var(--white);
            box-shadow: var(--shadow-xl);
        }

        .pricing-popular {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: var(--white);
            padding: 6px 20px;
            border-radius: var(--radius-full);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .pricing-card h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .pricing-card .pricing-desc {
            font-size: 0.95rem;
            margin-bottom: 24px;
            opacity: 0.8;
        }

        .pricing-price {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 4px;
        }

        .pricing-period {
            font-size: 0.9rem;
            opacity: 0.7;
            margin-bottom: 32px;
        }

        .pricing-features {
            list-style: none;
            text-align: left;
            margin-bottom: 32px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .pricing-features li {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }

        .pricing-features li i {
            font-size: 0.8rem;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .pricing-free .pricing-features li i {
            background: var(--green-100);
            color: var(--green-600);
        }

        .pricing-premium .pricing-features li i {
            background: rgba(255,255,255,0.2);
            color: var(--white);
        }

        .pricing-btn {
            display: block;
            width: 100%;
            padding: 16px;
            border-radius: var(--radius-full);
            font-size: 1rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pricing-free .pricing-btn {
            background: var(--green-600);
            color: var(--white);
        }

        .pricing-free .pricing-btn:hover {
            background: var(--green-700);
        }

        .pricing-premium .pricing-btn {
            background: var(--white);
            color: var(--green-700);
        }

        .pricing-premium .pricing-btn:hover {
            background: var(--green-50);
        }

        /* ============================================
           CTA SECTION
           ============================================ */
        .cta-section {
            background: linear-gradient(135deg, var(--green-700) 0%, var(--green-900) 100%);
            padding: 80px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .cta-section::after {
            content: '';
            position: absolute;
            bottom: -40%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, transparent 70%);
            border-radius: 50%;
        }

        .cta-section .container {
            position: relative;
            z-index: 1;
        }

        .cta-section h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 16px;
        }

        .cta-section p {
            font-size: 1.2rem;
            color: var(--green-200);
            margin-bottom: 36px;
            max-width: 540px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-section .btn-primary {
            background: var(--white);
            color: var(--green-700);
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
        }

        .cta-section .btn-primary:hover {
            background: var(--green-50);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        /* ============================================
           FOOTER
           ============================================ */
        .footer {
            background: var(--slate-900);
            color: var(--slate-300);
            padding: 60px 0 0;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            padding-bottom: 48px;
            border-bottom: 1px solid var(--slate-700);
        }

        .footer-brand .logo {
            color: var(--white);
            margin-bottom: 16px;
        }

        .footer-brand p {
            font-size: 0.95rem;
            line-height: 1.7;
            color: var(--slate-400);
            max-width: 280px;
        }

        .footer-social {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .footer-social a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--slate-800);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--slate-400);
            font-size: 1rem;
            transition: all 0.2s;
        }

        .footer-social a:hover {
            background: var(--green-600);
            color: var(--white);
        }

        .footer-col h4 {
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--white);
            margin-bottom: 20px;
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .footer-col a {
            font-size: 0.9rem;
            color: var(--slate-400);
            transition: color 0.2s;
        }

        .footer-col a:hover {
            color: var(--green-400);
        }

        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 0;
            font-size: 0.85rem;
            color: var(--slate-500);
        }

        .footer-bottom a {
            color: var(--slate-400);
            margin-left: 24px;
            transition: color 0.2s;
        }

        .footer-bottom a:hover {
            color: var(--green-400);
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 1024px) {
            .hero .container {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 48px;
            }

            .hero-text {
                margin: 0 auto 36px;
            }

            .hero-actions {
                justify-content: center;
            }

            .hero-stats {
                justify-content: center;
            }

            .hero h1 {
                font-size: 2.8rem;
            }

            .steps-grid::before {
                display: none;
            }

            .floating-badge {
                display: none;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            section {
                padding: 72px 0;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 72px;
                left: 0;
                right: 0;
                background: var(--white);
                flex-direction: column;
                padding: 24px;
                gap: 16px;
                border-bottom: 1px solid var(--slate-200);
                box-shadow: var(--shadow-lg);
            }

            .nav-links.active {
                display: flex;
            }

            .mobile-toggle {
                display: block;
            }

            .hero {
                padding: 120px 0 72px;
            }

            .hero h1 {
                font-size: 2.2rem;
            }

            .hero-text {
                font-size: 1.05rem;
            }

            .phone-mockup {
                width: 240px;
                height: 480px;
            }

            .phone-btn-ok {
                width: 100px;
                height: 100px;
                font-size: 0.9rem;
            }

            .phone-btn-ok i {
                font-size: 1.6rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .steps-grid {
                grid-template-columns: 1fr;
                gap: 48px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .comparison-grid {
                grid-template-columns: 1fr;
            }

            .testimonials-grid {
                grid-template-columns: 1fr;
            }

            .pricing-cards {
                grid-template-columns: 1fr;
                max-width: 420px;
            }

            .cta-section h2 {
                font-size: 2rem;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }

            .hero-stats {
                gap: 24px;
            }

            .comparison-card {
                padding: 32px 24px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 16px;
            }

            .hero h1 {
                font-size: 1.8rem;
            }

            .btn-primary, .btn-secondary {
                padding: 14px 28px;
                font-size: 0.95rem;
            }

            .hero-actions {
                flex-direction: column;
                align-items: center;
            }

            .phone-mockup {
                width: 220px;
                height: 440px;
            }

            .phone-name {
                font-size: 1.1rem;
            }

            .phone-btn-ok {
                width: 90px;
                height: 90px;
                font-size: 0.8rem;
            }

            .phone-btn-ok i {
                font-size: 1.3rem;
            }

            .phone-bottom-btns {
                gap: 8px;
            }

            .phone-btn-help, .phone-btn-emergency {
                padding: 10px 6px;
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>

    <!-- ===================== NAVBAR ===================== -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="#" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-heart-pulse"></i>
                </div>
                LifeKnob
            </a>
            <ul class="nav-links" id="navLinks">
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#comparison">Who It's For</a></li>
                <li><a href="#pricing">Pricing</a></li>
                <li><a href="#" class="nav-cta">Get Started</a></li>
            </ul>
            <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- ===================== HERO ===================== -->
    <section class="hero" id="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-shield-heart"></i>
                    Trusted by 10,000+ families
                </div>
                <h1>Peace of Mind,<br><span>One Tap Away</span></h1>
                <p class="hero-text">
                    Your loved one taps "I'm OK" each day. If they miss a check-in,
                    you get alerted instantly. Simple, reliable safety for elders
                    living independently.
                </p>
                <div class="hero-actions">
                    <a href="#" class="btn-primary">
                        <i class="fas fa-arrow-right"></i>
                        Get Started Free
                    </a>
                    <a href="#how-it-works" class="btn-secondary">
                        <i class="fas fa-play-circle"></i>
                        See How It Works
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-number">99.9%</div>
                        <div class="hero-stat-label">Uptime reliability</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">&lt; 30s</div>
                        <div class="hero-stat-label">Alert delivery time</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">24/7</div>
                        <div class="hero-stat-label">Monitoring</div>
                    </div>
                </div>
            </div>

            <div class="hero-visual">
                <!-- Floating badges -->
                <div class="floating-badge floating-badge-1">
                    <div class="fb-icon"><i class="fas fa-check"></i></div>
                    <span>Mum checked in</span>
                </div>
                <div class="floating-badge floating-badge-2">
                    <div class="fb-icon"><i class="fas fa-bell"></i></div>
                    <span>Alert sent to family</span>
                </div>
                <div class="floating-badge floating-badge-3">
                    <div class="fb-icon"><i class="fas fa-users"></i></div>
                    <span>3 family members linked</span>
                </div>

                <!-- Phone Mockup -->
                <div class="phone-mockup">
                    <div class="phone-notch"></div>
                    <div class="phone-screen">
                        <div class="phone-greeting">Good morning</div>
                        <div class="phone-name">Margaret</div>
                        <div class="phone-date">Wednesday, 25 June 2026</div>
                        <div class="phone-status">
                            <div class="phone-status-text"><i class="fas fa-clock"></i> &nbsp;Daily check-in due</div>
                        </div>
                        <div class="phone-btn-ok">
                            <i class="fas fa-check"></i>
                            I'm OK
                        </div>
                        <div class="phone-bottom-btns">
                            <div class="phone-btn-help">
                                <i class="fas fa-hand-holding-heart"></i>
                                I Need Help
                            </div>
                            <div class="phone-btn-emergency">
                                <i class="fas fa-phone-volume"></i>
                                Call Ambulance
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== HOW IT WORKS ===================== -->
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-label">How It Works</div>
                <h2 class="section-title">Up and Running in Minutes</h2>
                <p class="section-subtitle">
                    No complicated setup. No technical knowledge needed.
                    If they can press a button, they can use LifeKnob.
                </p>
            </div>

            <div class="steps-grid">
                <div class="step-card reveal reveal-delay-1">
                    <div class="step-number">
                        <i class="fas fa-mobile-screen-button"></i>
                        <span class="step-number-badge">1</span>
                    </div>
                    <h3>Download & Register</h3>
                    <p>Install the app or use the web version. Create an account in under a minute - for the elder or a family member.</p>
                </div>

                <div class="step-card reveal reveal-delay-2">
                    <div class="step-number">
                        <i class="fas fa-link"></i>
                        <span class="step-number-badge">2</span>
                    </div>
                    <h3>Link Your Family</h3>
                    <p>Share a simple invite code. Family members join the circle and choose how they want to be notified.</p>
                </div>

                <div class="step-card reveal reveal-delay-3">
                    <div class="step-number">
                        <i class="fas fa-circle-check"></i>
                        <span class="step-number-badge">3</span>
                    </div>
                    <h3>Check In Daily</h3>
                    <p>Each day, your loved one taps "I'm OK". Miss a check-in? The family gets an alert. It's that simple.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== FEATURES ===================== -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-label">Features</div>
                <h2 class="section-title">Everything You Need, Nothing You Don't</h2>
                <p class="section-subtitle">
                    Designed for simplicity. Built for reliability. Every feature serves
                    one purpose: keeping your family safe and connected.
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card reveal reveal-delay-1">
                    <div class="feature-icon feature-icon-green">
                        <i class="fas fa-circle-check"></i>
                    </div>
                    <h3>Daily OK Check-in</h3>
                    <p>One large, friendly button. Tap it once a day to let your family know you're well. No typing, no fuss.</p>
                </div>

                <div class="feature-card reveal reveal-delay-2">
                    <div class="feature-icon feature-icon-coral">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Automatic Alerts</h3>
                    <p>Silence is the alarm. If a check-in is missed, family members receive instant notifications via SMS, email, or push.</p>
                </div>

                <div class="feature-card reveal reveal-delay-3">
                    <div class="feature-icon feature-icon-amber">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3>Help Button</h3>
                    <p>Need non-emergency help? Press "I Need Help" and your entire family circle gets notified immediately.</p>
                </div>

                <div class="feature-card reveal reveal-delay-1">
                    <div class="feature-icon feature-icon-coral">
                        <i class="fas fa-phone-volume"></i>
                    </div>
                    <h3>Emergency Call</h3>
                    <p>One tap dials emergency services while simultaneously alerting your family with your location.</p>
                </div>

                <div class="feature-card reveal reveal-delay-2">
                    <div class="feature-icon feature-icon-blue">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Family Dashboard</h3>
                    <p>See check-in history, patterns, and wellness trends. Know when your loved one checked in - and when they didn't.</p>
                </div>

                <div class="feature-card reveal reveal-delay-3">
                    <div class="feature-icon feature-icon-green">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>Works Everywhere</h3>
                    <p>Available as a mobile app and a web app. Works on any smartphone, tablet, or computer with a browser.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== FOR ELDERS vs FOR FAMILY ===================== -->
    <section class="comparison" id="comparison">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-label">Who It's For</div>
                <h2 class="section-title">Designed for Both Sides of the Family</h2>
                <p class="section-subtitle">
                    Whether you're the one checking in or the one keeping watch,
                    LifeKnob gives you exactly what you need.
                </p>
            </div>

            <div class="comparison-grid">
                <div class="comparison-card comparison-elders reveal reveal-delay-1">
                    <div class="comparison-icon">
                        <i class="fas fa-person-cane"></i>
                    </div>
                    <h3>For Elders</h3>
                    <p class="card-sub">Simple. Independent. Dignified.</p>
                    <ul class="comparison-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>One big "I'm OK" button - impossible to miss</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Gentle daily reminders at a time you choose</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Large text and high-contrast design</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>No complicated menus or settings to navigate</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Help and emergency buttons always visible</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Maintains your independence while staying safe</span>
                        </li>
                    </ul>
                </div>

                <div class="comparison-card comparison-family reveal reveal-delay-2">
                    <div class="comparison-icon">
                        <i class="fas fa-people-roof"></i>
                    </div>
                    <h3>For Family Members</h3>
                    <p class="card-sub">Informed. Connected. Reassured.</p>
                    <ul class="comparison-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Real-time check-in notifications on your phone</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Missed check-in alerts via SMS, email, or push</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Dashboard with check-in history and patterns</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Multiple family members in one circle</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Instant help and emergency alerts with location</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Configurable alert escalation and schedules</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== TESTIMONIALS ===================== -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-label">Testimonials</div>
                <h2 class="section-title">Families Love LifeKnob</h2>
                <p class="section-subtitle">
                    Real stories from families who sleep better at night
                    knowing their loved ones are safe.
                </p>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card reveal reveal-delay-1">
                    <div class="testimonial-quote-mark">"</div>
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "My mum lives alone 200 miles away. Before LifeKnob, I worried constantly.
                        Now she taps her button each morning, and I get a little notification that
                        makes my whole day better. The one time she forgot, I called and she'd
                        just been busy gardening. But knowing the system works gave me such relief."
                    </p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar avatar-green">SH</div>
                        <div>
                            <div class="testimonial-name">Sarah Henderson</div>
                            <div class="testimonial-role">Daughter, Leeds</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card reveal reveal-delay-2">
                    <div class="testimonial-quote-mark">"</div>
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "I'm 78 and I value my independence. My children wanted me to move
                        closer but I love my home. LifeKnob was our compromise. I press one
                        button each morning and everyone's happy. It's so easy I sometimes
                        forget it's even there - which is the whole point, isn't it?"
                    </p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar avatar-blue">RW</div>
                        <div>
                            <div class="testimonial-name">Robert Williams</div>
                            <div class="testimonial-role">Elder user, Cornwall</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card reveal reveal-delay-3">
                    <div class="testimonial-quote-mark">"</div>
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "Dad had a fall last winter and used the help button. All four of us
                        siblings got the alert simultaneously. My brother, who lives closest,
                        was there in 15 minutes. Dad was fine, but without LifeKnob he would
                        have been on the floor for hours. Worth every penny."
                    </p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar avatar-amber">JP</div>
                        <div>
                            <div class="testimonial-name">James Patel</div>
                            <div class="testimonial-role">Son, Manchester</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== PRICING ===================== -->
    <section class="pricing" id="pricing">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-label">Pricing</div>
                <h2 class="section-title">Safety Shouldn't Be Expensive</h2>
                <p class="section-subtitle">
                    Start free and upgrade when you're ready. No contracts,
                    no hidden fees, cancel any time.
                </p>
            </div>

            <div class="pricing-cards">
                <div class="pricing-card pricing-free reveal reveal-delay-1">
                    <h3>Free</h3>
                    <p class="pricing-desc">Everything you need to get started</p>
                    <div class="pricing-price">$0</div>
                    <p class="pricing-period">Free forever</p>
                    <ul class="pricing-features">
                        <li><i class="fas fa-check"></i> Daily OK check-in</li>
                        <li><i class="fas fa-check"></i> Help button</li>
                        <li><i class="fas fa-check"></i> Emergency call</li>
                        <li><i class="fas fa-check"></i> Up to 3 family members</li>
                        <li><i class="fas fa-check"></i> Push notifications</li>
                        <li><i class="fas fa-check"></i> 7-day check-in history</li>
                    </ul>
                    <button class="pricing-btn">Get Started Free</button>
                </div>

                <div class="pricing-card pricing-premium reveal reveal-delay-2">
                    <div class="pricing-popular">Most Popular</div>
                    <h3>Family Plus</h3>
                    <p class="pricing-desc">For families who want more peace of mind</p>
                    <div class="pricing-price">$4.99</div>
                    <p class="pricing-period">per month</p>
                    <ul class="pricing-features">
                        <li><i class="fas fa-check"></i> Everything in Free</li>
                        <li><i class="fas fa-check"></i> Unlimited family members</li>
                        <li><i class="fas fa-check"></i> SMS & email alerts</li>
                        <li><i class="fas fa-check"></i> Full check-in history</li>
                        <li><i class="fas fa-check"></i> Custom alert schedules</li>
                        <li><i class="fas fa-check"></i> Location sharing on emergency</li>
                        <li><i class="fas fa-check"></i> Priority support</li>
                    </ul>
                    <button class="pricing-btn">Start 14-Day Free Trial</button>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== CTA ===================== -->
    <section class="cta-section">
        <div class="container reveal">
            <h2>Start Protecting Your Family Today</h2>
            <p>
                Join thousands of families who worry less and live more.
                Set up takes under two minutes.
            </p>
            <a href="#" class="btn-primary">
                <i class="fas fa-arrow-right"></i>
                Create Your Free Account
            </a>
        </div>
    </section>

    <!-- ===================== FOOTER ===================== -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="#" class="logo">
                        <div class="logo-icon">
                            <i class="fas fa-heart-pulse"></i>
                        </div>
                        LifeKnob
                    </a>
                    <p>
                        Simple daily check-ins that keep elderly loved ones safe
                        and families connected. Because independence and safety
                        should go hand in hand.
                    </p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Product</h4>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#pricing">Pricing</a></li>
                        <li><a href="#">Download App</a></li>
                        <li><a href="#">Web App</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">Help Centre</a></li>
                        <li><a href="#">Setup Guide</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Press Kit</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>2026 LifeKnob. All rights reserved.</span>
                <div>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ===================== JAVASCRIPT ===================== -->
    <script>
        // ---- Navbar scroll effect ----
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // ---- Mobile menu toggle ----
        const mobileToggle = document.getElementById('mobileToggle');
        const navLinks = document.getElementById('navLinks');

        mobileToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            const icon = mobileToggle.querySelector('i');
            if (navLinks.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-xmark');
            } else {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        });

        // Close mobile menu when a link is clicked
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                const icon = mobileToggle.querySelector('i');
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            });
        });

        // ---- Scroll reveal animation ----
        const revealElements = document.querySelectorAll('.reveal');

        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -40px 0px'
        });

        revealElements.forEach(el => revealObserver.observe(el));

        // ---- Smooth scroll for anchor links ----
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const navHeight = navbar.offsetHeight;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navHeight - 20;
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>

</body>
</html>
