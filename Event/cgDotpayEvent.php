<?php
/*
 * This file is part of the DotpayBundle.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cogitech\DotpayBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\ParameterBag;

class cgDotpayEvent extends Event
{
    protected $parameters;

    public function __construct(ParameterBag $parameters) {
        $this->parameters = $parameters;
    }

    public function getParameters() {
        return $this->parameters;
    }
}

