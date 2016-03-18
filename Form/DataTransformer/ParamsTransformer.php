<?php
/*
 * This file is part of the DotpayBundle.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cogitech\DotpayBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ParamsTransformer implements DataTransformerInterface
{
    public function transform($params)
    {
        if ( \is_array($params) && !empty($params) )
        	return \base64_encode(\json_encode($params));

        return '';
    }

    public function reverseTransform($params)
    {
    	$result = array();
    	
    	if ( !empty($params) )
    	{
    		$data = \base64_decode($params);
    		
    		if ( false !== $data ) 
    		{
    			$data = \json_decode($data, true);
    			
    			if ( \is_array($data) )
    				$result = $data;
    		}
    	}
    	
    	return $result;
    }
}