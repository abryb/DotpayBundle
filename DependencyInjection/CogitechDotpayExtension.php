<?php
/*
 * This file is part of the DotpayBundle.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cogitech\DotpayBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class CogitechDotpayExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        
        $parameters = $container->getParameter('cogitech_dotpay', array());

        if ( isset($parameters['endpoint']) ) {
        	$config['endpoint'] = $parameters['endpoint'];
        }
        
        if ( isset($config['firewall']) )
        {
        	if ( isset($config['firewall']['disabled']) ) {
        		$config['firewall'] = false;
        	}
        	else if ( empty($config['firewall']) && isset($parameters['default_values']['firewall']) ) {
        		$config['firewall'] = $parameters['default_values']['firewall'];
        	}
        }
        
        $container->setParameter('cogitech_dotpay', $config);
    }
}
