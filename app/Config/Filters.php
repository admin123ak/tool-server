<?php

namespace Config;

use App\Filters\AuthFilter;
use App\Filters\AdminFilter;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;

class Filters extends BaseConfig
{
	/**
	 * Configures aliases for Filter classes to make reading short
	 *
	 * @var array
	 */
	public $aliases = [
		'csrf'     => CSRF::class,
		'toolbar'  => DebugToolbar::class,
		'honeypot' => Honeypot::class,
		'auth'     => AuthFilter::class,
		'admin'    => AdminFilter::class,
	];

	/**
	 * List of filter aliases that are always applied to every request.
	 *
	 * @var array
	 */
	public $globals = [
		'before' => [
			// 'honeypot',
			'csrf' => [
				'except' => [
					'connect', 
					'webhook', 
					'telegram/webhook', // Bot ke liye zaroori line
					'api', 
					'api/*', 
					'keys/api'
				]
			],
			'auth' => [
				'except' => [
					'/', 
					'login', 
					'register', 
					'connect', 
					'webhook', 
					'telegram/webhook', // Bot ke liye zaroori line
					'api', 
					'api/*'
				]
			],
		],
		'after'  => [
			'toolbar',
			// 'honeypot',
		],
	];

	/**
	 * List of filter aliases that are applied to a particular HTTP method
	 * (GET, POST, etc.).
	 *
	 * @var array
	 */
	public $methods = [];

	/**
	 * List of filter aliases that should run on any before or after URI patterns.
	 *
	 * @var array
	 */
	public $filters = [];
}
