<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login | SX2 LADOR — Secure Access</title>

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
                        mono: ['Space Grotesk', 'monospace'],
                    },
                    colors: {
                        accent: {
                            DEFAULT: '#8b5cf6',
                            hover: '#7c3aed',
                            light: '#a78bfa',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Fixed: body background same premium style as homepage */
        body {
            background-color: #0b0f1c;
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 90% 70%, rgba(139, 92, 246, 0.12) 0%, transparent 55%),
                radial-gradient(circle at 40% 90%, rgba(236, 72, 153, 0.06) 0%, transparent 45%);
            background-attachment: fixed;
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        
        /* FIXED: glass-card with proper background variable defined */
        .glass-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }
        
        /* refined login input — smooth & premium */
        .login-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #f8fafc;
            transition: all 0.25s ease-in-out;
            font-weight: 500;
        }
        
        .login-input:focus {
            background: rgba(255, 255, 255, 0.09);
            border-color: #8b5cf6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.25);
        }
        
        /* input monospace for dots and text consistency */
        .login-input.font-mono {
            letter-spacing: 0.3px;
        }
        
        /* glow button improved */
        .glow-btn {
            background: linear-gradient(105deg, #7c3aed 0%, #a855f7 100%);
            box-shadow: 0 6px 18px rgba(124, 58, 237, 0.25);
            transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            border: none;
        }
        
        .glow-btn:hover {
            box-shadow: 0 10px 28px rgba(139, 92, 246, 0.35);
            transform: translateY(-2px);
        }
        
        /* custom scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #8b5cf6;
            border-radius: 12px;
        }
        
        /* selection smooth */
        ::selection {
            background: rgba(139, 92, 246, 0.4);
            color: white;
        }
        
        /* extra animation for card fade-in */
        .fade-up {
            animation: fadeUp 0.5s ease-out;
        }
        
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-5">

    <div class="w-full max-w-md fade-up">
        <!-- Logo/Header with refined glow -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 mx-auto mb-5 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-xl shadow-indigo-500/20 transition-transform hover:scale-105 duration-300">
                <i class="fas fa-shield-alt text-4xl text-white"></i>
            </div>
            <h1 class="text-3xl font-extrabold bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent tracking-tight">Welcome Back</h1>
            <p class="text-slate-400 mt-1 text-sm">Sign in to your SX2 LADOR account</p>
        </div>

        <!-- Login Card — FIXED: using glass-card class instead of broken bg-slate-800 -->
        <div class="glass-card rounded-3xl p-8 shadow-2xl border border-white/10">
            
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('msgDanger')) : ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-300 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2 backdrop-blur-sm">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?= session()->getFlashdata('msgDanger') ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('msgSuccess')) : ?>
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2 backdrop-blur-sm">
                    <i class="fas fa-check-circle"></i>
                    <span><?= session()->getFlashdata('msgSuccess') ?></span>
                </div>
            <?php endif; ?>

            <?= form_open() ?>
                
                <!-- CRITICAL FIX: IP hidden input now uses REMOTE_ADDR (real IP address) instead of USER_AGENT -->
                <input type="hidden" name="ip" value="<?= $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' ?>">
                <input type="hidden" name="stay_log" value="yes">

                <!-- Username Field -->
                <div class="mb-5">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-user-circle text-slate-400 text-md"></i>
                        </div>
                        <input type="text" name="username" class="login-input w-full pl-11 pr-4 py-3.5 rounded-xl font-medium placeholder:text-slate-500" placeholder="Username" required autocomplete="off">
                    </div>
                    <?php if ($validation->hasError('username')) : ?>
                        <p class="text-rose-400 text-xs mt-2 ml-1 flex items-center gap-1"><i class="fas fa-circle-exclamation text-[10px]"></i> <?= $validation->getError('username') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Password Field with Toggle & Monospace for Dots -->
                <div class="mb-8">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-slate-400"></i>
                        </div>
                        <!-- Added font-mono class for consistent dots/text sizing -->
                        <input type="password" name="password" id="password" class="login-input w-full pl-11 pr-11 py-3.5 rounded-xl font-mono font-medium placeholder:text-slate-500 tracking-wide" placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-200 transition cursor-pointer">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    <?php if ($validation->hasError('password')) : ?>
                        <p class="text-rose-400 text-xs mt-2 ml-1 flex items-center gap-1"><i class="fas fa-circle-exclamation text-[10px]"></i> <?= $validation->getError('password') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Submit Button with refined design -->
                <button type="submit" class="glow-btn w-full py-3.5 rounded-xl text-white font-bold text-lg tracking-wide flex items-center justify-center gap-2 transition-all duration-300 group">
                    <i class="fas fa-arrow-right-to-bracket text-sm group-hover:translate-x-0.5 transition-transform"></i>
                    Sign In
                </button>

            <?= form_close() ?>
            
            <!-- Extra subtle divider (optional smoothness) -->
            <div class="relative my-7">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/10"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="bg-[rgba(15,23,42,0.5)] px-3 text-slate-400 backdrop-blur-sm">secure access</span>
                </div>
            </div>
            
            <!-- Quick tip line -->
            <div class="text-center text-xs text-slate-500 flex items-center justify-center gap-1.5">
                <i class="fas fa-shield-heart text-indigo-400 text-[11px]"></i>
                <span>SYSTEM SX2 LADOR</span>
            </div>
        </div>

        <!-- Footer Links with smooth hover -->
        <div class="text-center mt-8">
            <p class="text-slate-400 text-sm mb-2 flex items-center justify-center gap-1.5">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                Buy account? 
                <a href="https://t.me/Sa_Nso" target="_blank" class="text-indigo-300 hover:text-white transition-all underline decoration-indigo-500/30 underline-offset-4 font-medium">Contact Support</a>
            </p>
            <p class="text-slate-600 text-xs tracking-wide">
                © <?= date('Y') ?> SX2 LADOR — Premium License System
            </p>
        </div>
    </div>

    <!-- Toggle Password JavaScript (smooth & refined) -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
        
        // optional: add small console greeting
        console.log("SX2 LADOR Login — premium interface ready (fixed IP & glass card)");
    </script>
</body>
</html>