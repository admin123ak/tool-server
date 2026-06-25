<?php
// Core logic handled by Keys controller
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Manager |  VIP TEAM</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- DataTables CSS for Dark Theme -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">

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

        /* CustomScrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(30, 41, 59, 0.5); 
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(139, 92, 246, 0.3); 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(139, 92, 246, 0.5); 
        }

        /* DataTables Customization for Dark Theme */
        .dataTables_wrapper .dataTables_length, 
        .dataTables_wrapper .dataTables_filter, 
        .dataTables_wrapper .dataTables_info, 
        .dataTables_wrapper .dataTables_processing, 
        .dataTables_wrapper .dataTables_paginate {
            color: #94a3b8 !important;
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_filter input {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
        }
        .dataTables_wrapper .dataTables_length select {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 0.375rem;
            padding: 0.25rem 2rem 0.25rem 0.5rem;
        }
        table.dataTable tbody tr {
            background-color: transparent !important;
        }
        table.dataTable tbody td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
            color: #cbd5e1 !important;
        }
        .page-item.active .page-link {
            background-color: #6366f1 !important;
            border-color: #6366f1 !important;
        }
        .page-link {
            background-color: rgba(30, 41, 59, 0.5) !important;
            border-color: rgba(255, 255, 255, 0.05) !important;
            color: #94a3b8 !important;
        }
        
        .blur-keys .key-text {
            filter: blur(5px);
            transition: filter 0.3s;
        }
        .reveal-keys .key-text {
            filter: none;
        }
        /* Mobile Pagination Fix */
        @media (max-width: 768px) {
            .dataTables_paginate {
                display: flex !important;
                justify-content: center !important;
                flex-wrap: wrap !important;
                margin-top: 1rem !important;
                margin-bottom: 2rem !important; /* Ensure space at bottom */
            }
            .dataTables_paginate .page-item {
                display: inline-block !important;
                margin: 2px !important;
            }
            .dataTables_paginate .page-link {
                padding: 0.5rem 0.75rem !important;
                font-size: 0.875rem !important;
                border-radius: 0.375rem !important;
            }
        }
    </style>
</head>
<body class="text-sm" style="background:#03040a"> 
<?= link_tag('assets/css/cyberpunk.css') ?>
<!-- TOP BAR + TABS (cyberpunk) -->
<header class="sys-bar px-6 py-3 border-b" style="border-color:rgba(0,245,255,.3);background:linear-gradient(180deg,rgba(0,8,16,.95),rgba(0,8,16,.6))">
  <div class="flex items-center justify-between gap-6">
    <div class="flex items-center gap-3">
      <div style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#00f5ff,#ff2bd6);clip-path:polygon(20% 0,80% 0,100% 50%,80% 100%,20% 100%,0 50%);box-shadow:0 0 16px #00f5ff"><i class="fas fa-shield-alt text-black"></i></div>
      <div><div style="font:900 16px 'Orbitron',sans-serif;color:#00f5ff">SX2.LADOR</div><div style="font:9px 'Share Tech Mono',monospace;color:#7fb3c2;letter-spacing:.2em">// MAINFRAME.NODE.01</div></div>
    </div>
    <div class="flex items-center gap-3">
      <div class="text-right"><div style="font:9px 'Share Tech Mono',monospace;color:#7fb3c2;letter-spacing:.2em">// CREDIT</div><div style="font:700 13px 'Share Tech Mono',monospace;color:#ffb800">$<?= number_format($user->saldo ?? 0, 2) ?></div></div>
      <a href="<?= site_url('logout') ?>" style="display:flex;align-items:center;justify-content:center;width:40px;height:40px;border:1px solid #ff2bd6;color:#ff2bd6"><i class="fas fa-power-off"></i></a>
    </div>
  </div>
  <nav class="nav-tabs mt-4" style="display:flex;border-bottom:1px solid rgba(0,245,255,.3)">
    <a href="<?= site_url('dashboard') ?>" class="cybtab"><i class="fas fa-chart-pie mr-2"></i>DASH</a>
    <a href="<?= site_url('keys') ?>" class="cybtab on"><i class="fas fa-key mr-2"></i>KEYS</a>
    <a href="<?= site_url('keys/generate') ?>" class="cybtab"><i class="fas fa-bolt mr-2"></i>FORGE</a>
    <?php if (isset($user->level) && $user->level == 1) : ?>
    <a href="<?= site_url('admin/manage-users') ?>" class="cybtab"><i class="fas fa-users mr-2"></i>USERS</a>
    <?php endif; ?>
    <a href="<?= site_url('settings') ?>" class="cybtab "><i class="fas fa-cog mr-2"></i>SYS</a>
  </nav>
