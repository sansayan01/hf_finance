<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HF Finance - Premium Loan Management System</title>
    <meta name="description" content="Next-generation loan management platform with AI-powered credit scoring and smart collections">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100">
    
    <!-- Premium Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 nav-premium">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="#" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="font-display font-bold text-2xl text-gradient-gold">HF Finance</span>
                </a>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="nav-premium-item font-medium">Features</a>
                    <a href="#solutions" class="nav-premium-item font-medium">Solutions</a>
                    <a href="#pricing" class="nav-premium-item font-medium">Pricing</a>
                    <a href="#contact" class="nav-premium-item font-medium">Contact</a>
                </div>
                
                <!-- CTA Buttons -->
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/admin') }}" class="btn-secondary">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-premium">Get Started</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-32 overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[600px] bg-gradient-to-br from-amber-200/30 via-blue-200/20 to-purple-200/30 rounded-full blur-3xl opacity-60"></div>
            <div class="absolute bottom-0 right-0 w-[800px] h-[400px] bg-gradient-to-tl from-blue-300/20 to-transparent rounded-full blur-3xl"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <!-- Hero Content -->
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 font-semibold text-sm mb-6 animate-fade-in-up">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Now with AI-Powered Credit Scoring
                    </div>
                    
                    <h1 class="font-display font-bold text-5xl lg:text-7xl leading-tight mb-6 animate-fade-in-up stagger-1">
                        Smart <span class="text-gradient-gold">Loan</span><br>
                        Management<br>
                        <span class="text-gradient-navy">Platform</span>
                    </h1>
                    
                    <p class="text-xl text-slate-600 dark:text-slate-400 mb-8 max-w-xl mx-auto lg:mx-0 animate-fade-in-up stagger-2">
                        Transform your lending operations with our AI-driven platform featuring smart collections, dynamic pricing, and comprehensive risk management.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start animate-fade-in-up stagger-3">
                        <a href="{{ route('register') }}" class="btn-premium text-center">
                            Start Free Trial
                            <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <a href="#demo" class="btn-secondary text-center">
                            Watch Demo
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="flex flex-wrap justify-center lg:justify-start gap-8 mt-12 pt-8 border-t border-slate-200 dark:border-slate-800 animate-fade-in-up stagger-4">
                        <div>
                            <div class="font-display font-bold text-3xl text-gradient-navy">500+</div>
                            <div class="text-sm text-slate-600 dark:text-slate-400">Organizations</div>
                        </div>
                        <div>
                            <div class="font-display font-bold text-3xl text-gradient-gold">$2B+</div>
                            <div class="text-sm text-slate-600 dark:text-slate-400">Loans Processed</div>
                        </div>
                        <div>
                            <div class="font-display font-bold text-3xl text-gradient-navy">99.9%</div>
                            <div class="text-sm text-slate-600 dark:text-slate-400">Uptime</div>
                        </div>
                    </div>
                </div>
                
                <!-- Hero Visual -->
                <div class="relative animate-fade-in-up stagger-2">
                    <div class="relative">
                        <!-- Main Card -->
                        <div class="card-premium p-8 relative z-10">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="font-display font-semibold text-lg">Portfolio Overview</h3>
                                    <p class="text-sm text-slate-500">Last updated: Just now</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="status-dot active"></span>
                                    <span class="text-sm font-medium text-emerald-600">Active</span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                    <p class="text-sm text-slate-500 mb-1">Total Portfolio</p>
                                    <p class="font-display font-bold text-2xl">$4.2M</p>
                                    <p class="text-xs text-emerald-600 mt-1">+12.5%</p>
                                </div>
                                <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                    <p class="text-sm text-slate-500 mb-1">Active Loans</p>
                                    <p class="font-display font-bold text-2xl">1,284</p>
                                    <p class="text-xs text-emerald-600 mt-1">+8.2%</p>
                                </div>
                                <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                    <p class="text-sm text-slate-500 mb-1">Collections</p>
                                    <p class="font-display font-bold text-2xl">$380K</p>
                                    <p class="text-xs text-amber-600 mt-1">This month</p>
                                </div>
                                <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                    <p class="text-sm text-slate-500 mb-1">PAR > 30</p>
                                    <p class="font-display font-bold text-2xl">2.1%</p>
                                    <p class="text-xs text-emerald-600 mt-1">-0.5%</p>
                                </div>
                            </div>
                            
                            <!-- Mini Chart -->
                            <div class="h-24 flex items-end gap-1">
                                <div class="flex-1 bg-amber-200 dark:bg-amber-800 rounded-t" style="height: 40%"></div>
                                <div class="flex-1 bg-amber-300 dark:bg-amber-700 rounded-t" style="height: 60%"></div>
                                <div class="flex-1 bg-amber-400 dark:bg-amber-600 rounded-t" style="height: 45%"></div>
                                <div class="flex-1 bg-amber-500 rounded-t" style="height: 80%"></div>
                                <div class="flex-1 bg-amber-400 dark:bg-amber-600 rounded-t" style="height: 65%"></div>
                                <div class="flex-1 bg-amber-300 dark:bg-amber-700 rounded-t" style="height: 90%"></div>
                                <div class="flex-1 bg-amber-200 dark:bg-amber-800 rounded-t" style="height: 70%"></div>
                            </div>
                        </div>
                        
                        <!-- Floating Cards -->
                        <div class="absolute -top-4 -right-4 card-premium p-4 animate-float" style="animation-delay: 0.5s">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm">Loan Approved</p>
                                    <p class="text-xs text-slate-500">$25,000</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="absolute -bottom-4 -left-4 card-premium p-4 animate-float" style="animation-delay: 1s">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm">AI Score</p>
                                    <p class="text-xs text-slate-500">745 - Good</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 lg:py-32 bg-white dark:bg-slate-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="font-display font-bold text-4xl lg:text-5xl mb-4">
                    Built for Modern <span class="text-gradient-gold">Lenders</span>
                </h2>
                <p class="text-xl text-slate-600 dark:text-slate-400">
                    Everything you need to manage loans, from application to recovery, powered by AI.
                </p>
            </div>
            
            <!-- Feature Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="card-premium p-8 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center mb-6 shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-xl mb-3">AI Credit Scoring</h3>
                    <p class="text-slate-600 dark:text-slate-400">Machine learning-powered risk assessment with 99.4% accuracy for smarter lending decisions.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="card-premium p-8 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center mb-6 shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-xl mb-3">Smart Collections</h3>
                    <p class="text-slate-600 dark:text-slate-400">AI-powered collection prioritization with optimal contact timing and recovery predictions.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="card-premium p-8 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-xl mb-3">Dynamic Pricing</h3>
                    <p class="text-slate-600 dark:text-slate-400">Risk-based interest rates that adapt to borrower profiles for competitive pricing.</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="card-premium p-8 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center mb-6 shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-xl mb-3">Document OCR</h3>
                    <p class="text-slate-600 dark:text-slate-400">Automated KYC verification with fraud detection and confidence scoring.</p>
                </div>
                
                <!-- Feature 5 -->
                <div class="card-premium p-8 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-500 to-rose-700 flex items-center justify-center mb-6 shadow-lg shadow-rose-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-xl mb-3">Fraud Detection</h3>
                    <p class="text-slate-600 dark:text-slate-400">Real-time fraud pattern recognition with automated alerts and risk scoring.</p>
                </div>
                
                <!-- Feature 6 -->
                <div class="card-premium p-8 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-500 to-cyan-700 flex items-center justify-center mb-6 shadow-lg shadow-cyan-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-xl mb-3">WhatsApp Chatbot</h3>
                    <p class="text-slate-600 dark:text-slate-400">24/7 AI assistant for borrower queries, payments, and EMI reminders.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 lg:py-32 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 to-slate-800"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23D4AF37" fill-opacity="0.05"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
        
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="font-display font-bold text-4xl lg:text-5xl text-white mb-6">
                Ready to Transform Your<br><span class="text-gradient-gold">Lending Operations?</span>
            </h2>
            <p class="text-xl text-slate-300 mb-10 max-w-2xl mx-auto">
                Join 500+ financial institutions using HF Finance to streamline their loan management and boost collections.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="btn-premium">
                    Get Started Free
                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="#contact" class="px-8 py-4 rounded-xl border-2 border-white/30 text-white font-semibold hover:bg-white/10 transition-colors">
                    Contact Sales
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div class="col-span-2">
                    <a href="#" class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="font-display font-bold text-2xl text-white">HF Finance</span>
                    </a>
                    <p class="max-w-sm">Next-generation loan management platform powering financial institutions worldwide.</p>
                </div>
                <div>
                    <h4 class="font-display font-semibold text-white mb-4">Product</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Integrations</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-display font-semibold text-white mb-4">Company</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition-colors">About</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="divider-premium mb-8"></div>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p>&copy; {{ date('Y') }} HF Finance. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition-colors">Privacy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms</a>
                    <a href="#" class="hover:text-white transition-colors">Security</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
