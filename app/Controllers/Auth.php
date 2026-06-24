<?php

namespace App\Controllers;

use App\Models\CodeModel;
use App\Models\UserModel;
use CodeIgniter\Config\Services;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // LOGIN PAGE
    public function login()
    {
        if (session()->has('userid')) return redirect()->to('dashboard');

        if ($this->request->getPost()) return $this->login_action();

        return view('Auth/login', [
            'title' => 'Login',
            'validation' => Services::validation()
        ]);
    }

    // LOGIN ACTION
    private function login_action()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $stay_log = $this->request->getPost('stay_log');

        $form_rules = [
            'username' => 'required|alpha_numeric|min_length[4]|max_length[25]|is_not_unique[users.username]',
            'password' => 'required|min_length[3]|max_length[45]',
        ];

        if (!$this->validate($form_rules)) {
            return redirect()->route('login')->withInput()->with('msgDanger', 'Please check the form.');
        }

        $cekUser = $this->userModel->getUser($username, 'username');

        if (!$cekUser || !password_verify(create_password($password, false), $cekUser->password)) {
            return redirect()->route('login')->withInput()->with('msgDanger', 'Wrong username or password.');
        }

        $time = new \CodeIgniter\I18n\Time;
        session()->set([
            'userid' => $cekUser->id_users,
            'unames' => $cekUser->username,
            'time_login' => $stay_log ? $time::now()->addHours(24) : $time::now()->addMinutes(30),
            'time_since' => $time::now(),
        ]);

        return redirect()->to('dashboard');
    }

    // REGISTER PAGE
    public function register()
    {
        if (session()->has('userid')) return redirect()->to('dashboard');

        if ($this->request->getPost()) return $this->register_action();

        return view('Auth/register', [
            'title' => 'Register',
            'validation' => Services::validation()
        ]);
    }

    // REGISTER ACTION
    public function register_action()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $password2 = $this->request->getPost('password2');
        $referral = $this->request->getPost('referral');

        $form_rules = [
            'username' => 'required|alpha_numeric|min_length[4]|max_length[25]|is_unique[users.username]',
            'password' => 'required|min_length[3]|max_length[45]',
            'password2' => 'required|matches[password]',
            'referral' => 'required|min_length[6]|alpha_numeric'
        ];

        if (!$this->validate($form_rules)) {
            return redirect()->route('register')->withInput()->with('msgDanger', 'Please check the form.');
        }

        $mCode = new CodeModel();
        $rCheck = $mCode->checkCode($referral);

        if (!$rCheck || $rCheck->used_by) {
            return redirect()->route('register')->withInput()->with('msgDanger', 'Invalid or used referral code.');
        }

        $hashPassword = create_password($password);
        $data_register = [
            'username' => $username,
            'password' => $hashPassword,
            'saldo' => $rCheck->set_saldo ?: 0,
            'uplink' => $rCheck->created_by
        ];

        $ids = $this->userModel->insert($data_register, true);
        if ($ids) $mCode->useReferral($referral, $username);

        return redirect()->to('login')->with('msgSuccess', 'Registration successful!');
    }

    // LOGOUT
    public function logout()
    {
        session()->remove(['userid', 'unames', 'time_login', 'time_since']);
        session()->setFlashdata('msgSuccess', 'Logout successful.');
        return redirect()->to('login');
    }
}