</header>
<style>
.cybtab{padding:9px 20px;color:#7fb3c2;text-decoration:none;font:700 11px 'Share Tech Mono',monospace;letter-spacing:.2em;border:1px solid transparent;border-bottom:0;clip-path:polygon(8px 0,100% 0,calc(100% - 8px) 100%,0 100%);margin-right:-6px;background:linear-gradient(180deg,transparent,rgba(0,245,255,.04))}
.cybtab.on{color:#00f5ff;background:linear-gradient(180deg,rgba(0,245,255,.18),rgba(0,245,255,.05));border-color:#00f5ff;text-shadow:0 0 8px rgba(0,245,255,.6)}
.cybtab:hover{color:#fff}
</style>
<main style="padding:16px 24px">


        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            
            <!-- Messages (Flash Data) -->
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
             <?php elseif (session()->getFlashdata('msgWarning')) : ?>
                <div class="bg-amber-500/10 border border-amber-500/20 text-amber-400 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= session()->getFlashdata('msgWarning') ?>
                </div>
            <?php endif; ?>

            <!-- Toolbar -->
            <div class="glass-panel rounded-2xl p-4 mb-6">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <!-- Search & Filters -->
                    <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto flex-1">
                        <div class="relative w-full md:w-64">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" id="searchInput" placeholder="Search key or note..." class="w-full bg-slate-800/50 border border-white/10 rounded-xl py-2 pl-10 pr-4 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
                        </div>
                        
                        <select id="gameFilter" class="bg-slate-800/50 border border-white/10 rounded-xl py-2 px-4 text-white focus:outline-none focus:border-indigo-500 cursor-pointer">
                            <option value="">All Games</option>
                            <?php 
                            $db = \Config\Database::connect();
                            $games = $db->query("SELECT DISTINCT game FROM keys_code ORDER BY game ASC")->getResult();
                            foreach ($games as $game) {
                                echo "<option value=\"{$game->game}\">{$game->game}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 w-full md:w-auto justify-end">
                        <button id="toggleBlurBtn" class="p-2 rounded-xl bg-slate-800/50 border border-white/10 text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                        <button id="refreshBtn" class="p-2 rounded-xl bg-slate-800/50 border border-white/10 text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Admin Actions -->
            <?php if ($user->uplink === 'PROFESSOR') { ?>
                <div class="flex flex-wrap gap-4 mb-6">
                    <button id="addDaysBtn" class="px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium hover:shadow-lg hover:shadow-indigo-500/25 transition-all flex items-center gap-2">
                        <i class="fas fa-calendar-plus"></i> Add Days to Keys
                    </button>
                    <button id="resetAllDevicesBtn" class="px-6 py-3 rounded-xl bg-gradient-to-r from-red-500 to-pink-600 text-white font-medium hover:shadow-lg hover:shadow-red-500/25 transition-all flex items-center gap-2">
                        <i class="fas fa-sync"></i> Reset All Devices
                    </button>
                </div>
            <?php } ?>

            <!-- Table -->
            <div class="glass-panel rounded-2xl p-6">
                <!-- DataTables Scroll Fixes -->
                <style>
                    div.dataTables_wrapper div.dataTables_scrollHead {
                        border: 0 !important;
                        overflow: hidden !important;
                    }
                    div.dataTables_wrapper div.dataTables_scrollBody {
                        border: 0 !important;
                    }
                </style>
                <table id="datatable" class="w-full text-left border-collapse nowrap" style="width:100%">
                    <thead>
                        <tr class="text-xs text-slate-400 uppercase tracking-wider border-b border-white/5">
                            <th class="py-4 px-4 font-bold text-center w-10"></th>
                            <th class="hidden">ID</th> <!-- Hidden ID Column for Sorting -->
                            <th class="py-4 px-4 font-bold">Game</th>
                            <th class="py-4 px-4 font-bold">License Key</th>
                            <th class="py-4 px-4 font-bold text-center">Expired</th>
                            <th class="py-4 px-4 font-bold text-center">Devices</th>
                            <th class="py-4 px-4 font-bold text-center">Duration</th>
                            <th class="py-4 px-4 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this -->
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.0/sweetalert2.all.min.js"></script>

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
            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                order: [[1, "desc"]], // Sort by hidden ID column (Index 1)
                ajax: "<?= site_url('keys/api') ?>",
                scrollX: true,
                scrollCollapse: true,
                dom: 'rtip', 
                columns: [
                    {
                        data: null, // Index 0: Checkbox
                        orderable: false,
                        render: function() {
                            return '<div class="flex justify-center"><input type="checkbox" class="w-4 h-4 rounded border-gray-600 text-indigo-600 focus:ring-indigo-500 bg-slate-700"></div>';
                        }
                    },
                    {
                        data: 'id', // Index 1: Hidden Raw ID
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'game', // Index 2: Game
                        render: function(data, type, row) {
                            return `<div>
                                <div class="font-bold text-white">${row.game}</div>
                                <div class="text-xs text-slate-500 font-mono">ID: ${row.id}</div>
                            </div>`;
                        }
                    },
                    {
                        data: 'user_key',
                        render: function(data, type, row) {
                            return `<div>
                                <div class="font-mono text-emerald-400 font-medium tracking-wide key-text">${row.user_key || '—'}</div>
                                <div class="text-xs text-slate-500 cursor-pointer hover:text-slate-400 transition-colors">+ note</div>
                            </div>`;
                        }
                    },
                    {
                        data: 'expired',
                        render: function(data, type, row) {
                            return `<span class="text-slate-400 font-medium">${row.expired || '(not started yet)'}</span>`;
                        }
                    },
                    {
                        data: 'devices',
                        render: function(data, type, row) {
                            return `<span class="text-slate-400 font-medium">${row.devices || 0}<span class="text-slate-600">/</span>${row.max_devices}</span>`;
                        }
                    },
                    {
                        data: 'duration',
                        render: function(data, type, row) {
                            return `<span class="text-indigo-400 font-medium">${row.duration}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        className: "text-right",
                        render: function(data, type, row) {
                            return `<div class="flex items-center justify-end gap-2">
                                <button onclick="viewKey('${row.user_key}')" class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white transition-all flex items-center justify-center">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="resetUserKey('${row.user_key}')" class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500 hover:text-white transition-all flex items-center justify-center">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <button onclick="editKey('${row.id}')" class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500 hover:text-white transition-all flex items-center justify-center">
                                    <i class="fas fa-user"></i>
                                </button>
                                <button onclick="resetUserKey1('${row.user_key}')" class="w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>`;
                        }
                    }
                ]
            });
            
            // Custom Filters
            $('#gameFilter').change(function() {
                table.column(2).search($(this).val()).draw();
            });
            
            $('#searchInput').on('keyup', function() {
                table.search($(this).val()).draw();
            });
            
            $('#refreshBtn').click(function() {
                table.ajax.reload();
            });

            // Toggle Blur
            $('#toggleBlurBtn').click(function() {
                $('body').toggleClass('blur-keys reveal-keys');
                var icon = $(this).find('i');
                if ($('body').hasClass('reveal-keys')) {
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    $(this).removeClass('text-slate-400').addClass('text-indigo-400 bg-indigo-500/10 border-indigo-500/20');
                } else {
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    $(this).addClass('text-slate-400').removeClass('text-indigo-400 bg-indigo-500/10 border-indigo-500/20');
                }
            });

            $('#resetAllDevicesBtn').click(function() {
                Swal.fire({
                    title: 'Reset Devices?',
                    text: "This will reset all devices for your keys.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#6366f1',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, reset all',
                    background: '#1e293b',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.getJSON("<?= site_url('keys/reset_all_devices') ?>", {}, function(data) {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Reset Complete',
                                    text: 'All devices have been reset.',
                                    icon: 'success',
                                    background: '#1e293b',
                                    color: '#fff'
                                }).then(() => table.ajax.reload());
                            }
                        });
                    }
                });
            });

            $('#addDaysBtn').click(function() {
                Swal.fire({
                    title: 'Add Days',
                    html: `
                        <div class="mb-3 text-left">
                            <label class="block text-sm font-medium text-slate-400 mb-1">Days to add</label>
                            <input type="number" id="swal-days" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white" min="1" value="1">
                        </div>
                        <div class="mb-3 text-left">
                            <label class="block text-sm font-medium text-slate-400 mb-1">Game</label>
                            <select id="swal-game" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white">
                                <option value="ALL">All Games</option>
                                <?php 
                                $db = \Config\Database::connect();
                                $games = $db->query("SELECT DISTINCT game FROM keys_code ORDER BY game ASC")->getResult();
                                foreach ($games as $game) {
                                    echo "<option value=\"{$game->game}\">{$game->game}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonColor: '#6366f1',
                    background: '#1e293b',
                    color: '#fff',
                    preConfirm: () => {
                        const days = document.getElementById('swal-days').value;
                        const game = document.getElementById('swal-game').value;
                        return { days: days, game: game };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                         $.getJSON("<?= site_url('keys/add_days') ?>", result.value, function(data) {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Success',
                                    text: `${data.affected} keys updated.`,
                                    icon: 'success',
                                    background: '#1e293b',
                                    color: '#fff'
                                }).then(() => table.ajax.reload());
                            }
                        });
                    }
                });
            });
        });

        // View Key (Eye Icon)
        window.viewKey = function(key) {
             Swal.fire({
                title: 'License Key',
                text: key,
                background: '#1e293b',
                color: '#fff',
                confirmButtonColor: '#6366f1'
             });
        };

        // Reset Device (Sync Icon)
        window.resetUserKey = function(key) {
            Swal.fire({
                title: 'Reset Device?',
                text: "Reset the device lock for this key?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, reset',
                background: '#1e293b',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.getJSON("<?= site_url('keys/reset') ?>", { userkey: key, reset: 1 }, function(data) {
                        if (data.registered) {
                             Swal.fire({
                                title: 'Success!',
                                text: 'Device reset successfully.',
                                icon: 'success',
                                background: '#1e293b',
                                color: '#fff'
                            }).then(() => $('#datatable').DataTable().ajax.reload());
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to reset device.',
                                icon: 'error',
                                background: '#1e293b',
                                color: '#fff'
                            });
                        }
                    });
                }
            });
        };
        
        window.editKey = function(id) {
            window.location.href = "<?= site_url('keys') ?>/" + id;
        };

        window.resetUserKey1 = function(key) {
            Swal.fire({
                title: 'Delete Key?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', 
                cancelButtonColor: '#6366f1',
                confirmButtonText: 'Yes, delete it',
                background: '#1e293b',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.getJSON("<?= site_url('keys/resetAll') ?>", { userkey: key }, function(data) {
                         Swal.fire({
                            title: 'Deleted!',
                            text: 'Key has been removed.',
                            icon: 'success',
                            background: '#1e293b',
                            color: '#fff'
                         }).then(() => $('#datatable').DataTable().ajax.reload());
                    });
                }
            });
        };

    </script>
</body>
</html>