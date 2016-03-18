<?php
/*
 * This file is part of the DotpayBundle.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cogitech\DotpayBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Cogitech\DotpayBundle\Form\DataTransformer\ParamsTransformer;

class DotpayType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options) 
	{
	    $builder->add('price', 'hidden');
	    $builder->add('first_name', 'hidden');
	    $builder->add('last_name', 'hidden');
	    $builder->add('email', 'hidden');
	    $builder->add('phone', 'hidden');
	    $builder->add('description', 'hidden');
	    $builder->add('city', 'hidden');
	    $builder->add('postcode', 'hidden');
	    $builder->add('street', 'hidden');
	    $builder->add('building_number', 'hidden');
	    $builder->add('description', 'hidden');
	    $builder->add('token', 'hidden');
	    $builder->add('params', 'hidden');
	     
	    $builder->get('params')
	    	->addViewTransformer(new ParamsTransformer());
	}
	
	public function getName() {
		return 'cg_dotpay';
	}
}