<?php

include('conn.php');
include('mail.php');

// Credit
$sql = "SELECT * FROM credit where id=1";
$result = mysqli_query($conn, $sql);
$credit = mysqli_fetch_assoc($result);

// IP FIX (IMPORTANT)
$user_ip = service('request')->getIPAddress();

?>

<?= $this->extend('Layout/Starter') ?>
<?= $this->section('content') ?>

<style>
body{
    background:
        radial-gradient(circle at top, rgba(255,200,0,0.18), transparent 60%),
        linear-gradient(180deg, #1a0000, #000000 80%);
    min-height:100vh;
}

/* CENTER */
.register-wrapper{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* CARD */
.register-card{
    width:100%;
    max-width:450px;
    background:linear-gradient(180deg,#120000,#050000);
    border-radius:26px;
    padding:35px 28px;
    border:1px solid rgba(255,200,80,0.45);
    box-shadow:
        0 0 25px rgba(255,0,0,.35),
        0 0 70px rgba(255,200,0,.25);
    color:#fff;
    position:relative;
}

.register-card::before{
    content:'';
    position:absolute;
    top:0;left:0;
    width:100%;height:4px;
    background:linear-gradient(90deg,#ff0000,#ffcc00,#ff0000);
}

/* TITLE */
.register-title{
    text-align:center;
    font-size:30px;
    font-weight:800;
    background:linear-gradient(90deg,#ffcc00,#ff8800,#ffcc00);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

/* SUB */
.register-sub{
    text-align:center;
    understanding:0;
    color:#ffd98a;
    font-size:14px;
    margin-bottom:25px;
}

/* INPUT */
.form-control{
    background:#140000;
    border:1px solid rgba(255,160,0,.45);
    border-radius:15px;
    color:#fff;
    padding:13px 16px;
}
.form-control::placeholder{color:#caa25a;}
.form-control:focus{
    background:#140000;
    border-color:#ffcc00;
    box-shadow:0 0 0 2px rgba(255,200,0,.5);
}

/* LABEL */
label{
    color:#ffe0a3;
    font-size:14px;
    margin-bottom:6px;
}

/* ICON */
.input-icon{position:relative;}
.input-icon i{
    position:absolute;
    right:14px;
    top:44px;
    color:#ffcc00;
}

/* BUTTON */
.btn-register{
    background:linear-gradient(90deg,#ff0000,#ffcc00);
    border:none;
    border-radius:18px;
    padding:14px;
    font-weight:800;
    letter-spacing:1px;
    color:#000;
    width:100%;
    box-shadow:
        0 0 18px rgba(255,180,0,.7),
        inset 0 0 6px rgba(255,255,255,.4);
    transition:.3s;
}
.btn-register:hover{
    transform:translateY(-2px);
    box-shadow:0 0 35px rgba(255,220,0,1);
}

/* FOOT */
.after-text{
    margin-top:20px;
    text-align:center;
    color:#ffd98a;
    font-size:14px;
}
.after-text a{
    color:#ffcc00;
    font-weight:700;
    text-decoration:none;
}
.after-text a:hover{text-decoration:underline;}
</style>

<div class="register-wrapper">
<div class="register-card">

<?= $this->include('Layout/msgStatus') ?>

<h3 class="register-title">VIP TEAM</h3>
<p class="register-sub">CREATE YOUR GOLD ACCESS</p>

<?= form_open() ?>

<div class="mb-3 input-icon">
<label>Email</label>
<input type="email" name="email" class="form-control" placeholder="Enter your email" value="<?= old('email') ?>" required>
<i class="bi bi-envelope-fill"></i>
</div>

<div class="mb-3 input-icon">
<label>Username</label>
<input type="text" name="username" class="form-control" placeholder="Choose username" value="<?= old('username') ?>" required>
<i class="bi bi-person-fill"></i>
</div>

<div class="mb-3 input-icon">
<label>Full Name</label>
<input type="text" name="fullname" class="form-control" placeholder="Your full name" value="<?= old('fullname') ?>" required>
<i class="bi bi-person-badge-fill"></i>
</div>

<div class="mb-3 input-icon">
<label>Password</label>
<input type="password" name="password" class="form-control" placeholder="Create password" required>
<i class="bi bi-lock-fill"></i>
</div>

<div class="mb-3 input-icon">
<label>Confirm Password</label>
<input type="password" name="password2" class="form-control" placeholder="Confirm password" required>
<i class="bi bi-lock-fill"></i>
</div>

<div class="mb-3 input-icon">
<label>Referral Code</label>
<input type="text" name="referral" class="form-control" placeholder="Referral code" value="<?= old('referral') ?>" required>
<i class="bi bi-gift-fill"></i>
</div>

<div class="mb-4 input-icon">
<label>Your IP</label>
<input type="text" class="form-control" value="<?= $user_ip ?>" readonly>
<i class="bi bi-globe"></i>
</div>

<button type="submit" class="btn-register">
✨ CREATE VIP ACCOUNT
</button>

<?= form_close() ?>

<div class="after-text">
Already VIP? <a href="<?= site_url('login') ?>">Login Here</a>
</div>

</div>
</div>

<?= $this->endSection() ?>