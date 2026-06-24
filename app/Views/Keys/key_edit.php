<?php
include('conn.php'); 
// Ensure session check
if (!session()->has('userid')) {
    return redirect()->to('login');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit License | VIP TEAM</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['Space Grotesk', 'monospace'],
                    },
                    colors: {
                        glass: {
                            card: 'rgba(30, 41, 59, 0.7)',
                            hover: 'rgba(255, 255, 255, 0.05)',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(236, 72, 153, 0.15) 0px, transparent 50%);
            background-attachment: fixed;
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }
        
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .sidebar-link {
            transition: all 0.2s ease;
        }
        
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-right: 3px solid #8b5cf6;
        }

        input:disabled, select:disabled, textarea:disabled {
            background-color: rgba(15, 23, 42, 0.5) !important;
            color: #64748b !important;
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* Custom Transition for the card */
        .card-enter {
            animation: slideIn 0.4s ease-out;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-sm bg-slate-900"> 

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden glass-overlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 glass-panel transform -translate-x-full transition-transform duration-300 md:translate-x-0 md:static md:flex flex-col justify-between border-r-0">
        <div>
            <!-- Logo -->
            <div class="h-20 flex items-center px-6 border-b border-white/5">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 shadow-lg shadow-indigo-500/20">
                    <i class="fas fa-shield-alt text-white text-sm"></i>
                </div>
                <div>
                    <h1 class="font-bold text-white text-base leading-tight">VIP TEAM</h1>
                    <span class="text-[10px] text-slate-400 font-mono tracking-wider">LICENSE MANAGER</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-3 space-y-1">
                <div class="px-3 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Main</div>
                
                <a href="<?= site_url('dashboard') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300 group">
                    <i class="fas fa-chart-pie w-6 text-center mr-2 text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium">Overview</span>
                </a>
                
                <a href="<?= site_url('keys/generate') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300 group">
                    <i class="fas fa-bolt w-6 text-center mr-2 text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium">Generate Keys</span>
                </a>
                
                <a href="<?= site_url('keys') ?>" class="sidebar-link active flex items-center px-3 py-3 rounded-lg text-slate-300 group">
                    <i class="fas fa-key w-6 text-center mr-2 text-indigo-400 transition-colors"></i>
                    <span class="font-medium">License Manager</span>
                </a>

                <div class="px-3 mt-6 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Configuration</div>
                
                <a href="<?= site_url('settings') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300 group">
                    <i class="fas fa-cog w-6 text-center mr-2 text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium">Settings</span>
                </a>
                
                <?php if ($user->uplink == 'PROFESSOR' || $user->level == 1) : ?>
                <a href="<?= site_url('admin/manage-users') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300 group">
                    <i class="fas fa-users w-6 text-center mr-2 text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium">Manage Users</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>

        <!-- User Profile -->
        <div class="p-4 border-t border-white/5">
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold border border-white/10">
                    <?= substr($user->username, 0, 1) ?>
                </div>
                <div class="overflow-hidden">
                    <h4 class="text-white font-medium truncate"><?= $user->username ?></h4>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span class="text-xs text-slate-400">Online</span>
                    </div>
                </div>
            </div>
            <a href="<?= site_url('logout') ?>" class="flex items-center justify-center w-full py-2.5 px-4 rounded-xl border border-red-500/20 bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-all font-medium text-xs">
                <i class="fas fa-power-off mr-2"></i> LOGOUT SESSION
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden relative">
        <!-- Top Bar -->
        <header class="h-20 flex items-center justify-between px-4 md:px-8 border-b border-white/5 bg-slate-900/50 backdrop-blur-sm z-10">
            <div class="flex items-center gap-3">
                <!-- Hamburger Menu -->
                <button onclick="toggleSidebar()" class="md:hidden p-2 text-white hover:bg-white/10 rounded-lg transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <div class="flex items-center gap-3">
                    <a href="<?= site_url('keys') ?>" class="w-10 h-10 rounded-xl bg-slate-800/50 flex items-center justify-center text-slate-400 hover:text-white transition-colors border border-white/10">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Edit License</h2>
                        <p class="text-slate-400 text-xs mt-1">Key: <span class="text-indigo-400 font-mono"><?= $key->user_key ?></span></p>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="glass-panel px-4 py-2 rounded-xl flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-500">
                        <i class="fas fa-coins text-sm"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Balance</p>
                        <p class="text-white font-mono font-medium">₹<?= number_format($user->saldo, 2) ?></p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            
            <div class="max-w-4xl mx-auto card-enter">
                
                <!-- Page Messages -->
                <?php if (session()->getFlashdata('msgDanger')) : ?>
                    <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session()->getFlashdata('msgDanger') ?>
                    </div>
                <?php elseif (session()->getFlashdata('msgSuccess')) : ?>
                    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <?= session()->getFlashdata('msgSuccess') ?>
                    </div>
                <?php endif; ?>

                <div class="glass-panel rounded-3xl p-6 md:p-8">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 border border-indigo-500/20">
                            <i class="fas fa-id-card text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">License Details</h3>
                            <p class="text-slate-400 text-xs">Modify the specific parameters for this license key</p>
                        </div>
                    </div>

                    <?= form_open('keys/edit') ?>
                    <input type="hidden" name="id_keys" value="<?= $key->id_keys ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <?php if ($user->level == 1 || $user->level == 2) : ?>
                        <!-- Game Selection -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Assigned Game</label>
                            <div class="relative group">
                                <i class="fas fa-gamepad absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-indigo-400 transition-colors"></i>
                                <input type="text" name="game" id="game" value="<?= old('game') ?: $key->game ?>" 
                                    class="w-full bg-slate-800/50 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-white focus:outline-none focus:border-indigo-500 transition-all placeholder-slate-600">
                            </div>
                            <?php if ($validation->hasError('game')) : ?>
                                <p class="text-[11px] text-red-400 ml-1"><?= $validation->getError('game') ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- License Key -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">License String</label>
                            <div class="relative group">
                                <i class="fas fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-indigo-400 transition-colors"></i>
                                <input type="text" name="user_key" id="user_key" value="<?= old('user_key') ?: $key->user_key ?>" 
                                    class="w-full bg-slate-800/50 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-white font-mono tracking-widest focus:outline-none focus:border-indigo-500 transition-all placeholder-slate-600">
                            </div>
                            <?php if ($validation->hasError('user_key')) : ?>
                                <p class="text-[11px] text-red-400 ml-1"><?= $validation->getError('user_key') ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Duration -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Duration <span class="text-[10px] text-slate-600 lowercase">(hours)</span></label>
                            <div class="relative group">
                                <i class="fas fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-indigo-400 transition-colors"></i>
                                <input type="number" name="duration" id="duration" value="<?= old('duration') ?: $key->duration ?>" 
                                    class="w-full bg-slate-800/50 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-white focus:outline-none focus:border-indigo-500 transition-all">
                            </div>
                            <?php if ($validation->hasError('duration')) : ?>
                                <p class="text-[11px] text-red-400 ml-1"><?= $validation->getError('duration') ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Max Devices -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Max Concurrent Devices</label>
                            <div class="relative group">
                                <i class="fas fa-desktop absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-indigo-400 transition-colors"></i>
                                <input type="number" name="max_devices" id="max_devices" value="<?= old('max_devices') ?: $key->max_devices ?>" 
                                    class="w-full bg-slate-800/50 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-white focus:outline-none focus:border-indigo-500 transition-all">
                            </div>
                            <?php if ($validation->hasError('max_devices')) : ?>
                                <p class="text-[11px] text-red-400 ml-1"><?= $validation->getError('max_devices') ?></p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Status -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Access Status</label>
                            <div class="relative group">
                                <i class="fas fa-shield-halved absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-indigo-400 transition-colors"></i>
                                <select name="status" id="status" class="w-full bg-slate-800/50 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-white appearance-none focus:outline-none focus:border-indigo-500 transition-all cursor-pointer">
                                    <option value="1" <?= $key->status == 1 ? 'selected' : '' ?>>Active (Enabled)</option>
                                    <option value="0" <?= $key->status == 0 ? 'selected' : '' ?>>Banned (Blocked)</option>
                                </select>
                                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none text-xs"></i>
                            </div>
                            <?php if ($validation->hasError('status')) : ?>
                                <p class="text-[11px] text-red-400 ml-1"><?= $validation->getError('status') ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Registrator -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Registrator (Owner)</label>
                            <div class="relative group">
                                <i class="fas fa-user-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-indigo-400 transition-colors"></i>
                                <input type="text" name="registrator" id="registrator" value="<?= old('registrator') ?: $key->registrator ?>" 
                                    class="w-full bg-slate-800/50 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-white focus:outline-none focus:border-indigo-500 transition-all">
                            </div>
                            <?php if ($validation->hasError('registrator')) : ?>
                                <p class="text-[11px] text-red-400 ml-1"><?= $validation->getError('registrator') ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Expired Date -->
                        <div class="col-span-1 md:col-span-2 space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Expiry Timestamp <?= !$key->expired_date ? '<span class="text-emerald-400 lowercase italic">(Not started yet)</span>' : ''  ?></label>
                            <div class="relative group">
                                <i class="fas fa-calendar-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-indigo-400 transition-colors"></i>
                                <input type="text" name="expired_date" id="expired_date" value="<?= old('expired_date') ?: $key->expired_date ?>" 
                                    class="w-full bg-slate-800/50 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-white focus:outline-none focus:border-indigo-500 transition-all font-mono">
                            </div>
                            <?php if ($validation->hasError('expired_date')) : ?>
                                <p class="text-[11px] text-red-400 ml-1"><?= $validation->getError('expired_date') ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Device List -->
                        <div class="col-span-1 md:col-span-2 space-y-2">
                            <div class="flex items-center justify-between ml-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Registered Hardware (HWIDs)</label>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-slate-800 border border-white/5 text-slate-300 maxDev">
                                    <?= $key_info->total ?>/<?= $key->max_devices ?>
                                </span>
                            </div>
                            <div class="relative group">
                                <i class="fas fa-fingerprint absolute left-4 top-6 text-slate-500 group-focus-within:text-indigo-400 transition-colors"></i>
                                <textarea name="devices" id="devices" rows="<?= max(3, $key_info->total) ?>" 
                                    class="w-full bg-slate-800/50 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-white focus:outline-none focus:border-indigo-500 transition-all font-mono text-xs leading-relaxed"><?= old('devices') ?: ($key_info->total ? $key_info->devices : '') ?></textarea>
                            </div>
                            <p class="text-[10px] text-slate-500 ml-1 italic">* Enter each device HWID on a new line</p>
                            <?php if ($validation->hasError('devices')) : ?>
                                <p class="text-[11px] text-red-400 ml-1"><?= $validation->getError('devices') ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Actions -->
                        <div class="col-span-1 md:col-span-2 pt-4 flex gap-4">
                            <button type="submit" class="btnUpdate flex-1 py-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold hover:shadow-lg hover:shadow-indigo-500/25 transition-all text-sm uppercase tracking-widest disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                Save Changes
                            </button>
                            <a href="<?= site_url('keys') ?>" class="py-4 px-8 rounded-2xl bg-slate-800/50 border border-white/10 text-slate-400 font-bold hover:text-white hover:bg-slate-700/50 transition-all text-sm uppercase tracking-widest">
                                Cancel
                            </a>
                        </div>
                    </div>
                    <?= form_close() ?>
                </div>

                <!-- Footer Hint -->
                <div class="mt-8 text-center">
                    <p class="text-slate-500 text-[10px] uppercase font-bold tracking-[0.2em]">Power Cheats Security System v2.0</p>
                </div>
            </div>

        </div>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        $(document).ready(function() {
            var level = "<?= $user->level ?>";
            // Disable fields for non-admins if necessary
            if (level != 1 && level != 2) {
                $("#registrator, #expired_date, #devices").attr('disabled', true);
            }

            // Enable submit button only on change
            $("input, select, textarea").on('input change', function() {
                $(".btnUpdate").attr('disabled', false).removeClass('opacity-50');
            });

            // Dynamic max devices update
            var total = "<?= $key_info->total ?>";
            $("#max_devices").on('input change', function() {
                $(".maxDev").html(total + '/' + $(this).val());
                // Adjust textarea rows dynamically if needed
                var newMax = parseInt($(this).val());
                if (newMax > 3) {
                    $("#devices").attr('rows', Math.min(newMax, 10));
                }
            });

            // Auto-hide alerts
            setTimeout(function() {
                $('.bg-emerald-500\\/10, .bg-red-500\\/10').fadeOut('slow');
            }, 5000);
        });
    </script>
</body>
</html>