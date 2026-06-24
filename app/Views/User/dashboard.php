<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Dashboard | SX2 LADOR — Premium License Manager</title>
    
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
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .sidebar-link {
            transition: all 0.2s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }
        
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(99, 102, 241, 0.2);
            color: white;
            border-right: 3px solid #8b5cf6;
        }

        .stat-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px -8px rgba(0, 0, 0, 0.3);
        }
        
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(30, 41, 59, 0.5); 
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(139, 92, 246, 0.4); 
            border-radius: 10px;
        }
        
        /* Dropdown menu for 3-dot button */
        .reseller-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 0.5rem;
            background: rgba(15, 23, 42, 0.98);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1rem;
            padding: 0.5rem;
            min-width: 170px;
            z-index: 100;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
        }
        
        .reseller-dropdown a, .reseller-dropdown button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s;
            width: 100%;
            text-align: left;
            cursor: pointer;
            color: #cbd5e1;
        }
        
        .reseller-dropdown a:hover, .reseller-dropdown button:hover {
            background: rgba(99, 102, 241, 0.25);
            color: white;
        }
        
        .dropdown-btn {
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .dropdown-btn:hover {
            background: rgba(99, 102, 241, 0.3);
            transform: scale(1.05);
        }
        
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeSlideUp 0.35s ease-out forwards;
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
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-shield-alt text-white text-base"></i>
                </div>
                <div>
                    <h1 class="font-bold text-white text-lg tracking-tight">SX2 LADOR</h1>
                    <span class="text-[10px] text-slate-400 font-mono">LICENSE MANAGER</span>
                </div>
            </div>
            <nav class="mt-6 px-3 space-y-1">
                <div class="px-3 mb-2 text-[10px] font-bold text-slate-500 uppercase">Main</div>
                <a href="<?= site_url('dashboard') ?>" class="sidebar-link active flex items-center px-3 py-3 rounded-lg text-slate-300"><i class="fas fa-chart-pie w-6 mr-2 text-indigo-400"></i><span>Overview</span></a>
                <a href="<?= site_url('keys/generate') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300"><i class="fas fa-bolt w-6 mr-2"></i><span>Generate Keys</span></a>
                <a href="<?= site_url('keys') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300"><i class="fas fa-key w-6 mr-2"></i><span>License Manager</span></a>
                <div class="px-3 mt-6 mb-2 text-[10px] font-bold text-slate-500 uppercase">Configuration</div>
                <a href="<?= site_url('settings') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300"><i class="fas fa-cog w-6 mr-2"></i><span>Settings</span></a>
                <?php if (isset($user->level) && $user->level == 1) : ?>
                <a href="<?= site_url('admin/manage-users') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300"><i class="fas fa-users w-6 mr-2"></i><span>Manage Users</span></a>
                <a href="<?= site_url('admin/create-referral') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300"><i class="fas fa-user-plus w-6 mr-2"></i><span>CREATE NEW USER</span></a>
                <?php endif; ?>
            </nav>
        </div>
        <div class="p-4 border-t border-white/10 mt-auto">
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-700 to-slate-800 flex items-center justify-center text-white font-bold border border-white/15"><?= isset($user->username) ? strtoupper(substr($user->username, 0, 1)) : 'U' ?></div>
                <div><h4 class="text-white font-semibold"><?= $user->username ?? 'User' ?></h4><div class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span><span class="text-xs text-slate-300"><?= $role_label ?? (isset($user->level) ? getLevel($user->level) : 'Member') ?></span></div></div>
            </div>
            <a href="<?= site_url('logout') ?>" class="flex items-center justify-center w-full py-2.5 rounded-xl border border-red-500/30 bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-all text-xs gap-2"><i class="fas fa-power-off"></i> LOGOUT</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="h-20 flex items-center justify-between px-4 md:px-8 border-b border-white/10 bg-slate-900/40 backdrop-blur-md">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden p-2.5 text-white hover:bg-white/10 rounded-xl"><i class="fas fa-bars text-xl"></i></button>
                <div class="hidden md:block"><h2 class="text-2xl font-extrabold bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">Overview</h2><p class="text-slate-400 text-xs">Welcome back, <span class="text-indigo-400 font-semibold"><?= $user->username ?? 'User' ?></span></p></div>
            </div>
            <div class="glass-panel px-5 py-2 rounded-xl flex items-center gap-3"><div class="w-9 h-9 rounded-xl bg-amber-500/15 flex items-center justify-center text-amber-400"><i class="fas fa-coins"></i></div><div><p class="text-[9px] text-slate-400 uppercase font-black">Balance</p><p class="text-white font-mono font-bold">$<?= isset($user->saldo) ? number_format($user->saldo, 2) : '0.00' ?></p></div></div>
        </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('msgDanger')) : ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-300 px-5 py-3.5 rounded-xl mb-6 text-sm flex items-center gap-3 animate-fade-in"><i class="fas fa-exclamation-triangle"></i><?= session()->getFlashdata('msgDanger') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('msgSuccess')) : ?>
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 px-5 py-3.5 rounded-xl mb-6 text-sm flex items-center gap-3 animate-fade-in"><i class="fas fa-check-circle"></i><?= session()->getFlashdata('msgSuccess') ?></div>
            <?php endif; ?>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="glass-panel p-6 rounded-2xl stat-card"><p class="text-xs text-slate-400 uppercase font-bold">Total Licenses</p><h3 class="text-3xl font-bold text-white my-4"><?= $stats['total_keys'] ?? 0 ?></h3><div class="text-xs text-slate-400"><i class="fas fa-database mr-2"></i>All time</div></div>
                <div class="glass-panel p-6 rounded-2xl stat-card"><p class="text-xs text-slate-400 uppercase font-bold">Active Keys</p><h3 class="text-3xl font-bold text-white my-4"><?= $stats['active_keys'] ?? 0 ?></h3><div class="text-xs text-emerald-400"><i class="fas fa-check-circle mr-2"></i>Running</div></div>
                <div class="glass-panel p-6 rounded-2xl stat-card"><p class="text-xs text-slate-400 uppercase font-bold">Unused Stock</p><h3 class="text-3xl font-bold text-white my-4"><?= $stats['unused_keys'] ?? 0 ?></h3><div class="text-xs text-blue-400"><i class="fas fa-archive mr-2"></i>Ready</div></div>
                <div class="p-6 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-700 cursor-pointer hover:shadow-xl transition" onclick="window.location.href='<?= site_url('keys/generate') ?>'"><div class="flex flex-col"><div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center mb-3"><i class="fas fa-bolt text-white"></i></div><h3 class="text-xl font-bold text-white">Generate Keys</h3><p class="text-indigo-100 text-xs">Create licenses</p></div></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Top Performance with working 3-dot menu for Resellers - FIXED: Proper foreach with id check -->
                    <div class="glass-panel rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-6"><div class="flex items-center gap-3"><i class="fas fa-crown text-amber-400"></i><h3 class="font-bold text-white">Top Performance / Resellers</h3></div></div>
                        <div class="space-y-4">
                            <?php if (!empty($resellers) && is_array($resellers)) : ?>
                                <?php foreach ($resellers as $idx => $res) : ?>
                                    <?php if (!isset($res->id)) continue; // Skip if no ID ?>
                                    <div class="flex items-center justify-between p-4 rounded-xl bg-slate-800/30 border border-white/10 hover:bg-slate-800/50 transition-all group relative" id="reseller-row-<?= $res->id ?>">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-slate-700 to-slate-800 flex items-center justify-center text-white font-bold text-lg border border-slate-600">
                                                <?= isset($res->username) ? strtoupper(substr($res->username, 0, 1)) : 'U' ?>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h4 class="text-white font-bold"><?= $res->username ?? 'Unknown' ?></h4>
                                                    <?php if (isset($res->level) && $res->level == 1): ?>
                                                        <span class="px-1.5 py-0.5 rounded bg-blue-500/20 text-[9px] text-blue-400 font-bold"><i class="fas fa-check-circle"></i> OWNER</span>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="text-[10px] text-slate-400 uppercase font-black bg-slate-700/50 px-2 py-0.5 rounded mt-1 inline-block">
                                                    <?php 
                                                        $levelName = 'Reseller';
                                                        if (isset($res->level)) {
                                                            if ($res->level == 1) $levelName = 'Owner';
                                                            elseif ($res->level == 2) $levelName = 'Admin';
                                                            else $levelName = 'Reseller';
                                                        }
                                                        echo $levelName;
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <div class="text-right">
                                                <p class="text-xl font-black text-white"><?= $res->managed_keys ?? 0 ?></p>
                                                <p class="text-[10px] text-slate-500 uppercase font-bold">Keys</p>
                                            </div>
                                            <!-- 3-Dot Button with Dropdown -->
                                            <div class="relative">
                                                <button class="dropdown-btn w-8 h-8 rounded-lg bg-slate-700/50 hover:bg-indigo-500/30 flex items-center justify-center transition-all" data-dropdown-id="dropdown-<?= $res->id ?>">
                                                    <i class="fas fa-ellipsis-v text-slate-300 text-sm"></i>
                                                </button>
                                                <div id="dropdown-<?= $res->id ?>" class="reseller-dropdown hidden">
                                                    <a href="<?= site_url('admin/edit-user/'.$res->id) ?>"><i class="fas fa-edit text-blue-400"></i> Edit Profile</a>
                                                    <a href="<?= site_url('admin/reset-password/'.$res->id) ?>"><i class="fas fa-key text-amber-400"></i> Reset Password</a>
                                                    <a href="<?= site_url('admin/manage-keys/'.$res->id) ?>"><i class="fas fa-key text-indigo-400"></i> View Keys</a>
                                                    <button onclick="confirmDelete(<?= $res->id ?>)" class="text-red-400 w-full"><i class="fas fa-trash-alt"></i> Delete User</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="text-center py-8 text-slate-400">No resellers found.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Activity Table -->
                    <div class="glass-panel rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-6"><i class="fas fa-history text-indigo-400"></i><h3 class="font-bold text-white">Recent Activity</h3></div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-xs text-slate-400 border-b border-white/10">
                                        <th class="py-3 px-2">Action</th>
                                        <th class="py-3 px-2">Key</th>
                                        <th class="py-3 px-2 text-right">Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($history) && is_array($history)): ?>
                                    <?php foreach ($history as $idx => $h) : 
                                        $in = isset($h->info) ? explode("|", $h->info) : ['Unknown', '---'];
                                        $action = $in[0] ?? 'Unknown';
                                        $key = $in[1] ?? '---';
                                    ?>
                                        <tr class="border-b border-white/10 hover:bg-white/5 transition-colors <?= ($idx === count($history)-1) ? 'border-b-0' : '' ?>">
                                            <td class="py-3 px-2 text-white text-xs"><?= $action ?></td>
                                            <td class="py-3 px-2 font-mono text-indigo-400 text-[10px]"><?= $key ?></td>
                                            <td class="py-3 px-2 text-right text-slate-500 text-[10px]">
                                                <?php 
                                                    if (isset($h->created_at) && class_exists('CodeIgniter\I18n\Time')) {
                                                        echo \CodeIgniter\I18n\Time::parse($h->created_at)->humanize();
                                                    } else {
                                                        echo $h->created_at ?? 'Just now';
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="py-8 text-center text-slate-500">No recent activity</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Quick Access & Account -->
                <div class="space-y-6">
                    <div class="glass-panel rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-6"><i class="fas fa-rocket text-indigo-400"></i><h3 class="font-bold uppercase text-xs">Quick Access</h3></div>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="<?= site_url('keys/generate') ?>" class="p-5 rounded-2xl bg-slate-800/30 border border-white/10 hover:bg-indigo-500/10 text-center group transition-all"><i class="fas fa-plus text-indigo-400 text-xl mb-2 block group-hover:scale-110 transition"></i><span class="text-[10px] font-black uppercase">Generate</span></a>
                            <a href="<?= site_url('keys') ?>" class="p-5 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 hover:bg-indigo-500/20 text-center transition-all"><i class="fas fa-key text-indigo-400 text-xl mb-2 block"></i><span class="text-[10px] font-black uppercase">Licenses</span></a>
                        </div>
                        <div class="mt-4"><a href="<?= site_url('settings') ?>" class="flex items-center justify-center gap-3 p-4 rounded-2xl bg-slate-800/30 border border-white/10 hover:bg-slate-700/50 transition-all"><i class="fas fa-cog text-slate-400"></i><span class="text-[10px] font-black uppercase">Settings</span></a></div>
                    </div>
                    <div class="glass-panel rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-6"><i class="fas fa-user-shield text-indigo-400"></i><h3 class="font-bold uppercase text-xs">Account Info</h3></div>
                        <div class="space-y-4">
                            <div class="flex justify-between"><span class="text-xs text-slate-400">Username</span><span class="text-sm text-white font-bold"><?= $user->username ?? 'User' ?></span></div>
                            <div class="flex justify-between"><span class="text-xs text-slate-400">Role</span><span class="px-2 py-1 rounded bg-indigo-500/20 text-[10px] font-black uppercase"><?= $role_label ?? (isset($user->level) ? getLevel($user->level) : 'Member') ?></span></div>
                            <div class="flex justify-between"><span class="text-xs text-slate-400">Expiration</span><span class="text-xs text-slate-300 font-mono"><?= isset($user->expiration_date) ? date("d M Y", strtotime($user->expiration_date)) : 'Unlimited' ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript: Sidebar + Working 3-dot dropdown menus -->
    <script>
        // Toggle sidebar mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                if(overlay) overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                if(overlay) overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }
        
        // Close sidebar on resize
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
        
        // Handle all 3-dot dropdown buttons - FIXED: proper event handling
        document.addEventListener('click', function(event) {
            const allDropdowns = document.querySelectorAll('.reseller-dropdown');
            const dropdownButton = event.target.closest('.dropdown-btn');
            
            if (!dropdownButton) {
                // Close all dropdowns if click outside
                allDropdowns.forEach(drop => {
                    drop.classList.add('hidden');
                });
                return;
            }
            
            // If button clicked, toggle its dropdown
            event.stopPropagation();
            const dropdownId = dropdownButton.getAttribute('data-dropdown-id');
            const targetDropdown = document.getElementById(dropdownId);
            
            if (targetDropdown) {
                // Close all other dropdowns first
                allDropdowns.forEach(drop => {
                    if (drop.id !== dropdownId) drop.classList.add('hidden');
                });
                // Toggle current
                targetDropdown.classList.toggle('hidden');
            }
        });
        
        // Prevent dropdown menu clicks from closing immediately
        document.querySelectorAll('.reseller-dropdown').forEach(drop => {
            drop.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
        
        // Delete confirmation function
        function confirmDelete(userId) {
            if (confirm('⚠️ Are you sure you want to DELETE this user? This action is permanent and cannot be undone.')) {
                window.location.href = '<?= site_url('admin/delete-user/') ?>' + userId;
            }
        }
        
        console.log("SX2 LADOR Dashboard - Fully loaded with working 3-dot menus");
    </script>
</body>
</html>