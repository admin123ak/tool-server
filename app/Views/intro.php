<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>SX2 LADOR | Premium License Management</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet" crossorigin="anonymous">

    <!-- Google Fonts: Inter & Space Grotesk -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Space Grotesk', 'monospace'],
                    },
                    colors: {
                        accent: {
                            DEFAULT: '#6366f1',
                            hover: '#4f46e5',
                            light: '#a5b4fc',
                        },
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'glow-pulse': 'glowPulse 3s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-8px)' },
                        },
                        glowPulse: {
                            '0%, 100%': { boxShadow: '0 0 20px rgba(99, 102, 241, 0.2)' },
                            '50%': { boxShadow: '0 0 35px rgba(99, 102, 241, 0.35)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }
        
        body {
            background: linear-gradient(145deg, #0a0f1e 0%, #0c1222 100%);
            color: #eef2ff;
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Soft noise texture for depth (optional, eye-friendly) */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(rgba(99, 102, 241, 0.04) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        /* Improved glass card — smooth and readable */
        .glass-card {
            background: rgba(18, 25, 45, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(99, 102, 241, 0.2);
            transition: all 0.3s ease-in-out;
        }
        
        /* Hero gradient softer to eyes */
        .hero-gradient {
            background: radial-gradient(ellipse 70% 50% at 50% 40%, rgba(99, 102, 241, 0.1) 0%, transparent 60%),
                        radial-gradient(ellipse at bottom left, rgba(139, 92, 246, 0.06) 0%, transparent 55%);
        }

        /* Premium glow button — softer glow, not harsh */
        .glow-btn {
            background: linear-gradient(105deg, #4f46e5 0%, #7c3aed 100%);
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.2);
            transition: all 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            border: none;
        }

        .glow-btn:hover {
            box-shadow: 0 10px 28px rgba(99, 102, 241, 0.3);
            transform: translateY(-2px) scale(1.01);
        }

        /* secondary button with smoothness */
        .secondary-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(99, 102, 241, 0.25);
            transition: all 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        .secondary-btn:hover {
            background: rgba(99, 102, 241, 0.2);
            border-color: rgba(99, 102, 241, 0.5);
            transform: translateY(-2px);
        }

        /* Feature card — premium yet soft on eyes */
        .feature-card {
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(99, 102, 241, 0.12);
            transition: all 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.2);
        }

        .feature-card:hover {
            background: rgba(30, 41, 59, 0.65);
            border-color: rgba(99, 102, 241, 0.35);
            transform: translateY(-5px);
            box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.4);
        }

        /* Stat card elegant */
        .stat-card {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.05) 100%);
            border: 1px solid rgba(99, 102, 241, 0.18);
            backdrop-filter: blur(4px);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            border-color: rgba(99, 102, 241, 0.4);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(139, 92, 246, 0.09) 100%);
        }

        /* custom scrollbar soft */
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #4f46e5;
            border-radius: 12px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #818cf8;
        }

        /* text selection soft */
        ::selection {
            background: rgba(99, 102, 241, 0.4);
            color: white;
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden flex flex-col relative">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-card border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="#" class="flex items-center gap-3 group transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-md transition-transform group-hover:scale-105">
                    <i class="fas fa-bolt text-white text-lg"></i>
                </div>
                <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">SX2 LADOR</span>
            </a>
            <div class="flex items-center gap-4">
                <a href="<?= base_url('login') ?>" class="glow-btn px-5 py-2.5 rounded-xl text-sm font-semibold text-white tracking-wide flex items-center gap-2 transition-all">
                    <i class="fas fa-arrow-right-to-bracket text-xs"></i> Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient min-h-screen flex items-center justify-center pt-24 pb-20 px-6 relative">
        <div class="max-w-6xl mx-auto text-center relative z-10">
            <!-- Cyberpunk status chip -->
            <div class="inline-flex items-center gap-3 px-5 py-2 rounded glass-card mb-10" style="font-family:'Share Tech Mono',monospace;letter-spacing:.12em">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute h-full w-full rounded-full bg-emerald-400 opacity-70"></span>
                    <span class="rounded-full h-2.5 w-2.5 bg-emerald-500 shadow-sm"></span>
                </span>
                <span class="text-xs font-semibold text-emerald-400 uppercase">[ NODE_01 :: ONLINE ] // UPTIME 99.97%</span>
            </div>

            <!-- Glitch heading (data-text bật hiệu ứng glitch trong cyberpunk.css) -->
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-black mb-8 leading-[1.1] tracking-tight"
                data-text="LICENSE //"
                style="font-family:'Orbitron',sans-serif">
                <span class="bg-gradient-to-r from-white via-gray-100 to-slate-300 bg-clip-text text-transparent">LICENSE //</span>
                <br>
                <span class="bg-gradient-to-r from-indigo-300 via-purple-300 to-pink-300 bg-clip-text text-transparent">CONTROL_GRID</span>
            </h1>

            <p class="text-lg md:text-xl text-slate-300 max-w-2xl mx-auto mb-12 leading-relaxed font-medium"
               style="font-family:'Share Tech Mono',monospace">
                &gt; Encrypted key vault. Realtime loader pipeline. Zero-trust auth.
                <span class="block text-slate-400 text-base mt-2">&gt; Built for games &amp; software protection. 24/7 uplink.</span>
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-5 mb-20">
                <a href="<?= base_url('login') ?>" class="glow-btn px-8 py-4 rounded text-lg font-bold flex items-center gap-3 w-full sm:w-auto justify-center transition-all group">
                    <i class="fas fa-terminal group-hover:translate-x-1 transition-transform"></i>
                    ENTER PANEL ▸
                </a>
                <a href="<?= base_url('Getkey.php') ?>" class="secondary-btn px-8 py-4 rounded text-lg font-semibold flex items-center gap-3 w-full sm:w-auto justify-center transition-all group">
                    <i class="fas fa-key group-hover:scale-110 transition-transform"></i>
                    GET_FREE_KEY
                </a>
            </div>

            <!-- Stats - HUD style -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5 max-w-4xl mx-auto">
                <div class="stat-card p-5 rounded text-center">
                    <div class="text-xs text-slate-400 uppercase tracking-widest mb-2" style="font-family:'Share Tech Mono',monospace">// LIC_TOTAL</div>
                    <div class="text-4xl font-black bg-gradient-to-r from-indigo-400 to-indigo-300 bg-clip-text text-transparent">0043</div>
                </div>
                <div class="stat-card p-5 rounded text-center">
                    <div class="text-xs text-slate-400 uppercase tracking-widest mb-2" style="font-family:'Share Tech Mono',monospace">// USR_ACTIVE</div>
                    <div class="text-4xl font-black bg-gradient-to-r from-purple-400 to-purple-300 bg-clip-text text-transparent">0013</div>
                </div>
                <div class="stat-card p-5 rounded text-center">
                    <div class="text-xs text-slate-400 uppercase tracking-widest mb-2" style="font-family:'Share Tech Mono',monospace">// GAME_NODES</div>
                    <div class="text-4xl font-black bg-gradient-to-r from-pink-400 to-rose-300 bg-clip-text text-transparent">005</div>
                </div>
                <div class="stat-card p-5 rounded text-center">
                    <div class="text-xs text-slate-400 uppercase tracking-widest mb-2" style="font-family:'Share Tech Mono',monospace">// UPTIME</div>
                    <div class="text-4xl font-black bg-gradient-to-r from-emerald-400 to-emerald-300 bg-clip-text text-transparent">99.9</div>
                </div>
            </div>
        </div>
        
        <!-- subtle background elements without harshness -->
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl -z-0"></div>
        <div class="absolute top-40 right-0 w-72 h-72 bg-purple-500/10 rounded-full blur-3xl -z-0"></div>
    </section>

    <!-- Features Section -->
    <section class="py-24 px-6 relative">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <div class="inline-block px-4 py-1.5 rounded-full bg-indigo-500/10 text-indigo-300 text-xs font-semibold tracking-wide mb-5 border border-indigo-500/20">
                    CORE FEATURES
                </div>
                <h2 class="text-4xl md:text-5xl font-bold mb-5 text-white tracking-tight">Why Choose SX2 LADOR</h2>
                <p class="text-slate-300 text-lg max-w-2xl mx-auto">Everything you need to manage your license keys in one powerful ecosystem.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
                <!-- feature cards with eye-friendly text colors -->
                <div class="feature-card p-7 rounded-2xl transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center mb-5 shadow-sm">
                        <i class="fas fa-bolt text-2xl text-indigo-300"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Instant Generation</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">Generate hundreds of unique license keys in seconds with custom prefixes and flexible durations.</p>
                </div>

                <div class="feature-card p-7 rounded-2xl transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center mb-5 shadow-sm">
                        <i class="fas fa-shield-alt text-2xl text-emerald-300"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">HWID Protection</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">Advanced hardware ID binding ensures each license is securely tied to specific devices.</p>
                </div>

                <div class="feature-card p-7 rounded-2xl transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500/20 to-orange-500/20 flex items-center justify-center mb-5 shadow-sm">
                        <i class="fas fa-chart-line text-2xl text-amber-300"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Real-time Analytics</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">Monitor API traffic, login activity, and key usage with intuitive live dashboards.</p>
                </div>

                <div class="feature-card p-7 rounded-2xl transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-pink-500/20 to-rose-500/20 flex items-center justify-center mb-5 shadow-sm">
                        <i class="fas fa-users text-2xl text-pink-300"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Multi-Role System</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">Owner, Admin, and Reseller roles with granular permission controls and logs.</p>
                </div>

                <div class="feature-card p-7 rounded-2xl transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500/20 to-cyan-500/20 flex items-center justify-center mb-5 shadow-sm">
                        <i class="fas fa-gamepad text-2xl text-blue-300"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Multi-Game Support</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">Manage licenses for multiple games with individual pricing, status and settings.</p>
                </div>

                <div class="feature-card p-7 rounded-2xl transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-violet-500/20 to-purple-500/20 flex items-center justify-center mb-5 shadow-sm">
                        <i class="fas fa-code text-2xl text-violet-300"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">API Integration</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">RESTful API for seamless integration with your applications, launchers and tools.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Additional Premium CTA with soft design -->
    <section class="py-16 px-6">
        <div class="max-w-5xl mx-auto glass-card rounded-3xl p-8 md:p-12 text-center border border-indigo-500/20 backdrop-blur-xl">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-left md:text-left">
                    <h3 class="text-2xl md:text-3xl font-bold text-white mb-2">Ready to scale your license system?</h3>
                    <p class="text-slate-300">Join SX2 LADOR today and experience enterprise security with zero hassle.</p>
                </div>
                <a href="<?= base_url('login') ?>" class="glow-btn px-8 py-3.5 rounded-xl font-bold flex items-center gap-2 transition-all whitespace-nowrap">
                    Get Started <i class="fas fa-arrow-right ml-1 group-hover:translate-x-1 transition"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 px-6 border-t border-white/5 mt-auto bg-black/10">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                        <i class="fas fa-bolt text-white text-sm"></i>
                    </div>
                    <span class="font-bold text-white tracking-tight">SX2 LADOR</span>
                    <span class="text-xs text-slate-500 hidden md:inline-block ml-2">|</span>
                    <span class="text-xs text-slate-400 hidden md:inline-block">Premium License Infrastructure</span>
                </div>

                <div class="flex items-center gap-7 text-sm text-slate-300">
                    <a href="<?= base_url('login') ?>" class="hover:text-white transition-all hover:scale-105 inline-block">Login</a>
                    <!-- Footer link also Getkey.php matching original exactly -->
                    <a href="<?= base_url('Getkey.php') ?>" class="hover:text-white transition-all hover:scale-105 inline-block">Free Keys</a>
                    <a href="https://t.me/VIPTEAM08" target="_blank" class="hover:text-white transition-all flex items-center gap-1.5 hover:scale-105 inline-block">
                        <i class="fab fa-telegram"></i> Contact
                    </a>
                </div>

                <div class="text-xs text-slate-400 font-mono">
                    © <?= date('Y') ?> SX2 LADOR — All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <!-- subtle smoothness script (optional) -->
    <script>
        (function() {
            // Just smooth console greet, no heavy UI interference
            console.log("SX2 LADOR | Premium & Smooth UI — Getkey.php ready");
        })();
    </script>
</body>
</html>