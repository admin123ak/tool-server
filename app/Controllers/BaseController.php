<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class BaseController extends Controller
{
	protected $request;
	protected $helpers = ['nata', 'form', 'url', 'text', 'html', 'filesystem'];

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
		
		$uri = service('uri');
		if ($uri->getSegment(1) === 'api' || $uri->getSegment(1) === 'webhook') {
			return; 
		}

		$this->session = \Config\Services::session();
	}

	function getUserIP()
	{
        $clientIp  = @$_SERVER['HTTP_CLIENT_IP'];
        $forwardIp = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remoteIp  = $_SERVER['REMOTE_ADDR'];
        return filter_var($clientIp, FILTER_VALIDATE_IP) ? $clientIp : (filter_var($forwardIp, FILTER_VALIDATE_IP) ? $forwardIp : $remoteIp);
    }
}