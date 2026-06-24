<?php
// Ensure session check
if (!session()->has('userid')) {
    return redirect()->to('login');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Account Settings | SX2 LADOR — Premium License Manager</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['Space Grotesk', 'monospace'],
                    },
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #0b0f1c;
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 90% 70%, rgba(139, 92, 246, 0.12) 0%, transparent 55%),
                radial-gradient(circle at 40% 90%, rgba(236, 72, 153, 0.06) 0%, transparent 45%);
            background-attachment: fixed;
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
        }
        
        .glass-panel {
            background: rgba(30, 41, 59, 0.45);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .sidebar-link {
            transition: all 0.2s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }
        
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(99, 102, 241, 0.2);
            color: white;
            border-right: 3px solid #8b5cf6;
        }
        
        input, button {
            transition: all 0.3s ease-in-out;
        }
        
        input:focus {
            transition: all 0.3s ease-in-out;
        }

        .card-enter {
            animation: slideIn 0.4s ease-out;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(30, 41, 59, 0.5); 
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(139, 92, 246, 0.5); 
            border-radius: 10px;
        }
        
        .toast-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(99, 102, 241, 0.4);
            border-radius: 1rem;
            padding: 0.75rem 1.5rem;
            z-index: 1000;
            animation: fadeInOut 2s ease forwards;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }
        
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(20px); }
            15% { opacity: 1; transform: translateY(0); }
            85% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(20px); visibility: hidden; }
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-sm">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 glass-panel transform -translate-x-full transition-transform duration-300 ease-in-out md:relative md:translate-x-0 md:flex md:flex-col flex-col justify-between border-r border-white/10 shadow-2xl">
        <div>
            <div class="h-20 flex items-center px-6 border-b border-white/10">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 shadow-lg shadow-indigo-500/20">
                    <i class="fas fa-shield-alt text-white text-base"></i>
                </div>
                <div>
                    <h1 class="font-bold text-white text-lg tracking-tight leading-tight">SX2 LADOR</h1>
                    <span class="text-[10px] text-slate-400 font-mono tracking-wider">LICENSE MANAGER</span>
                </div>
            </div>

            <nav class="mt-6 px-3 space-y-1">
                <div class="px-3 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Main</div>
                
                <a href="<?= site_url('dashboard') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300 group transition-all">
                    <i class="fas fa-chart-pie w-6 text-center mr-2 text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium text-sm">Overview</span>
                </a>
                
                <a href="<?= site_url('keys/generate') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300 group transition-all">
                    <i class="fas fa-bolt w-6 text-center mr-2 text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium text-sm">Generate Keys</span>
                </a>
                
                <a href="<?= site_url('keys') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300 group transition-all">
                    <i class="fas fa-key w-6 text-center mr-2 text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium text-sm">License Manager</span>
                </a>

                <div class="px-3 mt-6 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Configuration</div>
                
                <a href="<?= site_url('settings') ?>" class="sidebar-link active flex items-center px-3 py-3 rounded-lg text-slate-300 group transition-all">
                    <i class="fas fa-cog w-6 text-center mr-2 text-indigo-400 transition-colors"></i>
                    <span class="font-medium text-sm">Settings</span>
                </a>
                
                <?php if (isset($user->uplink) && ($user->uplink == 'PROFESSOR' || $user->level == 1)) : ?>
                <a href="<?= site_url('admin/manage-users') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300 group transition-all">
                    <i class="fas fa-users w-6 text-center mr-2 text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium text-sm">Manage Users</span>
                </a>
                <a href="<?= site_url('admin/create-referral') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300 group transition-all">
                    <i class="fas fa-user-plus w-6 text-center mr-2 text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium text-sm">Create User</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>

        <div class="p-4 border-t border-white/10 mt-auto">
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-700 to-slate-800 flex items-center justify-center text-white font-bold border border-white/15 shadow-md">
                    <?= isset($user->username) ? strtoupper(substr($user->username, 0, 1)) : 'U' ?>
                </div>
                <div class="overflow-hidden">
                    <h4 class="text-white font-semibold truncate text-sm"><?= $user->username ?? 'User' ?></h4>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs text-slate-300 font-medium">Online</span>
                    </div>
                </div>
            </div>
            <a href="<?= site_url('logout') ?>" class="flex items-center justify-center w-full py-2.5 px-4 rounded-xl border border-red-500/30 bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-all font-semibold text-xs gap-2">
                <i class="fas fa-power-off text-xs"></i> LOGOUT SESSION
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden relative">
        <!-- Top Bar -->
        <header class="h-20 flex items-center justify-between px-4 md:px-8 border-b border-white/10 bg-slate-900/40 backdrop-blur-md z-10">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden p-2.5 text-white hover:bg-white/10 rounded-xl transition-all duration-200">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent" data-text="// SYSTEM.CONFIG" style="font-family:'Orbitron',sans-serif">// SYSTEM.CONFIG</h2>
            </div>
            
            <!-- FIXED: Balance card made smaller - reduced padding, icon size, and text size -->
            <div class="flex items-center gap-3">
                <div class="glass-panel px-3 py-1.5 rounded-xl flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-amber-500/15 flex items-center justify-center text-amber-400">
                        <i class="fas fa-coins text-xs"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-[8px] text-slate-400 uppercase font-black tracking-wider">Balance</p>
                        <p class="text-white font-mono font-bold text-sm">₹<?= isset($user->saldo) ? number_format($user->saldo, 2) : '0.00' ?></p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            <div class="max-w-5xl mx-auto card-enter">
                
                <!-- Page Messages -->
                <?php if (session()->getFlashdata('msgDanger')) : ?>
                    <div class="bg-red-500/10 border border-red-500/20 text-red-300 px-5 py-3.5 rounded-xl mb-6 text-sm flex items-center gap-3 alert-box backdrop-blur-sm">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= session()->getFlashdata('msgDanger') ?>
                    </div>
                <?php elseif (session()->getFlashdata('msgSuccess')) : ?>
                    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 px-5 py-3.5 rounded-xl mb-6 text-sm flex items-center gap-3 alert-box backdrop-blur-sm">
                        <i class="fas fa-check-circle"></i>
                        <?= session()->getFlashdata('msgSuccess') ?>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- Change Password Card -->
                    <div class="glass-panel rounded-3xl p-6 md:p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-500/15 flex items-center justify-center text-indigo-400 border border-indigo-500/25">
                                <i class="fas fa-lock text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Change Password</h3>
                                <p class="text-slate-400 text-xs">Update your security credentials</p>
                            </div>
                        </div>

                        <?= form_open() ?>
                        <input type="hidden" name="password_form" value="1">
                        
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Current Password</label>
                                <div class="relative group">
                                    <i class="fas fa-shield absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-indigo-400 transition-colors"></i>
                                    <input type="password" name="current" id="current" placeholder="Enter current password"
                                        class="w-full bg-slate-800/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-300 ease-in-out placeholder-slate-600">
                                </div>
                                <?php if ($validation->hasError('current')) : ?>
                                    <p class="text-[11px] text-red-400 ml-1"><?= $validation->getError('current') ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">New Password</label>
                                <div class="relative group">
                                    <i class="fas fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-indigo-400 transition-colors"></i>
                                    <input type="password" name="password" id="password" placeholder="Min 6 characters"
                                        class="w-full bg-slate-800/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-300 ease-in-out placeholder-slate-600">
                                </div>
                                <?php if ($validation->hasError('password')) : ?>
                                    <p class="text-[11px] text-red-400 ml-1"><?= $validation->getError('password') ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Confirm New Password</label>
                                <div class="relative group">
                                    <i class="fas fa-check-double absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-indigo-400 transition-colors"></i>
                                    <input type="password" name="password2" id="password2" placeholder="Repeat new password"
                                        class="w-full bg-slate-800/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-300 ease-in-out placeholder-slate-600">
                                </div>
                                <?php if ($validation->hasError('password2')) : ?>
                                    <p class="text-[11px] text-red-400 ml-1"><?= $validation->getError('password2') ?></p>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold hover:shadow-lg hover:shadow-indigo-500/30 transition-all duration-300 text-sm uppercase tracking-widest mt-4 hover:scale-[1.02]">
                                Change Password
                            </button>
                        </div>
                        <?= form_close() ?>
                    </div>

                    <!-- Account Information Card -->
                    <div class="glass-panel rounded-3xl p-6 md:p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 flex items-center justify-center text-emerald-400 border border-emerald-500/25">
                                <i class="fas fa-user-circle text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Profile Details</h3>
                                <p class="text-slate-400 text-xs">Manage your personal identification</p>
                            </div>
                        </div>

                        <?= form_open() ?>
                        <input type="hidden" name="fullname_form" value="1">
                        
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Full Name</label>
                                <div class="relative group">
                                    <i class="fas fa-id-card absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-emerald-400 transition-colors"></i>
                                    <input type="text" name="fullname" id="fullname" value="<?= old('fullname') ?: ($user->fullname ?? '') ?>" placeholder="Your display name"
                                        class="w-full bg-slate-800/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-300 ease-in-out placeholder-slate-600">
                                </div>
                                <?php if ($validation->hasError('fullname')) : ?>
                                    <p class="text-[11px] text-red-400 ml-1"><?= $validation->getError('fullname') ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Seller Key</label>
                                <div class="flex items-center gap-2">
                                    <div class="relative group flex-1">
                                        <input type="password" id="seller_key" value="<?= $user->seller_key ?? '' ?>" readonly
                                            class="w-full bg-slate-800/50 border border-white/10 rounded-xl py-3.5 px-4 text-white font-mono text-sm focus:outline-none transition-all duration-300">
                                    </div>
                                    <button type="button" onclick="toggleSellerKey()" class="w-12 h-12 flex items-center justify-center rounded-xl bg-slate-800/80 border border-indigo-500/20 text-slate-400 hover:text-white hover:bg-slate-700 transition-all duration-300 hover:scale-105">
                                        <i class="fas fa-eye-slash" id="keyIcon"></i>
                                    </button>
                                    <button type="button" onclick="copySellerKey()" class="w-12 h-12 flex items-center justify-center rounded-xl bg-slate-800/80 border border-white/5 text-slate-400 hover:text-white hover:bg-slate-700 transition-all duration-300 hover:scale-105">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-2 ml-1"><i class="fas fa-robot text-indigo-400"></i> Send <code class="bg-slate-800 px-1.5 py-0.5 rounded text-indigo-300 font-mono">/start <?= $user->seller_key ?? '' ?></code> to the Telegram bot to securely link your account</p>
                            </div>

                            <div class="p-6 rounded-2xl bg-slate-800/30 border border-white/10 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-400 font-medium">Username</span>
                                    <span class="text-xs text-white font-mono font-bold"><?= $user->username ?? 'N/A' ?></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-400 font-medium">Access Level</span>
                                    <span class="text-[10px] px-3 py-1 rounded-full bg-indigo-500/15 text-indigo-400 border border-indigo-500/25 font-bold uppercase tracking-wider">
                                        <?php 
                                            $level = $user->level ?? 3;
                                            if ($level == 1) echo 'Owner';
                                            elseif ($level == 2) echo 'Admin';
                                            else echo 'Reseller';
                                        ?>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-400 font-medium">Uplink</span>
                                    <span class="text-xs text-slate-400 font-mono"><?= $user->uplink ?? 'None' ?></span>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold hover:shadow-lg hover:shadow-emerald-500/30 transition-all duration-300 text-sm uppercase tracking-widest mt-4 hover:scale-[1.02]">
                                Update Profile
                            </button>
                        </div>
                        <?= form_close() ?>
                    </div>

                </div>

                <div class="mt-12 text-center">
                    <p class="text-slate-500 text-[10px] uppercase font-bold tracking-[0.2em]">SX2 LADOR Security v3.0</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts with modern clipboard API -->
    <script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }
        
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                if(sidebar && overlay) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            }
        });

        function toggleSellerKey() {
            const input = document.getElementById('seller_key');
            const icon = document.getElementById('keyIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }

        function copySellerKey() {
            const input = document.getElementById('seller_key');
            const originalType = input.type;
            
            input.type = 'text';
            const keyValue = input.value;
            
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(keyValue).then(function() {
                    showToast('✓ Seller Key copied to clipboard!', 'success');
                }).catch(function() {
                    fallbackCopy(keyValue);
                });
            } else {
                fallbackCopy(keyValue);
            }
            
            input.type = originalType;
        }
        
        function fallbackCopy(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            showToast('✓ Seller Key copied to clipboard!', 'success');
        }
        
        function showToast(message, type = 'success') {
            const existingToast = document.querySelector('.toast-notification');
            if (existingToast) existingToast.remove();
            
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle text-emerald-400' : 'fa-exclamation-circle text-red-400'} mr-2"></i>${message}`;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast) toast.remove();
            }, 2000);
        }

        $(document).ready(function() {
            setTimeout(function() {
                $('.alert-box').fadeOut(500);
            }, 5000);
        });
    </script>
</body>
</html>