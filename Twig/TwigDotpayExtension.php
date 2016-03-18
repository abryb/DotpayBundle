<?php 
/*
 * This file is part of the DotpayBundle.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cogitech\DotpayBundle\Twig;

use Symfony\Component\HttpFoundation\ParameterBag;

class TwigDotpayExtension extends \Twig_Extension
{	
    private $formFactory = null;
    
    public function __construct($formFactory) {
        $this->formFactory = $formFactory;
    }
    
    public function getFunctions()
    {
        return array(
        	new \Twig_SimpleFunction('dotpay_button', array($this, 'buttonFunction'), array(
        		'is_safe' => array('html'), 'needs_environment' => true)),
        );
    }
 
    public function buttonFunction(\Twig_Environment $env, $params = array(), $template = 'CogitechDotpayBundle:Dotpay:button.html.twig') {
    	$params = new ParameterBag(is_array($params) ? $params : array());
    	$template = $params->get('template', $template);
    	
    	if ( !$params->has('class') ) 
    		$params->set('class', '');
    	if ( !$params->has('title') ) 
    		$params->set('title', 'Płatność internetowa');
    	if ( !$params->has('description') ) 
    		$params->set('description', 'Płatność internetowa');
    	if ( !$params->has('name') )
    		$params->set('name', '');
    	if ( !$params->has('price') )
    		$params->set('price', 0);
    	
    	if ( !$params->has('form') ) 
    	{
    	    $form = $this->formFactory
    	       ->createBuilder('cg_dotpay')
    	       ->getForm();
    	    
    	    $data = array();
    	    $data['params'] = $params->get('params');
    	    
    	    if ( !is_array($data['params']) )
    	    	$data['params'] = array();
    	   	
    	    foreach ( $form->all() as $field => $child )
    	        $data[$field] = $params->get($field);
    	    
    	    $form->setData($data);
    	    
    	    $params->set('form', $form->createView());
    	}
    	
    	return $env->loadTemplate($template)->render($params->all());
    }
    
    public function getName()
    {
        return 'dotpay';
    }
}