<?php

namespace App\Controllers;

use App\Models\Server;
use App\Models\Status;
use App\Models\Feature;
use CodeIgniter\Controller;



class Home extends BaseController
{

    public function index()
    {
        echo view('intro');
    }

    public function migrate()
    {
        $db = \Config\Database::connect();
        
        if ($db->tableExists('users')) {
            if (!$db->fieldExists('telegram_id', 'users')) {
                $db->query("ALTER TABLE `users` ADD `telegram_id` VARCHAR(50) NULL DEFAULT NULL AFTER `password`");
                echo "Successfully added 'telegram_id' to users table.<br>";
            } else {
                echo "Column 'telegram_id' already exists in users table.<br>";
            }
            
            if (!$db->fieldExists('seller_key', 'users')) {
                $db->query("ALTER TABLE `users` ADD `seller_key` VARCHAR(100) NULL DEFAULT NULL AFTER `telegram_id`");
                echo "Successfully added 'seller_key' to users table.<br>";
            } else {
                echo "Column 'seller_key' already exists in users table.<br>";
            }
        } else {
            echo "Users table does not exist.<br>";
        }
    }
}
