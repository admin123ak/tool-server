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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | SX2 LADOR</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

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

        /* Custom DataTables Styling */
        .dataTables_wrapper .dataTables_length, 
        .dataTables_wrapper .dataTables_filter, 
        .dataTables_wrapper .dataTables_info, 
        .dataTables_wrapper .dataTables_processing, 
        .dataTables_wrapper .dataTables_paginate {
            color: #94a3b8 !important;
            font-size: 12px;
            margin-bottom: 1rem;
        }

        table.dataTable {
            border-collapse: separate !important;
            border-spacing: 0 8px !important;
            border: none !important;
            background: transparent !important;
        }
        
        table.dataTable thead th {
            background: rgba(15, 23, 42, 0.5) !important;
            color: #64748b !important;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.1em;
            font-weight: 700;
            padding: 12px 16px !important;
            border: none !important;
        }

        table.dataTable tbody tr {
            background: rgba(30, 41, 59, 0.5) !important;
            transition: transform 0.2s, background 0.2s;
            cursor: pointer;
        }

        table.dataTable tbody tr:hover {
            background: rgba(255, 255, 255, 0.05) !important;
            transform: translateY(-2px);
        }

        table.dataTable tbody td {
            padding: 16px !important;
            border: none !important;
            font-size: 13px;
            color: #cbd5e1;
        }

        .dataTables_filter input {
            background: rgba(15, 23, 42, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 8px !important;
            color: white !important;
            padding: 6px 12px !important;
        }

        .dataTables_paginate .paginate_button {
            border-radius: 8px !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #94a3b8 !important;
            margin: 0 2px !important;
        }

        .dataTables_paginate .paginate_button.current {
            background: #6366f1 !important;
            border-color: #6366f1 !important;
            color: white !important;
        }

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
                    <h1 class="font-bold text-white text-base leading-tight">SX2 LADOR</h1>
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
                
                <a href="<?= site_url('keys') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300 group">
                    <i class="fas fa-key w-6 text-center mr-2 text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium">License Manager</span>
                </a>

                <div class="px-3 mt-6 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Configuration</div>
                
                <a href="<?= site_url('settings') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300 group">
                    <i class="fas fa-cog w-6 text-center mr-2 text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium">Settings</span>
                </a>
                
                <a href="<?= site_url('admin/manage-users') ?>" class="sidebar-link active flex items-center px-3 py-3 rounded-lg text-slate-300 group">
                    <i class="fas fa-users w-6 text-center mr-2 text-indigo-400 transition-colors"></i>
                    <span class="font-medium">Manage Users</span>
                </a>
                
                <a href="<?= site_url('admin/create-referral') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300 group">
                    <i class="fas fa-user-plus w-6 text-center mr-2 text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium">Create User</span>
                </a>
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
                <button onclick="toggleSidebar()" class="md:hidden p-2 text-white hover:bg-white/10 rounded-lg transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-2xl font-bold text-white">Manage Users</h2>
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
            <div class="max-w-7xl mx-auto card-enter">
                
                <!-- Info Alert -->
                <div class="bg-indigo-500/10 border border-indigo-500/20 text-slate-300 px-4 py-3 rounded-2xl mb-8 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                        <i class="fas fa-info-circle text-xs"></i>
                    </div>
                    <p class="text-xs font-medium tracking-wide">Search users by username, fullname, saldo, or uplink reference.</p>
                </div>

                <div class="glass-panel rounded-3xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between">
                        <h3 class="font-bold text-white flex items-center gap-2">
                            <i class="fas fa-list text-indigo-400"></i>
                            User Directory
                        </h3>
                    </div>

                    <div class="p-6 overflow-x-auto">
                        <table id="userTable" class="w-full text-left">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User Information</th>
                                    <th>Access Level</th>
                                    <th>Point Balance</th>
                                    <th>Status</th>
                                    <th>Uplink</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($user_list as $u) : ?>
                                <tr>
                                    <td class="font-mono text-[10px] text-slate-500">#<?= $u->id_users ?></td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-indigo-400 font-bold border border-white/5">
                                                <?= strtoupper(substr($u->username, 0, 1)) ?>
                                            </div>
                                            <div>
                                                <p class="font-bold text-white truncate max-w-[120px]"><?= $u->username ?></p>
                                                <p class="text-[10px] text-slate-500 font-mono truncate max-w-[120px]"><?= $u->expiration_date ?: 'Permanent' ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($u->level == 1) : ?>
                                            <span class="px-2 py-1 rounded-md bg-purple-500/10 text-purple-400 border border-purple-500/20 text-[10px] font-bold uppercase tracking-wider">Owner</span>
                                        <?php elseif($u->level == 2) : ?>
                                            <span class="px-2 py-1 rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-[10px] font-bold uppercase tracking-wider">Admin</span>
                                        <?php else : ?>
                                            <span class="px-2 py-1 rounded-md bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[10px] font-bold uppercase tracking-wider">Reseller</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-coins text-amber-500/50 text-xs"></i>
                                            <span class="font-mono font-bold <?= $u->level == 1 ? 'text-slate-500 italic' : 'text-white' ?>">
                                                <?= $u->level == 1 ? 'Infinite' : number_format($u->saldo, 2) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($u->status == 1) : ?>
                                            <div class="flex items-center gap-2 text-emerald-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                                <span class="text-[11px] font-bold uppercase tracking-wider">Active</span>
                                            </div>
                                        <?php elseif($u->status == 2) : ?>
                                            <div class="flex items-center gap-2 text-red-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                                <span class="text-[11px] font-bold uppercase tracking-wider">Banned</span>
                                            </div>
                                        <?php else : ?>
                                            <div class="flex items-center gap-2 text-slate-500">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                                <span class="text-[11px] font-bold uppercase tracking-wider">Expired</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="text-[11px] text-slate-400 italic"><?= $u->uplink ?: 'None' ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('admin/user/'.$u->id_users) ?>" class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-white transition-all flex items-center justify-center">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-8 text-center px-4">
                    <p class="text-slate-500 text-[10px] uppercase font-bold tracking-[0.2em]">Quantum Integrated User Management System v3.1</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

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
            $('#userTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "info": true,
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search directory...",
                    "paginate": {
                        "previous": "<i class='fas fa-chevron-left text-[10px]'></i>",
                        "next": "<i class='fas fa-chevron-right text-[10px]'></i>"
                    }
                },
                "dom": '<"flex flex-wrap items-center justify-between mb-4"lf>rt<"flex flex-wrap items-center justify-between mt-4"ip>'
            });
        });
    </script>
</body>
</html>