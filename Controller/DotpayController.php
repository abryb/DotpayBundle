<?php
/*
 * This file is part of the DotpayBundle.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cogitech\DotpayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Cogitech\DotpayBundle\Event\cgDotpayEvent;
use Cogitech\DotpayBundle\Event\cgDotpayEvents;

class DotpayController extends Controller
{
	public function createAction(Request $request)
	{
	    $form = $this->createForm('cg_dotpay');
	    $referer = $request->headers->get('referer');

		if ( $request->isMethod('post') && $request->request->has($form->getName()) )
		{
		    $form->handleRequest($request);

		    if ( $form->isValid() )
		    {
		        $dispatcher = $this->get('event_dispatcher');
		        $dotpay = $this->get('cg.dotpay');
		        
		        $data = new ParameterBag($form->getData());
		        $token = ( $data->get('token') !== NULL ) ? $data->get('token') : $dotpay->generateToken();
				$params = base64_encode(json_encode($data->get('params')));
				
				$url = $this->generateUrl($dotpay->getRouteComplete(), array(), UrlGeneratorInterface::ABSOLUTE_URL);
				$urlc = $this->generateUrl('cg_dotpay_confirm', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
				
				$dotpayRequest = array(
					'amount' => number_format($data->get('price'), 2),
					'description' => $data->get('description'),
					'control' => $params,
					'url' => $url,
					'urlc' => $urlc,
					'payer' => array(
						'first_name' => $data->get('first_name'),
			            'last_name' => $data->get('last_name'),
			            'email' => $data->get('email'),
			            'phone' => $data->get('phone'),
						'address' => array(
							'street' => $data->get('street'),
							'building_number' => $data->get('building_number'),
							'postcode' => $data->get('postcode'),
							'city' => $data->get('city')
						)
					)
				);
				
		        $dotpayResponse = $dotpay->createPaymentLink($dotpayRequest);
		        
		        $dispatcher->dispatch(cgDotpayEvents::CREATE, new cgDotpayEvent(
					new ParameterBag(array(
				        'token' => $token,
		        		'create_data' => $data->all(),
		        		'dotpay_request' => $dotpayRequest,
		        		'dotpay_response' => $dotpayResponse
				    ))
		        ));
		        
		        if (isset($dotpayResponse['payment_url'])) 
		        {
			       	return $this->redirect($dotpayResponse['payment_url']);
		        }
		        else 
		        {
		        	$dispatcher->dispatch(cgDotpayEvents::CHANGE_STATUS, new cgDotpayEvent(
	        			new ParameterBag(array(
	        				'token' => $token,
	        				'status' => 'cancel',
	        				'dotpay_response' => null
	        			))
		        	));
		        	
		        	$dispatcher->dispatch(cgDotpayEvents::CANCEL, new cgDotpayEvent(
	        			new ParameterBag(array(
	        				'token' => $token,
	        				'dotpay_response' => null
	        			))
		        	));
		        }
		    }
		}
		else {
			throw $this->createNotFoundException();
		}

		return $this->redirect($referer);
	}
	
	public function confirmAction(Request $request, $token = null) 
	{
		$result = array('result' => false);
		$dotpay = $this->get('cg.dotpay');
		
		// Check if we should process request
		if ( !$dotpay->firewall($request->getClientIp()) ) {
			throw $this->createAccessDeniedException();
		}
		
		if ( $request->isMethod('post') )
		{
			$dispatcher = $this->get('event_dispatcher');
			$r = $request->request;
			
			if ( !is_null($token) )
			{
				if ( $r->get('status', null) === 'OK' )
				{
					$result = array('result' => true);
					
					$dispatcher->dispatch(cgDotpayEvents::CONFIRM, new cgDotpayEvent(
						new ParameterBag(array(
							'token' => $token,
							'dotpay_response' => $r->all()
						))
					));
					
					$dispatcher->dispatch(cgDotpayEvents::CHANGE_STATUS, new cgDotpayEvent(
						new ParameterBag(array(
							'token' => $token,
							'status' => 'confirm',
							'dotpay_response' => $r->all()
						))
					));
				}
				else
				{
					$dispatcher->dispatch(cgDotpayEvents::CANCEL, new cgDotpayEvent(
						new ParameterBag(array(
							'token' => $token,
							'dotpay_response' => $r->all()
						))
					));
						
					$dispatcher->dispatch(cgDotpayEvents::CHANGE_STATUS, new cgDotpayEvent(
						new ParameterBag(array(
							'token' => $token,
							'status' => 'confirm',
							'dotpay_response' => $r->all()
						))
					));
				}
			}
		}
		
		return new JsonResponse($result);
	}
	
	public function thanksAction(Request $request) {
		return $this->render('CogitechDotpayBundle:Dotpay:thanks.html.twig');
	}
}
