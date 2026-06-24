<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?= $this->include('Layout/msgStatus') ?>
            
            <?php if (session()->getFlashdata('user_key')) : ?>
                <div class="alert alert-success bg-success/20 border border-success/30 text-success-100 rounded-3" role="alert">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-gem fs-5"></i>
                        <strong>License Generated Successfully!</strong>
                    </div>
                    <hr class="border-success/30 my-2">
                    <div class="small">
                        <div><strong>Game:</strong> <?= session()->getFlashdata('game') ?></div>
                        <div><strong>Duration:</strong> <?= session()->getFlashdata('duration') ?> Days</div>
                        <div><strong>License Key:</strong> <code class="bg-dark px-2 py-1 rounded"><?= session()->getFlashdata('user_key') ?></code></div>
                        <div><strong>Max Devices:</strong> <?= session()->getFlashdata('max_devices') ?></div>
                        <div><strong>Credits Deducted:</strong> <span class="text-danger">-<?= session()->getFlashdata('fees') ?></span> (Balance left: <?= number_format($user->saldo, 2) ?> Credits)</div>
                        <small class="text-muted"><i class="bi bi-info-circle"></i> Duration starts when license is first activated</small>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="card border-0 shadow-2xl rounded-4 overflow-hidden">
                <div class="card-header bg-gradient-dark p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-plus-circle fs-4 text-primary"></i>
                            <h4 class="mb-0 fw-bold text-white">Create License</h4>
                        </div>
                        <a class="btn btn-sm btn-outline-light rounded-3 px-3" href="<?= site_url('keys') ?>">
                            <i class="bi bi-people"></i> Manage Keys
                        </a>
                    </div>
                </div>
                
                <div class="card-body bg-dark-800 p-4">
                    <?= form_open('', ['class' => 'needs-validation']) ?>
                    
                    <!-- Game & Max Devices Row -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="game" class="form-label fw-semibold">
                                <i class="bi bi-controller text-primary me-1"></i> Select Game
                            </label>
                            <?= form_dropdown(
                                ['class' => 'form-select bg-dark-700 border-secondary text-white rounded-3', 'name' => 'game', 'id' => 'game'], 
                                $game, 
                                old('game') ?: ''
                            ) ?>
                            <?php if ($validation->hasError('game')) : ?>
                                <div class="text-danger small mt-1"><?= $validation->getError('game') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="max_devices" class="form-label fw-semibold">
                                <i class="bi bi-devices text-primary me-1"></i> Max Devices
                            </label>
                            <div class="d-flex gap-2">
                                <input type="number" name="max_devices" id="max_devices" class="form-control bg-dark-700 border-secondary text-white rounded-3" value="1" min="1" max="10" readonly>
                                <button type="button" id="incDevice" class="btn btn-outline-secondary rounded-3 px-3">+</button>
                                <button type="button" id="decDevice" class="btn btn-outline-secondary rounded-3 px-3">-</button>
                            </div>
                            <small class="text-muted"><i class="bi bi-info-circle"></i> Max devices allowed for this game</small>
                            <?php if ($validation->hasError('max_devices')) : ?>
                                <div class="text-danger small mt-1"><?= $validation->getError('max_devices') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Duration -->
                    <div class="mb-4">
                        <label for="duration" class="form-label fw-semibold">
                            <i class="bi bi-calendar-range text-primary me-1"></i> Duration (Days)
                        </label>
                        <?= form_dropdown(
                            ['class' => 'form-select bg-dark-700 border-secondary text-white rounded-3', 'name' => 'duration', 'id' => 'duration'], 
                            $duration, 
                            old('duration') ?: ''
                        ) ?>
                        <?php if ($validation->hasError('duration')) : ?>
                            <div class="text-danger small mt-1"><?= $validation->getError('duration') ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Key Type Selection -->
                    <div class="mb-4 p-3 bg-dark-900 rounded-3 border border-secondary">
                        <label class="fw-semibold mb-3"><i class="bi bi-key-fill text-primary me-1"></i> Key Type</label>
                        
                        <div class="d-flex flex-wrap gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="key_type" id="keyTypeAuto" value="auto" checked>
                                <label class="form-check-label" for="keyTypeAuto">
                                    <i class="bi bi-robot text-info"></i> Auto Generate Key
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="key_type" id="keyTypeCustom" value="custom">
                                <label class="form-check-label" for="keyTypeCustom">
                                    <i class="bi bi-pencil-square text-warning"></i> Custom Key
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="key_type" id="keyTypeBulk" value="bulk">
                                <label class="form-check-label" for="keyTypeBulk">
                                    <i class="bi bi-files text-success"></i> Bulk Keys
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Custom Key Input (hidden by default) -->
                    <div id="customKeySection" class="mb-4" style="display: none;">
                        <label for="custom" class="form-label fw-semibold">
                            <i class="bi bi-key text-warning me-1"></i> Enter Custom Key
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark-700 border-secondary"><i class="bi bi-key"></i></span>
                            <input type="text" name="cuslicense" class="form-control bg-dark-700 border-secondary text-white rounded-end-3" id="custom" placeholder="Enter your custom key (4-16 characters)" minlength="4" maxlength="16">
                        </div>
                        <small class="text-muted">Custom key must be unique and 4-16 characters long</small>
                    </div>
                    
                    <!-- Bulk Keys Section (hidden by default) -->
                    <div id="bulkKeySection" class="mb-4" style="display: none;">
                        <label for="hulala" class="form-label fw-semibold">
                            <i class="bi bi-files text-success me-1"></i> Number of Keys
                        </label>
                        <select class="form-select bg-dark-700 border-secondary text-white rounded-3" id="hulala" name="loopcount">
                            <option value="1">1 Key</option>
                            <option value="5">5 Keys</option>
                            <option value="10">10 Keys</option>
                            <option value="25">25 Keys</option>
                            <option value="50">50 Keys</option>
                            <option value="100">100 Keys</option>
                        </select>
                        <small class="text-muted">Bulk generation will deduct credits for each key</small>
                    </div>
                    
                    <!-- Hidden field to track selection -->
                    <input type="hidden" id="keyMode" name="key_mode" value="auto">
                    
                    <!-- Credits Estimation -->
                    <div class="mb-4 p-3 bg-gradient-dark rounded-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold"><i class="bi bi-wallet2 text-warning me-1"></i> Credits to Deduct:</span>
                            <span class="fs-3 fw-bold text-primary" id="estimation">0</span>
                        </div>
                        <small class="text-muted">Your current balance: <strong class="text-success"><?= number_format($user->saldo, 2) ?></strong> Credits</small>
                    </div>
                    
                    <!-- Generate Button -->
                    <button type="submit" class="btn btn-gradient w-100 py-3 rounded-3 fw-bold fs-5">
                        <i class="bi bi-lightning-charge-fill me-2"></i> Generate License
                    </button>
                    
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-dark-700 { background-color: #1e293b; }
    .bg-dark-800 { background-color: #0f172a; }
    .bg-dark-900 { background-color: #0a0f1c; }
    .bg-gradient-dark {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    }
    .btn-gradient {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        border: none;
        transition: all 0.3s ease;
    }
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
        color: white;
    }
    .rounded-4 { border-radius: 1rem; }
    .rounded-3 { border-radius: 0.75rem; }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
    .form-select, .form-control {
        transition: all 0.2s ease;
    }
    .form-select:focus, .form-control:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
    }
</style>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function () {
    // Game data and pricing
    let gamePrices = JSON.parse('<?= $price ?>');
    let currentMaxDevices = 1;
    let gameMaxDevices = {
        // Default max devices per game (you can customize this)
        <?php foreach ($game as $key => $val): ?>
            "<?= $key ?>": 5,
        <?php endforeach; ?>
    };
    
    // Elements
    const gameSelect = $("#game");
    const durationSelect = $("#duration");
    const maxDevicesInput = $("#max_devices");
    const incBtn = $("#incDevice");
    const decBtn = $("#decDevice");
    const estimationSpan = $("#estimation");
    
    // Key type radio buttons
    const keyTypeAuto = $("#keyTypeAuto");
    const keyTypeCustom = $("#keyTypeCustom");
    const keyTypeBulk = $("#keyTypeBulk");
    const customSection = $("#customKeySection");
    const bulkSection = $("#bulkKeySection");
    const keyModeHidden = $("#keyMode");
    
    // Update max devices based on selected game
    function updateMaxDevices() {
        let selectedGame = gameSelect.val();
        let maxLimit = gameMaxDevices[selectedGame] || 5;
        
        // Update max attribute
        maxDevicesInput.attr('max', maxLimit);
        
        // Reset to 1 if current value exceeds max
        let currentVal = parseInt(maxDevicesInput.val());
        if (currentVal > maxLimit) {
            maxDevicesInput.val(1);
        }
        currentMaxDevices = parseInt(maxDevicesInput.val());
    }
    
    // Calculate total credits
    function updatePrice() {
        let device = parseInt(maxDevicesInput.val()) || 1;
        let durate = durationSelect.val();
        let multiplier = 1;
        
        // Check key type for bulk
        if (keyTypeBulk.is(':checked')) {
            multiplier = parseInt($("#hulala").val()) || 1;
        }
        
        let pricePerKey = gamePrices[durate] || 0;
        let total = (device * pricePerKey) * multiplier;
        
        estimationSpan.text(isNaN(total) ? '0' : total);
    }
    
    // Device increment/decrement with validation
    incBtn.click(function() {
        let maxLimit = parseInt(maxDevicesInput.attr('max')) || 10;
        let newVal = parseInt(maxDevicesInput.val()) + 1;
        if (newVal <= maxLimit) {
            maxDevicesInput.val(newVal);
            updatePrice();
        } else {
            showToast("Max " + maxLimit + " devices allowed for this game", "warning");
        }
    });
    
    decBtn.click(function() {
        let newVal = parseInt(maxDevicesInput.val()) - 1;
        if (newVal >= 1) {
            maxDevicesInput.val(newVal);
            updatePrice();
        }
    });
    
    // Handle key type changes
    keyTypeAuto.change(function() {
        if (this.checked) {
            customSection.slideUp(200);
            bulkSection.slideUp(200);
            keyModeHidden.val("auto");
            updatePrice();
        }
    });
    
    keyTypeCustom.change(function() {
        if (this.checked) {
            customSection.slideDown(200);
            bulkSection.slideUp(200);
            keyModeHidden.val("custom");
            updatePrice();
        }
    });
    
    keyTypeBulk.change(function() {
        if (this.checked) {
            customSection.slideUp(200);
            bulkSection.slideDown(200);
            keyModeHidden.val("bulk");
            updatePrice();
        }
    });
    
    // Bulk quantity change
    $("#hulala").change(function() {
        updatePrice();
    });
    
    // Game change event
    gameSelect.change(function() {
        updateMaxDevices();
        updatePrice();
    });
    
    // Duration change event
    durationSelect.change(function() {
        updatePrice();
    });
    
    // Max devices manual change
    maxDevicesInput.on('input', function() {
        let maxLimit = parseInt(maxDevicesInput.attr('max')) || 10;
        let val = parseInt(this.value);
        if (val > maxLimit) {
            this.value = maxLimit;
        }
        if (val < 1) {
            this.value = 1;
        }
        updatePrice();
    });
    
    // Custom key validation
    $("#custom").on('input', function() {
        let val = $(this).val();
        if (val.length > 0 && (val.length < 4 || val.length > 16)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    // Toast notification function
    function showToast(message, type = 'info') {
        let toastHtml = `
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
                <div class="toast align-items-center text-white bg-${type === 'warning' ? 'warning' : 'dark'} border-0 show" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        `;
        $('body').append(toastHtml);
        setTimeout(() => $('.toast').toast('hide'), 3000);
    }
    
    // Initialize
    updateMaxDevices();
    updatePrice();
    
    // Form submission validation
    $('form').on('submit', function(e) {
        if (keyTypeCustom.is(':checked')) {
            let customKey = $("#custom").val();
            if (!customKey || customKey.length < 4 || customKey.length > 16) {
                e.preventDefault();
                showToast("Custom key must be 4-16 characters long", "warning");
                return false;
            }
        }
        return true;
    });
});
</script>
<?= $this->endSection() ?>