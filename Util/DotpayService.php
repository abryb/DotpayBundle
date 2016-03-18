<?php
/*
 * This file is part of the DotpayBundle.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cogitech\DotpayBundle\Util;

use Symfony\Component\HttpFoundation\ParameterBag;
use Cogitech\DotpayBundle\Exception\RuntimeException;

class DotpayService {
	private $config = array();

	private $login = null;
	private $password = null;
	private $endpoint = null;
	private $shopId = null;
	private $prod = false;

	public function __construct($config) 
	{
		$this->initialize($config);
	}

	public function initialize($config) 
	{
		$this->config = new ParameterBag($config);

		if ( $this->config->get('production', false) === true )
			$this->endpoint = $this->config->get('endpoint')['prod'];
		else
			$this->endpoint = $this->config->get('endpoint')['test'];

		$this->login = $this->config->get('login');
		$this->password = $this->config->get('password');
		$this->shopId = $this->config->get('shop_id');
	}

	public function request($url, $params = array(), $method = 'GET') 
	{
		$ch = curl_init();
		$url = $this->endpoint . $url;

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_USERPWD, $this->login . ':' . $this->password);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json'
		));
		
		if (count($params) > 0)
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
			curl_setopt($ch, CURLOPT_POST, 1);
		}

		$response = curl_exec($ch);
		
		curl_close($ch);

		if ( $response )
			$response = json_decode($response, true);

		return $response;
	}
	
	public function firewall($ip) 
	{
		$firewall = $this->config->get('firewall');
		return $firewall === false || (is_array($firewall) && in_array($ip, $firewall));
	}
	
	public function createPaymentLink($params) 
	{
		$this->createPaymentLinkParams($params);
		return $this->request('accounts/'.$this->shopId.'/payment_links/', $params);
	}
	
	private function createPaymentLinkParams(&$params) 
	{
		if (!array_key_exists('expiration_datetime', $params)) // format: YYYY-MM-DDTHH:MM:SS
		{
			$dateTime = new \DateTime();
			$dateTime->modify('+3 hour');
			$params['expiration_datetime'] = $dateTime->format('Y-m-d') . 'T' . $dateTime->format('H:i:s');  
		}
		
		if (!array_key_exists('language', $params))
			$params['language'] = 'pl';
		
		if (!array_key_exists('currency', $params))
			$params['currency'] = 'PLN';
		
		if (!array_key_exists('redirection_type', $params))
			$params['redirection_type'] = 0;
		
		if (!array_key_exists('onlinetransfer', $params))
			$params['onlinetransfer'] = 1;
		
		if (!array_key_exists('ch_lock', $params))
			$params['ch_lock'] = 0;
	}
	
	public function generateToken() {
		return base_convert(sha1(uniqid(mt_rand(), true)), 16,36);
	}
	
	public function getRouteComplete() {
		return $this->config->get('route_complete');
	}
}
