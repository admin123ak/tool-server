<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Keys | SX2 LADOR</title>
    
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
        
        /* Form Elements */
        .form-input {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.2s;
        }
        .form-input:focus {
            border-color: #8b5cf6;
            outline: none;
            box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2);
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
                    <h1 class="font-bold text-white text-base leading-tight"> SX2 LADOR</h1>
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
                
                <a href="<?= site_url('keys/generate') ?>" class="sidebar-link active flex items-center px-3 py-3 rounded-lg text-slate-300 group">
                    <i class="fas fa-bolt w-6 text-center mr-2 text-indigo-400"></i>
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
                
                <?php if ($user->level == 1) : ?>
                <a href="<?= site_url('admin/manage-users') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300 group">
                    <i class="fas fa-users w-6 text-center mr-2 text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium">Manage Users</span>
                </a>
                <a href="<?= site_url('admin/create-referral') ?>" class="sidebar-link flex items-center px-3 py-3 rounded-lg text-slate-300 group">
                    <i class="fas fa-user-plus w-6 text-center mr-2 text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium">CREATE NEW USER</span>
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
                        <span class="text-xs text-slate-400"><?= getLevel($user->level) ?></span>
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
                
                <div class="hidden md:block">
                    <h2 class="text-2xl font-bold text-white">Generate Keys</h2>
                    <p class="text-slate-400 text-xs mt-1">Create new licenses for your users</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="glass-panel px-4 py-2 rounded-xl flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-500">
                        <i class="fas fa-coins text-sm"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Balance</p>
                        <p class="text-white font-mono font-medium">$<?= number_format($user->saldo, 2) ?></p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            
            <!-- Error/Warning Messages -->
            <?php if (session()->getFlashdata('msgDanger')) : ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2 animate-in fade-in slide-in-from-top-4 duration-300">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= session()->getFlashdata('msgDanger') ?>
                </div>
            <?php elseif (session()->getFlashdata('msgWarning')) : ?>
                <div class="bg-amber-500/10 border border-amber-500/20 text-amber-400 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2 animate-in fade-in slide-in-from-top-4 duration-300">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= session()->getFlashdata('msgWarning') ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Generator Form -->
                <div class="lg:col-span-2 space-y-6">
                    <?= form_open() ?>
                    
                    <!-- 1. Select Game -->
                    <div class="glass-panel p-6 rounded-2xl">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-sm">1</div>
                            <h3 class="font-bold text-white text-lg">SELECT GAME</h3>
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wide">Game Type</label>
                            <?= form_dropdown(['class' => 'w-full form-input rounded-xl px-4 py-3 text-sm focus:border-indigo-500', 'name' => 'game', 'id' => 'game'], $game, old('game') ?: '') ?>
                             <?php if ($validation->hasError('game')) : ?>
                                <p class="text-red-400 text-xs mt-1"><?= $validation->getError('game') ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 2. Configure Key -->
                    <div class="glass-panel p-6 rounded-2xl">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-sm">2</div>
                            <h3 class="font-bold text-white text-lg">CONFIGURE KEY</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Duration -->
                            <div>
                                <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wide">Duration</label>
                                <?= form_dropdown(['class' => 'w-full form-input rounded-xl px-4 py-3 text-sm focus:border-indigo-500', 'name' => 'duration', 'id' => 'duration'], $duration, old('duration') ?: '') ?>
                            </div>
                            
                            <!-- Prefix -->
                            <div>
                                <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wide">Prefix</label>
                                <input type="text" name="prefix" class="w-full form-input rounded-xl px-4 py-3 text-sm focus:border-indigo-500" placeholder="KEY" value="KEY">
                            </div>

                            <!-- Max Devices -->
                            <div>
                                <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wide">Max Devices</label>
                                <input type="number" name="max_devices" id="max_devices" class="w-full form-input rounded-xl px-4 py-3 text-sm focus:border-indigo-500" placeholder="1" value="1" min="1">
                            </div>
                        </div>

                        <!-- Custom Key -->
                        <div class="mb-2">
                             <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wide">License Key</label>
                             <div class="relative">
                                 <input type="text" name="cuslicense" id="cuslicense" class="w-full form-input rounded-xl px-4 py-3 text-sm focus:border-indigo-500 font-mono text-indigo-300" placeholder="Enter License Key" value="<?= old('cuslicense') ?>" required>
                                 <button type="button" id="randomKeyBtn" class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 rounded-lg bg-indigo-500/20 hover:bg-indigo-500/40 text-indigo-300 text-xs font-medium transition-all flex items-center gap-1.5 border border-indigo-500/30">
                                     <i class="fas fa-dice-d6"></i> Random
                                 </button>
                             </div>
                             <?php if ($validation->hasError('cuslicense')) : ?>
                                <p class="text-red-400 text-xs mt-1"><?= $validation->getError('cuslicense') ?></p>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- 3. Action -->
                    <div class="glass-panel p-8 rounded-2xl text-center relative overflow-hidden">
                        <div class="relative z-10">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Total Cost</p>
                            <h2 class="text-4xl font-bold text-white mb-6">$<span id="totalCost">0.00</span></h2>
                            
                            <button type="submit" class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl text-white font-bold shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:scale-[1.02] transition-all">
                                <i class="fas fa-bolt mr-2"></i> Generate Key
                            </button>
                        </div>
                        <!-- Background Glow -->
                         <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 bg-indigo-500/20 rounded-full blur-3xl z-0"></div>
                    </div>

                    <?= form_close() ?>
                </div>

                <!-- Sidebar / Pricing / Results -->
                <div class="space-y-6">
                    <!-- Success Box In Sidebar (Matches Screenshot) -->
                    <?php if (session()->getFlashdata('generated_keys')) : ?>
                        <div class="glass-panel rounded-2xl overflow-hidden mb-6 border-emerald-500/20 bg-emerald-500/5 animate-in zoom-in duration-300">
                            <div class="p-4 bg-emerald-500/10 border-b border-emerald-500/20 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest bg-emerald-500/20 px-2 py-0.5 rounded">Generated!</span>
                                </div>
                                <button onclick="copyBulk(this)" class="px-3 py-1 rounded-lg bg-emerald-500 text-white text-[10px] font-bold hover:bg-emerald-600 transition-all flex items-center gap-1.5 shadow-lg shadow-emerald-500/20">
                                    <i class="fas fa-copy"></i> Copy All
                                </button>
                            </div>
                            <div class="p-4 space-y-2 max-h-[300px] overflow-y-auto custom-scrollbar">
                                <?php foreach(session()->getFlashdata('generated_keys') as $key): ?>
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/50 border border-white/5 group hover:border-indigo-500/30 transition-all">
                                        <code class="text-indigo-400 font-mono text-xs tracking-wider"><?= $key ?></code>
                                        <button onclick="copySingle('<?= $key ?>', this)" class="text-slate-600 hover:text-indigo-400 transition-colors">
                                            <i class="fas fa-copy text-xs"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <textarea id="bulkKeys" class="hidden"><?= implode("\n", session()->getFlashdata('generated_keys')) ?></textarea>
                        </div>
                    <?php endif; ?>

                    <div class="glass-panel p-6 rounded-2xl">
                        <h3 class="font-bold text-white mb-4">Pricing</h3>
                        <div class="divide-y divide-white/5">
                            <?php 
                                $priceData = is_string($price) ? json_decode($price, true) : $price;
                                if($priceData):
                                    foreach($priceData as $dur => $cost): 
                            ?>
                                <div class="flex items-center justify-between py-3">
                                    <span class="text-slate-400 text-sm"><?= $dur ?> Hours</span>
                                    <span class="text-white font-mono font-medium">$<?= $cost ?>/device</span>
                                </div>
                            <?php 
                                    endforeach; 
                                endif; 
                            ?>
                        </div>
                         <?php 
                             /* 
                             // HoursToDays helper definition if not available in view context
                             if (!function_exists('HoursToDays')) {
                                function HoursToDays($value) {
                                    if($value == 1) return "$value Hour";
                                    else if($value >= 2 && $value < 24) return "$value Hours";
                                    else if($value == 24) return ($value / 24) . " Day";
                                    else if($value > 24) return ($value / 24) . " Days";
                                    return $value;
                                }
                             }
                             */
                         ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

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

        // Close sidebar when clicking on overlay
        document.getElementById('sidebarOverlay')?.addEventListener('click', function() {
            document.getElementById('sidebar')?.classList.add('-translate-x-full');
            this.classList.add('hidden');
        });

        // Function to generate random license key
        function generateRandomKey() {
            // Pattern: SX2 + 4 random uppercase letters + OIX + 4 random digits (like SX2XXXXOIX9383)
            // Or completely random 16-char alphanumeric uppercase
            const prefixes = ['SX2', 'LIC', 'KEY', 'PRM', 'VIP', 'GOLD', 'SILVER', 'BRONZE'];
            const suffixes = ['OIX', 'X9', 'LTD', 'PRO', 'MAX', 'ULTRA'];
            
            // Generate a random key in format like SX2-A7B3-OIX-9382 or similar style
            const randomUpper = (len) => {
                let result = '';
                const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
                for(let i = 0; i < len; i++) {
                    result += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                return result;
            };
            
            // Make it look professional like license keys
            const part1 = randomUpper(4);
            const part2 = randomUpper(4);
            const part3 = Math.floor(Math.random() * 9000 + 1000).toString();
            
            // Different formats randomly
            const formats = [
                () => `SX2-${part1}-${part2}-${part3}`,
                () => `${prefixes[Math.floor(Math.random() * prefixes.length)]}${randomUpper(6)}${suffixes[Math.floor(Math.random() * suffixes.length)]}${Math.floor(Math.random() * 100)}`,
                () => `${randomUpper(4)}-${randomUpper(4)}-${randomUpper(4)}`,
                () => `SX2${randomUpper(8)}${Math.floor(Math.random() * 10000)}`,
                () => `LIC-${randomUpper(5)}-${Math.floor(Math.random() * 9999)}`
            ];
            
            const selectedFormat = formats[Math.floor(Math.random() * formats.length)];
            let key = selectedFormat();
            
            // Ensure key is uppercase and no confusing characters
            key = key.toUpperCase().replace(/[O0]/g, 'X').replace(/[I1]/g, 'K');
            
            return key;
        }

        // Add event listener for random key button
        document.addEventListener('DOMContentLoaded', function() {
            const randomBtn = document.getElementById('randomKeyBtn');
            const licenseInput = document.getElementById('cuslicense');
            
            if (randomBtn && licenseInput) {
                randomBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const newKey = generateRandomKey();
                    licenseInput.value = newKey;
                    // Trigger input event for any validation that might be listening
                    const event = new Event('input', { bubbles: true });
                    licenseInput.dispatchEvent(event);
                    // Optional: Visual feedback
                    randomBtn.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        randomBtn.style.transform = '';
                    }, 150);
                });
            }
        });

        // Copy functions (keep original)
        function copyBulk(btn) {
            const textarea = document.getElementById('bulkKeys');
            if (textarea) {
                textarea.select();
                document.execCommand('copy');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 1500);
            }
        }

        function copySingle(key, btn) {
            navigator.clipboard.writeText(key).then(() => {
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check text-emerald-400"></i>';
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                }, 1000);
            }).catch(() => {
                const textarea = document.createElement('textarea');
                textarea.value = key;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check text-emerald-400"></i>';
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                }, 1000);
            });
        }

        // Update total cost based on duration and max devices
        function updateTotalCost() {
            const durationSelect = document.getElementById('duration');
            const maxDevicesInput = document.getElementById('max_devices');
            const totalCostSpan = document.getElementById('totalCost');
            
            if (durationSelect && maxDevicesInput && totalCostSpan) {
                const selectedOption = durationSelect.options[durationSelect.selectedIndex];
                const pricePerDevice = selectedOption?.getAttribute('data-price') || 0;
                const devices = parseInt(maxDevicesInput.value) || 1;
                const total = parseFloat(pricePerDevice) * devices;
                totalCostSpan.textContent = total.toFixed(2);
            }
        }

        // Add data-price attributes to duration options if not present
        document.addEventListener('DOMContentLoaded', function() {
            const durationSelect = document.getElementById('duration');
            if (durationSelect) {
                // Map duration text to price based on the PHP price data
                const options = durationSelect.options;
                for(let i = 0; i < options.length; i++) {
                    const durationText = options[i].text;
                    let price = 0;
                    <?php if(isset($priceData) && is_array($priceData)): ?>
                        const hours = parseInt(durationText);
                        if (!isNaN(hours) && <?= json_encode($priceData) ?>[hours]) {
                            price = <?= json_encode($priceData) ?>[hours];
                        }
                    <?php endif; ?>
                    options[i].setAttribute('data-price', price);
                }
                updateTotalCost();
                durationSelect.addEventListener('change', updateTotalCost);
            }
            
            const maxDevicesInput = document.getElementById('max_devices');
            if (maxDevicesInput) {
                maxDevicesInput.addEventListener('input', updateTotalCost);
            }
        });
    </script>
</body>
</html>