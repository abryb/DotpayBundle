<?php
/*
 * This file is part of the DotpayBundle.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cogitech\DotpayBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cogitech_dotpay');

        $rootNode
	        ->children()
	            ->booleanNode('production')
	        	    ->defaultFalse()
	            ->end()
	            ->scalarNode('login')
	           		->isRequired()
	            ->end()
	            ->scalarNode('password')
	            	->isRequired()
	            ->end()
                ->scalarNode('shop_id')
	        	    ->isRequired()
	            ->end()
	            ->scalarNode('route_complete')
	            	->defaultValue('cg_dotpay_thanks')
	            ->end()
	            ->arrayNode('firewall')
	            	->treatFalseLike(array('disabled' => true))
	            	->treatTrueLike(array())
	            	->prototype('scalar')
	            	->end()
	            ->end()
       		->end();

        return $treeBuilder;
    }
}
