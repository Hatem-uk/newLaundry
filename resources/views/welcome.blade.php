<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>موج - حل متكامل لإدارة المغاسل</title>
        
        <!-- Meta Tags for SEO and Social Sharing -->
        <meta name="description" content="موج - الحل المتكامل لإدارة المغاسل وتشغيلها بكفاءة عالية. تطبيقين منفصلين لصاحب المغسلة والعملاء">
        <meta name="keywords" content="مغسلة, إدارة مغسلة, تطبيق مغسلة, خدمة عملاء, غسيل ملابس">
        <meta name="author" content="موج">
        <meta property="og:title" content="موج - حل متكامل لإدارة المغاسل">
        <meta property="og:description" content="الحل المتكامل لإدارة المغاسل وتشغيلها بكفاءة عالية">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ asset('dashboard/logo.png') }}">
        <meta name="twitter:card" content="summary_large_image">
        
        <!-- Preload Critical Resources -->
        <link rel="preload" href="{{ asset('heroSection.jpg') }}" as="image">
        <link rel="preload" href="{{ asset('about.png') }}" as="image">
        <link rel="preload" href="{{ asset('Frame 1244832970.png') }}" as="image">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        
        <!-- Custom CSS -->
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap');
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Cairo', sans-serif;
                line-height: 1.6;
                color: #1b1b18;
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            }
            
            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
            }
            
            /* Header */
            .header {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1000;
                padding: 1rem 0;
                box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }
            
            .nav {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 2rem;
            }
            
            .logo {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-shrink: 0;
            }
            
            .logo-icon {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                transition: all 0.3s ease;
                position: relative;
            }
            
            .logo-icon:hover {
                transform: scale(1.05);
            }
            
            .logo-icon img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .nav-links {
                display: flex;
                gap: 2rem;
                list-style: none;
                margin: 0;
                padding: 0;
                align-items: center;
            }
            
            .nav-links a {
                text-decoration: none;
                color: #1b1b18;
                font-weight: 500;
                padding: 0.5rem 1rem;
                border-radius: 25px;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }
            
            .nav-links a:hover {
                color: #1e40af;
                background: rgba(30, 64, 175, 0.1);
            }
            
            .language-selector {
                display: flex;
                align-items: center;
                gap: 5px;
                padding: 0.5rem 1rem;
                border: 1px solid #e5e7eb;
                border-radius: 25px;
                background: white;
                cursor: pointer;
                transition: all 0.3s ease;
                flex-shrink: 0;
                position: relative;
            }
            
            .language-selector:hover {
                border-color: #1e40af;
                background: #f8fafc;
            }
            
            /* Mobile Menu Button */
            .mobile-menu-btn {
                display: none;
                flex-direction: column;
                gap: 4px;
                cursor: pointer;
                padding: 0.5rem;
                border: none;
                background: none;
                transition: all 0.3s ease;
            }
            
            .mobile-menu-btn span {
                width: 25px;
                height: 3px;
                background: #1b1b18;
                border-radius: 2px;
                transition: all 0.3s ease;
            }
            
            .mobile-menu-btn.active span:nth-child(1) {
                transform: rotate(45deg) translate(5px, 5px);
            }
            
            .mobile-menu-btn.active span:nth-child(2) {
                opacity: 0;
            }
            
            .mobile-menu-btn.active span:nth-child(3) {
                transform: rotate(-45deg) translate(7px, -6px);
            }
            
            /* Hero Section */
            .hero {
                background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), 
                            url('{{ asset("heroSection.jpg") }}');
                background-size: cover;
                background-position: center;
                min-height: 100vh;
                display: flex;
                align-items: center;
                position: relative;
                overflow: hidden;
            }
            
            .hero::before {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 200px;
                background: linear-gradient(transparent, rgba(255, 255, 255, 0.1));
                clip-path: polygon(0 100%, 100% 100%, 100% 0);
            }
            
            .hero-content {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 4rem;
                align-items: center;
                padding-top: 80px;
            }
            
            .hero-text {
                color: white;
                z-index: 2;
            }
            
            .hero-text h1 {
                font-size: 3.5rem;
                font-weight: 700;
                margin-bottom: 1rem;
                line-height: 1.2;
            }
            
            .hero-text p {
                font-size: 1.25rem;
                margin-bottom: 2rem;
                opacity: 0.9;
            }
            
            .cta-button {
                display: inline-block;
                background: #1e40af;
                color: white;
                padding: 1rem 2rem;
                border-radius: 50px;
                text-decoration: none;
                font-weight: 600;
                font-size: 1.1rem;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
            }
            
            .cta-button:hover {
                background: #1d4ed8;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(30, 64, 175, 0.4);
            }
            
            .hero-image {
                position: relative;
                z-index: 2;
            }
            
            .hero-image img {
                width: 100%;
                height: auto;
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            }
            
                                    /* About Section */
            .about {
                background: url('{{ asset("about.png") }}') center center;
                background-size: 100% 100%;
                background-repeat:  no-repeat;
                color: white;
                padding: 10rem 0;
                position: relative;
                overflow: hidden;
                background: #004799;
                height: 100%;
            }
       
            .about-content {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 4rem;
                align-items: center;
                position: relative;
                z-index: 3;
            }
            
            .about-text h2 {
                font-size: 3rem;
                font-weight: 700;
                margin-bottom: 2rem;
                position: relative;
            }
            
                        .about-text h2::before {
                content: '|';
                color: #60a5fa;
                margin-left: 1rem;
            }
            
            .about-text p {
                font-size: 1.1rem;
                margin-bottom: 2rem;
                line-height: 1.8;
            }
            
            .features-list {
                list-style: none;
            }
            
            .features-list li {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin-bottom: 1rem;
                font-size: 1.1rem;
            }
            
            .feature-icon {
                width: 24px;
                height: 24px;
                background: #60a5fa;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 0.8rem;
            }
            
            .about-images {
                display: flex;
                justify-content: center;
                align-items: center;
            }
            
            .single-circle-image {
                max-width: 500px;
                height: auto;
             }
            
            /* App Section */
            .app-section {
                background: white;
                padding: 6rem 0;
                position: relative;
            }
            
            .app-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100px;
                background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
                clip-path: polygon(0 0, 100% 0, 100% 100%, 0 70%);
            }
            
            .app-content {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 4rem;
                align-items: center;
                margin-top: 2rem;
            }
            
            .app-text h2 {
                font-size: 3rem;
                font-weight: 700;
                margin-bottom: 1rem;
                color: #1e40af;
                position: relative;
            }
            
            .app-text h2::before {
                content: '|';
                color: #60a5fa;
                margin-left: 1rem;
            }
            
            .app-text h3 {
                font-size: 1.5rem;
                color: linear-gradient(135deg,rgb(0, 136, 255) 0%,rgba(7, 156, 255, 0.53) 100%);
                margin-bottom: 1rem;
            }
            
            .app-text p {
                font-size: 1.1rem;
                color: #4b5563;
                margin-bottom: 2rem;
                line-height: 1.8;
            }
            
            .app-features {
                list-style: none;
                margin-bottom: 2rem;
            }
            
            .app-features li {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin-bottom: 1rem;
                font-size: 1.1rem;
                color: #4b5563;
            }
            
            .app-feature-icon {
                width: 40px;
                height: 40px;
                background: #1e40af;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1.2rem;
            }
            
            .app-buttons {
                display: flex;
                gap: 1rem;
                flex-wrap: wrap;
            }
            
            .app-button {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0;
                border-radius: 10px;
                text-decoration: none;
                transition: all 0.3s ease;
                overflow: hidden;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }
            
            .app-button img {
                width: 100%;
                height: auto;
                display: block;
                transition: transform 0.3s ease;
            }
            
            .app-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            }
            
            .app-button:hover img {
                transform: scale(1.05);
            }
            
            .apple-store {
                max-width: 200px;
            }
            
            .google-play {
                max-width: 200px;
            }
            
            .app-image {
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            
            .phone-mockup {
                max-width: 500px;
                height: auto;
             
             }
            
          
            
            /* Customer App Section */
            .customer-app {
                background: #f8fafc;
                padding: 6rem 0;
            }
            
            .customer-app-content {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 4rem;
                align-items: center;
            }
            
            .customer-app-text h2 {
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 1rem;
                color: #1e40af;
                position: relative;
            }
            
            .customer-app-text h2::before {
                content: '|';
                color: #60a5fa;
                margin-left: 1rem;
            }
            
            .customer-app-text p {
                font-size: 1.1rem;
                color: #4b5563;
                margin-bottom: 2rem;
                line-height: 1.8;
            }
            
            /* Footer */
            .footer {
                background: url('{{ asset("Frame 1244832970.png") }}') center center;
                background-size: cover;
                color: white;
                padding: 4rem 0 2rem;
                position: relative;
                overflow: hidden;
            }
            
            .footer::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="%23ffffff" opacity="0.1"/><circle cx="80" cy="40" r="3" fill="%23ffffff" opacity="0.1"/><circle cx="40" cy="80" r="2" fill="%23ffffff" opacity="0.1"/><circle cx="90" cy="90" r="3" fill="%23ffffff" opacity="0.1"/><circle cx="10" cy="60" r="2" fill="%23ffffff" opacity="0.1"/></svg>');
                background-size: 150px 150px;
            }
            
            .footer-content {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 2rem;
                position: relative;
                z-index: 2;
                align-items: start;
            }
            
            .footer-section h3 {
                font-size: 1.2rem;
                font-weight: 600;
                margin-bottom: 1rem;
                color: #1e40af;
            }
            
            .footer-section p,
            .footer-section a {
                color: #4b5563;
                text-decoration: none;
                margin-bottom: 0.5rem;
                display: block;
                transition: color 0.3s ease;
                font-size: 0.9rem;
            }
            
            .footer-section a:hover {
                color: #1e40af;
            }
            
            /* Logo Section */
            .logo-section {
                text-align: center;
            }
            
            .footer-logo-icon {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                margin: 0 auto;
            }
            
            .footer-logo-icon img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
            
            .footer-logo-link {
                text-decoration: none;
                display: block;
                transition: transform 0.3s ease;
            }
            
            .footer-logo-link:hover {
                transform: scale(1.05);
            }
            
            /* Links Section */
            .links-section {
                text-align: center;
            }
            
            .footer-links {
                margin-bottom: 1rem;
            }
            
            .footer-links a {
                display: inline-block;
                margin: 0 0.5rem;
                color: #4b5563;
                font-weight: 500;
            }
            
            .social-links {
                display: flex;
                gap: 0.5rem;
                justify-content: center;
                margin-top: 1rem;
            }
            
            .social-link {
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #1e40af;
                text-decoration: none;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }
            

                        .social-link:hover {
                transform: translateY(-3px) scale(1.05);
            }
            
            /* Social Icons Specific Colors */
            .social-link.linkedin:hover {
                color: #0077b5;
            }
            
            .social-link.instagram:hover {
                color: #e4405f;
            }
            
            .social-link.twitter:hover {
                color: #1da1f2;
            }
            
            .social-link.facebook:hover {
                color: #1877f2;
            }
            
            .social-link.youtube:hover {
                color: #ff0000;
            }
            
            .social-link.whatsapp:hover {
                color: #25d366;
            }
            
            /* Contact Section */
            .contact-section {
                text-align: center;
            }
            
            .contact-info {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .contact-item {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                color: #4b5563;
            }
            
            .contact-icon {
                font-size: 1rem;
                color: #1e40af;
            }
            
            .footer-bottom {
                text-align: center;
                margin-top: 2rem;
                padding-top: 1.5rem;
                border-top: 1px solid #e5e7eb;
                position: relative;
                z-index: 2;
            }
            
            .legal-links {
                margin-bottom: 1rem;
            }
            
            .legal-links a {
                display: inline-block;
                margin: 0 0.5rem;
                color: #4b5563;
                font-size: 0.9rem;
            }
            
            .copyright {
                color: #4b5563;
                font-size: 0.9rem;
                margin: 0;
            }
            
            /* Responsive Design */
            @media (max-width: 768px) {
                /* Header Mobile Styles */
                .nav {
                    justify-content: space-between;
                    gap: 1rem;
                }
                
                .logo-icon {
                    width: 50px;
                    height: 50px;
                }
                
                .nav-links {
                    display: none;
                    position: absolute;
                    top: 100%;
                    left: 0;
                    right: 0;
                    background: rgba(255, 255, 255, 0.98);
                    backdrop-filter: blur(10px);
                    flex-direction: column;
                    gap: 0;
                    padding: 1rem 0;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                    border-top: 1px solid rgba(0, 0, 0, 0.1);
                }
                
                .nav-links.active {
                    display: flex;
                }
                
                .nav-links a {
                    padding: 1rem 2rem;
                    width: 100%;
                    text-align: center;
                    border-radius: 0;
                }
                
                .nav-links a:hover {
                    background: rgba(30, 64, 175, 0.1);
                }
                
                .mobile-menu-btn {
                    display: flex;
                }
                
                .language-selector {
                    padding: 0.4rem 0.8rem;
                    font-size: 0.9rem;
                }
                
                /* Content Mobile Styles */
                .hero-content,
                .about-content,
                .app-content,
                .customer-app-content {
                    grid-template-columns: 1fr;
                    gap: 2rem;
                    text-align: center;
                }
                
                .hero-text h1 {
                    font-size: 2.5rem;
                }
                
                .about-text h2,
                .app-text h2 {
                    font-size: 2rem;
                }
                
                .app-buttons {
                    justify-content: center;
                    flex-direction: column;
                    align-items: center;
                    gap: 1rem;
                }
                
                .app-button {
                    width: 100%;
                    max-width: 250px;
                    justify-content: center;
                }
                
                .container {
                    padding: 0 15px;
                }
                
                .phone-mockup,
                .single-circle-image {
                    max-width: 250px;
                }
                
                .footer-content {
                    grid-template-columns: 1fr;
                    gap: 2rem;
                    text-align: center;
                }
                
                .footer-links a {
                    display: block;
                    margin: 0.5rem 0;
                }
                
                .social-links {
                    justify-content: center;
                }
            }
            
            @media (max-width: 480px) {
                .hero-text h1 {
                    font-size: 2rem;
                }
                
                .about-text h2,
                .app-text h2 {
                    font-size: 1.8rem;
                }
                
                .about-text p,
                .app-text p {
                    font-size: 1rem;
                }
                
                .features-list li,
                .app-features li {
                    font-size: 1rem;
                }
            }
            
            /* Advanced Animations & Effects */
            @keyframes float {
                0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
                25% { transform: translateY(-15px) rotate(2deg) scale(1.02); }
                50% { transform: translateY(-25px) rotate(5deg) scale(1.05); }
                75% { transform: translateY(-15px) rotate(2deg) scale(1.02); }
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(50px) scale(0.95);
                    filter: blur(5px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                    filter: blur(0);
                }
            }
            
            @keyframes fadeInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-50px) scale(0.95);
                    filter: blur(5px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0) scale(1);
                    filter: blur(0);
                }
            }
            
            @keyframes fadeInRight {
                from {
                    opacity: 0;
                    transform: translateX(50px) scale(0.95);
                    filter: blur(5px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0) scale(1);
                    filter: blur(0);
                }
            }
            
            @keyframes scaleIn {
                from {
                    opacity: 0;
                    transform: scale(0.7) rotate(-5deg);
                    filter: blur(3px);
                }
                to {
                    opacity: 1;
                    transform: scale(1) rotate(0deg);
                    filter: blur(0);
                }
            }
            
            @keyframes slideInFromBottom {
                from {
                    opacity: 0;
                    transform: translateY(80px) scale(0.9);
                    filter: blur(3px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                    filter: blur(0);
                }
            }
            
            @keyframes pulse {
                0%, 100% { transform: scale(1) rotate(0deg); }
                25% { transform: scale(1.03) rotate(1deg); }
                50% { transform: scale(1.08) rotate(2deg); }
                75% { transform: scale(1.03) rotate(1deg); }
            }
            
            @keyframes bounce {
                0%, 20%, 50%, 80%, 100% { transform: translateY(0) scale(1); }
                40% { transform: translateY(-15px) scale(1.1); }
                60% { transform: translateY(-8px) scale(1.05); }
            }
            
            @keyframes shimmer {
                0% { background-position: -200% 0; }
                100% { background-position: 200% 0; }
            }
            
            @keyframes glow {
                0%, 100% { box-shadow: 0 0 20px rgba(30, 64, 175, 0.3); }
                50% { box-shadow: 0 0 40px rgba(30, 64, 175, 0.6), 0 0 60px rgba(30, 64, 175, 0.3); }
            }
            
            @keyframes wave {
                0%, 100% { transform: rotate(0deg); }
                25% { transform: rotate(5deg); }
                75% { transform: rotate(-5deg); }
            }
            
            @keyframes sparkle {
                0%, 100% { opacity: 0; transform: scale(0) rotate(0deg); }
                50% { opacity: 1; transform: scale(1) rotate(180deg); }
            }
            
            /* Floating Bubbles */
            .floating-bubble {
                position: absolute;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                animation: float 6s ease-in-out infinite;
            }
            
            .bubble-1 {
                width: 100px;
                height: 100px;
                top: 20%;
                left: 10%;
                animation-delay: 0s;
            }
            
            .bubble-2 {
                width: 150px;
                height: 150px;
                top: 60%;
                right: 15%;
                animation-delay: 2s;
            }
            
            .bubble-3 {
                width: 80px;
                height: 80px;
                bottom: 20%;
                left: 20%;
                animation-delay: 4s;
            }
            
            /* Particle Effects */
            .particles-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 1;
                overflow: hidden;
            }
            
            .particle {
                position: absolute;
                background: radial-gradient(circle, rgba(96, 165, 250, 0.8), rgba(30, 64, 175, 0.4));
                border-radius: 50%;
                animation: particleFloat 8s linear infinite;
                opacity: 0.6;
            }
            
            .particle:nth-child(1) { width: 4px; height: 4px; left: 10%; animation-delay: 0s; }
            .particle:nth-child(2) { width: 6px; height: 6px; left: 20%; animation-delay: 1s; }
            .particle:nth-child(3) { width: 3px; height: 3px; left: 30%; animation-delay: 2s; }
            .particle:nth-child(4) { width: 5px; height: 5px; left: 40%; animation-delay: 3s; }
            .particle:nth-child(5) { width: 4px; height: 4px; left: 50%; animation-delay: 4s; }
            .particle:nth-child(6) { width: 6px; height: 6px; left: 60%; animation-delay: 5s; }
            .particle:nth-child(7) { width: 3px; height: 3px; left: 70%; animation-delay: 6s; }
            .particle:nth-child(8) { width: 5px; height: 5px; left: 80%; animation-delay: 7s; }
            .particle:nth-child(9) { width: 4px; height: 4px; left: 90%; animation-delay: 8s; }
            .particle:nth-child(10) { width: 6px; height: 6px; left: 95%; animation-delay: 9s; }
            
            @keyframes particleFloat {
                0% {
                    transform: translateY(100vh) rotate(0deg);
                    opacity: 0;
                }
                10% {
                    opacity: 0.6;
                }
                90% {
                    opacity: 0.6;
                }
                100% {
                    transform: translateY(-100px) rotate(360deg);
                    opacity: 0;
                }
            }
            
            /* Animated Gradients */
            .animated-gradient-bg {
                background: linear-gradient(-45deg, #1e40af, #3b82f6, #60a5fa, #93c5fd, #1e40af);
                background-size: 400% 400%;
                animation: gradientShift 8s ease infinite;
            }
            
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            
            /* Enhanced Hero Background */
            .hero {
                background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), 
                            url('{{ asset("heroSection.jpg") }}');
                background-size: cover;
                background-position: center;
                min-height: 100vh;
                display: flex;
                align-items: center;
                position: relative;
                overflow: hidden;
            }
            

            
            /* Animated Section Backgrounds */
            .about {
                background: linear-gradient(135deg, #004799 0%, #1e40af 50%, #3b82f6 100%);
                background-size: 200% 200%;
                animation: gradientMove 6s ease infinite;
            }
            
            @keyframes gradientMove {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            
            /* Animated App Section */
            .app-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100px;
                background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
                background-size: 200% 200%;
                animation: gradientShift 4s ease infinite;
                clip-path: polygon(0 0, 100% 0, 100% 100%, 0 70%);
            }
            
            /* Footer */
            .footer {
                background: url('{{ asset("Frame 1244832970.png") }}') center center;
                background-size: cover;
                color: white;
                padding: 4rem 0 2rem;
                position: relative;
                overflow: hidden;
            }
            
            .footer::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="%23ffffff" opacity="0.1"/><circle cx="80" cy="40" r="3" fill="%23ffffff" opacity="0.1"/><circle cx="40" cy="80" r="2" fill="%23ffffff" opacity="0.1"/><circle cx="90" cy="90" r="3" fill="%23ffffff" opacity="0.1"/><circle cx="10" cy="60" r="2" fill="%23ffffff" opacity="0.1"/></svg>');
                background-size: 150px 150px;
            }
            
            /* Entrance Animations */
            .hero-text h1 {
                animation: fadeInLeft 1s ease-out 0.3s both;
            }
            
            .hero-text p {
                animation: fadeInLeft 1s ease-out 0.6s both;
            }
            
            .cta-button {
                animation: fadeInLeft 1s ease-out 0.9s both;
            }
            
            .hero-image {
                animation: fadeInRight 1s ease-out 0.6s both;
            }
            
            /* Section Animations */
            .about-text h2 {
                animation: fadeInUp 1s ease-out 0.3s both;
            }
            
            .about-text p {
                animation: fadeInUp 1s ease-out 0.5s both;
            }
            
            .features-list li {
                animation: fadeInUp 0.8s ease-out both;
            }
            
            .features-list li:nth-child(1) { animation-delay: 0.7s; }
            .features-list li:nth-child(2) { animation-delay: 0.9s; }
            .features-list li:nth-child(3) { animation-delay: 1.1s; }
            .features-list li:nth-child(4) { animation-delay: 1.3s; }
            
            .about-images {
                animation: scaleIn 1s ease-out 0.8s both;
            }
            
            .app-text h2 {
                animation: fadeInUp 1s ease-out 0.3s both;
            }
            
            .app-text h3 {
                animation: fadeInUp 1s ease-out 0.5s both;
            }
            
            .app-text p {
                animation: fadeInUp 1s ease-out 0.7s both;
            }
            
            .app-features li {
                animation: fadeInUp 0.8s ease-out both;
            }
            
            .app-features li:nth-child(1) { animation-delay: 0.9s; }
            .app-features li:nth-child(2) { animation-delay: 1.1s; }
            .app-features li:nth-child(3) { animation-delay: 1.3s; }
            .app-features li:nth-child(4) { animation-delay: 1.5s; }
            .app-features li:nth-child(5) { animation-delay: 1.7s; }
            
            .app-buttons {
                animation: slideInFromBottom 1s ease-out 1.9s both;
            }
            
            .app-image {
                animation: scaleIn 1s ease-out 1s both;
            }
            
            .customer-app-text h2 {
                animation: fadeInUp 1s ease-out 0.3s both;
            }
            
            .customer-app-text p {
                animation: fadeInUp 1s ease-out 0.5s both;
            }
            
            /* Hover Animations */
            .logo-icon:hover {
                animation: pulse 0.6s ease-in-out;
                cursor: pointer;
            }
            
            .cta-button:hover {
                animation: bounce 0.6s ease-in-out;
            }
            
            .app-button:hover img {
                animation: pulse 0.6s ease-in-out;
            }
            
            .feature-icon {
                animation: pulse 2s ease-in-out infinite;
            }
            
            .app-feature-icon {
                animation: pulse 2s ease-in-out infinite;
            }
            
            /* Footer Animations */
            .footer-section {
                animation: fadeInUp 1s ease-out both;
            }
            
            .footer-section:nth-child(1) { animation-delay: 0.2s; }
            .footer-section:nth-child(2) { animation-delay: 0.4s; }
            .footer-section:nth-child(3) { animation-delay: 0.6s; }
            
            .social-link:hover {
                animation: bounce 0.6s ease-in-out;
            }
            
            /* Scroll-triggered animations */
            .animate-on-scroll {
                opacity: 0;
                transform: translateY(30px);
                transition: all 0.8s ease-out;
            }
            
            .animate-on-scroll.animated {
                opacity: 1;
                transform: translateY(0);
            }
            
            /* Loading animation for images */
            .image-loading {
                animation: pulse 1.5s ease-in-out infinite;
            }
            
            /* Smooth transitions for all interactive elements */
            * {
                transition: all 0.3s ease;
            }
            
            /* Special attention to buttons and links */
            .cta-button,
            .app-button,
            .nav-links a,
            .social-link,
            .footer-logo-link {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            /* Advanced Visual Effects */
            .hero::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
                animation: shimmer 3s ease-in-out infinite;
                pointer-events: none;
            }
            
            .cta-button {
                position: relative;
                overflow: hidden;
                background: #1e40af;
                transition: all 0.3s ease;
            }
            
            .cta-button:hover {
                background: #1d4ed8;
                transform: translateY(-2px);
            }
            
            /* Enhanced Feature Icons */
            .feature-icon,
            .app-feature-icon {
                position: relative;
                overflow: hidden;
                background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
                box-shadow: 0 4px 15px rgba(96, 165, 250, 0.4);
            }
            

            
            /* Enhanced App Buttons */
            .app-button {
                position: relative;
                transform-style: preserve-3d;
                perspective: 1000px;
            }
            

            
            /* Enhanced Navigation */
            .nav-links a {
                position: relative;
                overflow: hidden;
            }
            
            .nav-links a::before {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 0;
                height: 2px;
                background: linear-gradient(90deg, #1e40af, #60a5fa);
                transition: width 0.3s ease;
            }
            
            .nav-links a:hover::before {
                width: 100%;
            }
            

            
            /* Enhanced Logo */
            .logo-icon {
                position: relative;
                box-shadow: 0 8px 25px rgba(30, 64, 175, 0.2);
            }
            
            .logo-icon::after {
                content: '';
                position: absolute;
                top: -2px;
                left: -2px;
                right: -2px;
                bottom: -2px;
                background: linear-gradient(45deg, #1e40af, #60a5fa, #3b82f6, #1e40af);
                background-size: 400% 400%;
                border-radius: 50%;
                z-index: -1;
                animation: glow 3s ease-in-out infinite;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .logo-icon:hover::after {
                opacity: 1;
            }
            

            

            
            /* Enhanced Social Links */
            .social-link {
                position: relative;
                overflow: hidden;
            }
            
            .social-link:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            
            /* Enhanced Floating Bubbles */
            .floating-bubble {
                position: relative;
                background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.05));
                backdrop-filter: blur(5px);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            

            
            /* Advanced Particle Effects */
            .particle-trail {
                position: absolute;
                width: 2px;
                height: 2px;
                background: radial-gradient(circle, rgba(96, 165, 250, 0.8), transparent);
                border-radius: 50%;
                pointer-events: none;
                animation: particleTrail 2s ease-out forwards;
            }
            
            @keyframes particleTrail {
                0% {
                    opacity: 1;
                    transform: scale(1);
                }
                100% {
                    opacity: 0;
                    transform: scale(0);
                }
            }
            

            
            /* Enhanced Section Transitions */
            .section-transition {
                position: relative;
                overflow: hidden;
            }
            
            /* Glowing Borders */
            .glow-border {
                position: relative;
            }
            
            /* Enhanced Section Transitions */
            .about,
            .app-section,
            .customer-app {
                position: relative;
                overflow: hidden;
            }
            .hero-text h1,
            .about-text h2{
                color :while;
            }
            /* Enhanced Typography */
            
            .app-text h2,
            .customer-app-text h2 {
                background: linear-gradient(135deg,rgb(0, 136, 255) 0%,rgba(7, 156, 255, 0.53) 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            
            /* Enhanced Mobile Experience */
            @media (max-width: 768px) {
                .floating-bubble {
                    display: none;
                }
                
                .hero::after,
                .footer::after {
                    animation: none;
                }
            }
            
            /* Performance Optimizations */
            .animate-on-scroll {
                will-change: transform, opacity;
            }
            
            .floating-bubble {
                will-change: transform;
            }
            
            /* Accessibility Enhancements */
            @media (prefers-reduced-motion: reduce) {
                *,
                *::before,
                *::after {
                    animation-duration: 0.01ms !important;
                    animation-iteration-count: 1 !important;
                    transition-duration: 0.01ms !important;
                }
            }
        </style>
    </head>
    <body>
        <!-- Particles Container -->
        <div class="particles-container">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
        

        
        <!-- Header -->
        <header class="header">
            <div class="container">
                <nav class="nav">
                    <div class="logo">
                        <div class="logo-icon">
                            <img src="/dashboard/logo.png" alt="موج">
                        </div>
                    </div>
                    
                                        <ul class="nav-links">
                        <li><a href="#home">الرئيسية</a></li>
                        <li><a href="#about">من نحن</a></li>
                        <li><a href="#app">احصل علي التطبيق</a></li>
                        <li><a href="#contact">تواصل معنا</a></li>
                    </ul>
                    
                    <div class="language-selector">
                        <span>🌐</span>
                        <span>AR</span>
                    </div>
                    
                    <button class="mobile-menu-btn" id="mobileMenuBtn">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section id="home" class="hero">
            <div class="floating-bubble bubble-1"></div>
            <div class="floating-bubble bubble-2"></div>
            <div class="floating-bubble bubble-3"></div>
            
            <div class="container">
                <div class="hero-content">
                    <div class="hero-text">
                        <h1>حل متكامل لإدارة</h1>
                        <p>مغسلتك وخدمة عملائك بكل سهولة</p>
                        <a href="#about" class="cta-button">معرفة المزيد</a>
                    </div>
                    
                    <div class="hero-image">
                        <img src="{{ asset('heroSection.jpg') }}" alt="مغسلة حديثة">
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="about animate-on-scroll section-transition glow-border">
            <div class="container">
                <div class="about-content">
                    <div class="about-text">
                        <h2>من نحن</h2>
                        <p>نحن فريق متخصص في تطوير حلول ذكية لإدارة وتشغيل المغاسل نوفر تطبيقين منفصلين يخدمان طرفي العملية بشكل متكامل</p>
                        
                        <ul class="features-list">
                            <li>
                                <span class="feature-icon">✓</span>
                                <span>تتبع الطلب لحظيا</span>
                            </li>
                            <li>
                                <span class="feature-icon">✓</span>
                                <span>خيارات دفع متعددة وآمنة</span>
                            </li>
                            <li>
                                <span class="feature-icon">✓</span>
                                <span>نظام إدارة متكامل للمغسلة</span>
                            </li>
                            <li>
                                <span class="feature-icon">✓</span>
                                <span>تقارير وتحليلات فورية</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="about-images">
                        <img src="{{ asset('cyrcle.png') }}" alt="صورة دائرية" class="single-circle-image">
                    </div>
                </div>
            </div>
        </section>

        <!-- App Section for Laundry Owners/Employees -->
        <section id="app" class="app-section animate-on-scroll section-transition glow-border">
            <div class="container">
                <div class="app-content">
                    <div class="app-text">
                        <h2>احصل علي التطبيق</h2>
                        <h3>سواء كنت صاحب مغسلة أو موظف</h3>
                        <p>تتحكم في مغسلتك بكل سهولة - من استلام الطلبات لين تتبع المخزون وشراء المستلزمات</p>
                        
                        <ul class="app-features">
                            <li>
                                <span class="app-feature-icon">🧴</span>
                                <span>متابعة استهلاك المغسلة للمواد</span>
                            </li>
                            <li>
                                <span class="app-feature-icon">🛒</span>
                                <span>شراء المنتجات اللازمة للمغسلة من التطبيق</span>
                            </li>
                            <li>
                                <span class="app-feature-icon">📋</span>
                                <span>استلام الطلبات من العملاء مباشرة</span>
                            </li>
                            <li>
                                <span class="app-feature-icon">💳</span>
                                <span>طلب دفع عادي أو الشراء بالتقسيط</span>
                            </li>
                            <li>
                                <span class="app-feature-icon">📊</span>
                                <span>نظام دائن ومدين شامل</span>
                            </li>
                        </ul>
                        
                        <div class="app-buttons">
                            <a href="#" class="app-button apple-store">
                                <img src="{{ asset('appleStore.png') }}" alt="Download on the Apple Store">
                            </a>
                            <a href="#" class="app-button google-play">
                                <img src="{{ asset('googlePlay.png') }}" alt="Download on Google Play">
                            </a>
                        </div>
                    </div>
                    
                    <div class="app-image">
                        <img src="{{ asset('mobilesImage.png') }}" alt="تطبيق المغسلة" class="phone-mockup">
                    </div>
                </div>
            </div>
        </section>

        <!-- Customer App Section -->
        <section class="customer-app animate-on-scroll section-transition glow-border">
            <div class="container">
                <div class="customer-app-content">
                    <div class="customer-app-text">
                        <h2>سواء كنت عميل تبحث عن مغسلة قريبة</h2>
                        <p>تطبيقنا بيخلي كل شي أسهل عليك</p>
                        
                        <ul class="app-features">
                            <li>
                                <span class="app-feature-icon">📋</span>
                                <span>تتبع طلبك لحظة بلحظة</span>
                            </li>
                            <li>
                                <span class="app-feature-icon">⭐</span>
                                <span>تقييمات أسعار، والمسافة عنك - كل شيء واضح قدامك</span>
                            </li>
                            <li>
                                <span class="app-feature-icon">💳</span>
                                <span>وسائل دفع متعددة وآمنة</span>
                            </li>
                        </ul>
                        
                        <div class="app-buttons">
                            <a href="#" class="app-button apple-store">
                                <img src="{{ asset('appleStore.png') }}" alt="Download on the Apple Store">
                            </a>
                            <a href="#" class="app-button google-play">
                                <img src="{{ asset('googlePlay.png') }}" alt="Download on Google Play">
                            </a>
                        </div>
                    </div>
                    
                    <div class="app-image">
                        <img src="{{ asset('mobilesImage.png') }}" alt="تطبيق العملاء" class="phone-mockup">
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer id="contact" class="footer animate-on-scroll">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-section logo-section">
                        <div class="footer-logo">
                            <a href="{{ url('/admin/login') }}" class="footer-logo-link">
                                <div class="footer-logo-icon">
                                    <img src="/dashboard/logo.png" alt="موج">
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <div class="footer-section links-section">
                        <div class="footer-links">
                            <a href="#about">من نحن</a>
                            <a href="#app">احصل علي التطبيق</a>
                            <a href="#contact">تواصل معنا</a>
                        </div>
                        <div class="social-links">
                            <a href="#" class="social-link linkedin" title="LinkedIn">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.047-1.032-3.047-1.032 0-1.26 1.317-1.26 3.038v5.578H9.351V9h3.413v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                            <a href="#" class="social-link instagram" title="Instagram">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.919-.058-1.265-.07-1.644-.07-4.849 0-3.204.012-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.058 1.644-.07 4.849-.07zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </a>
                            <a href="#" class="social-link twitter" title="Twitter">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                            </a>
                            <a href="#" class="social-link facebook" title="Facebook">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            <a href="#" class="social-link youtube" title="YouTube">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </a>
                            <a href="#" class="social-link whatsapp" title="WhatsApp">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.242.489 1.67.626.712.226 1.36.194 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="footer-section contact-section">
                        <h3>تواصل معنا</h3>
                        <div class="contact-info">
                            <div class="contact-item">
                                <span class="contact-icon">📧</span>
                                <span>info@mouj.com</span>
                            </div>
                            <div class="contact-item">
                                <span class="contact-icon">📞</span>
                                <span>+966 50 123 4567</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <div class="legal-links">
                        <a href="#">قانوني</a>
                        <a href="#">الخصوصيه</a>
                    </div>
                    <p class="copyright">حقوق الطباعة والنشر تذهب الي 2025 © موج</p>
                </div>
            </div>
        </footer>

        <!-- Smooth Scrolling Script -->
        <script>
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        // Close mobile menu if open
                        closeMobileMenu();
                    }
                });
            });
            
            // Header scroll effect
            window.addEventListener('scroll', () => {
                const header = document.querySelector('.header');
                if (window.scrollY > 100) {
                    header.style.background = 'rgba(255, 255, 255, 0.98)';
                } else {
                    header.style.background = 'rgba(255, 255, 255, 0.95)';
                }
            });
            
            // Mobile menu functionality
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const navLinks = document.querySelector('.nav-links');
            
            function toggleMobileMenu() {
                mobileMenuBtn.classList.toggle('active');
                navLinks.classList.toggle('active');
            }
            
            function closeMobileMenu() {
                mobileMenuBtn.classList.remove('active');
                navLinks.classList.remove('active');
            }
            
            mobileMenuBtn.addEventListener('click', toggleMobileMenu);
            
            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.nav') && navLinks.classList.contains('active')) {
                    closeMobileMenu();
                }
            });
            
            // Close menu on window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    closeMobileMenu();
                }
            });
            
            // Scroll-triggered animations
            function animateOnScroll() {
                const elements = document.querySelectorAll('.animate-on-scroll');
                elements.forEach(element => {
                    const elementTop = element.getBoundingClientRect().top;
                    const elementVisible = 150;
                    
                    if (elementTop < window.innerHeight - elementVisible) {
                        element.classList.add('animated');
                    }
                });
            }
            
            // Add scroll event listener
            window.addEventListener('scroll', animateOnScroll);
            
            // Initial check for elements in view
            animateOnScroll();
            
            // Parallax effect for floating bubbles
            function parallaxBubbles() {
                const scrolled = window.pageYOffset;
                const bubbles = document.querySelectorAll('.floating-bubble');
                
                bubbles.forEach((bubble, index) => {
                    const speed = 0.5 + (index * 0.1);
                    const yPos = -(scrolled * speed);
                    bubble.style.transform = `translateY(${yPos}px)`;
                });
            }
            
            window.addEventListener('scroll', parallaxBubbles);
            
            // Enhanced hover effects for interactive elements
            function addHoverEffects() {
                const interactiveElements = document.querySelectorAll('.cta-button, .app-button, .nav-links a, .social-link');
                
                interactiveElements.forEach(element => {
                    element.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-3px) scale(1.02)';
                    });
                    
                    element.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0) scale(1)';
                    });
                });
            }
            
            // Initialize hover effects
            addHoverEffects();
            
            // Smooth reveal animation for sections
            function revealSections() {
                const sections = document.querySelectorAll('section');
                sections.forEach((section, index) => {
                    const sectionTop = section.getBoundingClientRect().top;
                    const sectionVisible = 100;
                    
                    if (sectionTop < window.innerHeight - sectionVisible) {
                        section.style.opacity = '1';
                        section.style.transform = 'translateY(0)';
                    }
                });
            }
            
            // Add reveal animation styles
            sections.forEach(section => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(30px)';
                section.style.transition = 'all 0.8s ease-out';
            });
            
            // Initial reveal check
            revealSections();
            
            // Add scroll event for section reveal
            window.addEventListener('scroll', revealSections);
            
            // Loading animation for images
            function addImageLoadingAnimation() {
                const images = document.querySelectorAll('img');
                images.forEach(img => {
                    img.addEventListener('load', function() {
                        this.style.animation = 'fadeInUp 0.6s ease-out';
                    });
                    
                    img.addEventListener('error', function() {
                        this.style.animation = 'pulse 1s ease-in-out';
                    });
                });
            }
            
            // Initialize image loading animations
            addImageLoadingAnimation();
            
            // Typing effect for hero title
            function typeWriter(element, text, speed = 100) {
                let i = 0;
                element.innerHTML = '';
                
                function type() {
                    if (i < text.length) {
                        element.innerHTML += text.charAt(i);
                        i++;
                        setTimeout(type, speed);
                    }
                }
                
                type();
            }
            
            // Initialize typing effect when page loads
            window.addEventListener('load', () => {
                const heroTitle = document.querySelector('.hero-text h1');
                if (heroTitle) {
                    const originalText = heroTitle.textContent;
                    typeWriter(heroTitle, originalText, 80);
                }
            });
            
            // Advanced Interactive Features
            function addAdvancedInteractions() {
                // Parallax scrolling for different elements
                window.addEventListener('scroll', () => {
                    const scrolled = window.pageYOffset;
                    const parallaxElements = document.querySelectorAll('.hero-image, .about-images, .app-image');
                    
                    parallaxElements.forEach((element, index) => {
                        const speed = 0.3 + (index * 0.1);
                        const yPos = -(scrolled * speed);
                        element.style.transform = `translateY(${yPos}px)`;
                    });
                });
                
                // Interactive cursor effects
                const cursor = document.createElement('div');
                cursor.className = 'custom-cursor';
                cursor.style.cssText = `
                    position: fixed;
                    width: 20px;
                    height: 20px;
                    background: radial-gradient(circle, rgba(30, 64, 175, 0.8), transparent);
                    border-radius: 50%;
                    pointer-events: none;
                    z-index: 9999;
                    transition: all 0.1s ease;
                    mix-blend-mode: difference;
                `;
                document.body.appendChild(cursor);
                
                document.addEventListener('mousemove', (e) => {
                    cursor.style.left = e.clientX - 10 + 'px';
                    cursor.style.top = e.clientY - 10 + 'px';
                });
                
                // Cursor effects on interactive elements
                const interactiveElements = document.querySelectorAll('a, button, .logo-icon, .feature-icon');
                interactiveElements.forEach(element => {
                    element.addEventListener('mouseenter', () => {
                        cursor.style.transform = 'scale(2)';
                        cursor.style.background = 'radial-gradient(circle, rgba(96, 165, 250, 0.8), transparent)';
                    });
                    
                    element.addEventListener('mouseleave', () => {
                        cursor.style.transform = 'scale(1)';
                        cursor.style.background = 'radial-gradient(circle, rgba(30, 64, 175, 0.8), transparent)';
                    });
                });
                
                // Hide cursor on mobile
                if (window.innerWidth <= 768) {
                    cursor.style.display = 'none';
                }
            }
            
            // Initialize advanced interactions
            addAdvancedInteractions();
            
            // Particle System Management
            function initParticleSystem() {
                const particles = document.querySelectorAll('.particle');
                let mouseX = 0;
                let mouseY = 0;
                
                // Track mouse movement for interactive particles
                document.addEventListener('mousemove', (e) => {
                    mouseX = e.clientX;
                    mouseY = e.clientY;
                    
                    // Make particles slightly follow mouse
                    particles.forEach((particle, index) => {
                        const speed = (index + 1) * 0.1;
                        const deltaX = (mouseX - window.innerWidth / 2) * speed * 0.01;
                        const deltaY = (mouseY - window.innerHeight / 2) * speed * 0.01;
                        
                        particle.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
                    });
                });
                
                // Add random movement to particles
                setInterval(() => {
                    particles.forEach((particle, index) => {
                        const randomX = (Math.random() - 0.5) * 20;
                        const randomY = (Math.random() - 0.5) * 20;
                        const currentTransform = particle.style.transform || '';
                        
                        particle.style.transform = `${currentTransform} translate(${randomX}px, ${randomY}px)`;
                    });
                }, 3000);
                
                // Create additional dynamic particles
                function createDynamicParticle() {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.cssText = `
                        position: absolute;
                        width: ${Math.random() * 6 + 2}px;
                        height: ${Math.random() * 6 + 2}px;
                        left: ${Math.random() * 100}%;
                        top: 100vh;
                        background: radial-gradient(circle, 
                            rgba(${Math.random() * 255}, ${Math.random() * 255}, 255, 0.8), 
                            rgba(30, 64, 175, 0.4));
                        border-radius: 50%;
                        animation: particleFloat ${Math.random() * 4 + 6}s linear infinite;
                        opacity: 0;
                    `;
                    
                    document.querySelector('.particles-container').appendChild(particle);
                    
                    // Remove particle after animation
                    setTimeout(() => {
                        if (particle.parentNode) {
                            particle.parentNode.removeChild(particle);
                        }
                    }, 10000);
                }
                
                // Create particles periodically
                setInterval(createDynamicParticle, 2000);
            }
            
            // Initialize particle system
            initParticleSystem();
            
            // Enhanced Particle Interactions
            function addParticleInteractions() {
                // Add particle trails on mouse move
                let trailTimeout;
                document.addEventListener('mousemove', (e) => {
                    clearTimeout(trailTimeout);
                    
                    trailTimeout = setTimeout(() => {
                        const trail = document.createElement('div');
                        trail.className = 'particle-trail';
                        trail.style.left = e.clientX + 'px';
                        trail.style.top = e.clientY + 'px';
                        document.body.appendChild(trail);
                        
                        // Remove trail after animation
                        setTimeout(() => {
                            if (trail.parentNode) {
                                trail.parentNode.removeChild(trail);
                            }
                        }, 2000);
                    }, 50);
                });
                
                // Add click particle burst
                document.addEventListener('click', (e) => {
                    for (let i = 0; i < 8; i++) {
                        const burstParticle = document.createElement('div');
                        burstParticle.className = 'particle';
                        burstParticle.style.cssText = `
                            position: fixed;
                            width: ${Math.random() * 8 + 3}px;
                            height: ${Math.random() * 8 + 3}px;
                            left: ${e.clientX}px;
                            top: ${e.clientY}px;
                            background: radial-gradient(circle, 
                                rgba(${Math.random() * 255}, ${Math.random() * 255}, 255, 0.9), 
                                rgba(30, 64, 175, 0.6));
                            border-radius: 50%;
                            pointer-events: none;
                            z-index: 10000;
                            animation: particleBurst 1s ease-out forwards;
                        `;
                        
                        document.body.appendChild(burstParticle);
                        
                        // Animate burst
                        const angle = (i / 8) * Math.PI * 2;
                        const distance = Math.random() * 100 + 50;
                        const x = Math.cos(angle) * distance;
                        const y = Math.sin(angle) * distance;
                        
                        burstParticle.animate([
                            { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                            { transform: `translate(${x}px, ${y}px) scale(0)`, opacity: 0 }
                        ], {
                            duration: 1000,
                            easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
                        });
                        
                        // Remove burst particle
                        setTimeout(() => {
                            if (burstParticle.parentNode) {
                                burstParticle.parentNode.removeChild(burstParticle);
                            }
                        }, 1000);
                    }
                });
            }
            
            // Add particle burst animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes particleBurst {
                    0% { transform: translate(0, 0) scale(1); opacity: 1; }
                    100% { transform: translate(var(--x, 0), var(--y, 0)) scale(0); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
            
            // Initialize particle interactions
            addParticleInteractions();
            
            // Smooth reveal with intersection observer
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated');
                        
                        // Add staggered animations to child elements
                        const children = entry.target.querySelectorAll('.feature-icon, .app-feature-icon, .app-button');
                        children.forEach((child, index) => {
                            setTimeout(() => {
                                child.style.animation = 'fadeInUp 0.6s ease-out both';
                            }, index * 100);
                        });
                    }
                });
            }, observerOptions);
            
            // Observe all animate-on-scroll elements
            document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
            
            // Performance monitoring
            let lastScrollTime = Date.now();
            let scrollCount = 0;
            
            function throttleScroll(func, limit) {
                let inThrottle;
                return function() {
                    const args = arguments;
                    const context = this;
                    if (!inThrottle) {
                        func.apply(context, args);
                        inThrottle = true;
                        setTimeout(() => inThrottle = false, limit);
                    }
                }
            }
            
            // Optimized scroll handlers
            const optimizedScrollHandler = throttleScroll(() => {
                scrollCount++;
                if (scrollCount % 5 === 0) {
                    animateOnScroll();
                    parallaxBubbles();
                }
            }, 16); // 60fps
            
            window.addEventListener('scroll', optimizedScrollHandler);
            
            // Add loading states and error handling
            function enhanceImageLoading() {
                const images = document.querySelectorAll('img');
                images.forEach(img => {
                    // Add loading animation
                    img.style.opacity = '0';
                    img.style.transition = 'opacity 0.6s ease-out';
                    
                    img.addEventListener('load', function() {
                        this.style.opacity = '1';
                        this.style.animation = 'fadeInUp 0.6s ease-out';
                    });
                    
                    img.addEventListener('error', function() {
                        this.style.animation = 'pulse 1s ease-in-out';
                        this.style.border = '2px solid #ef4444';
                        this.style.padding = '10px';
                        this.style.backgroundColor = '#fef2f2';
                    });
                });
            }
            
            enhanceImageLoading();
            
            // Add keyboard navigation support
            document.addEventListener('keydown', (e) => {
                switch(e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        const nextSection = document.querySelector('section:not(.hero)');
                        if (nextSection) {
                            nextSection.scrollIntoView({ behavior: 'smooth' });
                        }
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        break;
                    case 'Escape':
                        closeMobileMenu();
                        break;
                }
            });
            
            // Add touch gestures for mobile
            let touchStartY = 0;
            let touchEndY = 0;
            
            document.addEventListener('touchstart', (e) => {
                touchStartY = e.changedTouches[0].screenY;
            });
            
            document.addEventListener('touchend', (e) => {
                touchEndY = e.changedTouches[0].screenY;
                handleSwipe();
            });
            
            function handleSwipe() {
                const swipeThreshold = 50;
                const diff = touchStartY - touchEndY;
                
                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0) {
                        // Swipe up - go to next section
                        const currentSection = document.elementFromPoint(window.innerWidth / 2, window.innerHeight / 2).closest('section');
                        if (currentSection && currentSection.nextElementSibling) {
                            currentSection.nextElementSibling.scrollIntoView({ behavior: 'smooth' });
                        }
                    } else {
                        // Swipe down - go to previous section
                        const currentSection = document.elementFromPoint(window.innerWidth / 2, window.innerHeight / 2).closest('section');
                        if (currentSection && currentSection.previousElementSibling) {
                            currentSection.previousElementSibling.scrollIntoView({ behavior: 'smooth' });
                        }
                    }
                }
            }
            
            // Add scroll progress indicator
            function addScrollProgress() {
                const progressBar = document.createElement('div');
                progressBar.className = 'scroll-progress';
                progressBar.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 0%;
                    height: 3px;
                    background: linear-gradient(90deg, #1e40af, #60a5fa);
                    z-index: 10000;
                    transition: width 0.1s ease;
                `;
                document.body.appendChild(progressBar);
                
                window.addEventListener('scroll', () => {
                    const scrollTop = document.documentElement.scrollTop;
                    const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
                    const progress = (scrollTop / scrollHeight) * 100;
                    progressBar.style.width = progress + '%';
                });
            }
            
            addScrollProgress();
            
            // Add page load performance metrics
            window.addEventListener('load', () => {
                setTimeout(() => {
                    const loadTime = performance.now();
                    console.log(`Page loaded in ${loadTime.toFixed(2)}ms`);
                    
                    // Send performance data to analytics if needed
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'timing_complete', {
                            name: 'load',
                            value: Math.round(loadTime)
                        });
                    }
                    
                    // Hide loading screen
                    const loadingScreen = document.getElementById('loadingScreen');
                    if (loadingScreen) {
                        loadingScreen.classList.add('hidden');
                        setTimeout(() => {
                            loadingScreen.style.display = 'none';
                        }, 200);
                    }
                }, 200); // Show loading for at least 1 second
            });
            
            // Fallback: Hide loading screen if page takes too long
            setTimeout(() => {
                const loadingScreen = document.getElementById('loadingScreen');
                if (loadingScreen && !loadingScreen.classList.contains('hidden')) {
                    loadingScreen.classList.add('hidden');
                    setTimeout(() => {
                        loadingScreen.style.display = 'none';
                    }, 500);
                }
            }, 5000);
        </script>
    </body>
</html>
