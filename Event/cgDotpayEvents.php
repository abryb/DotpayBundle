<?php
/*
 * This file is part of the DotpayBundle.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cogitech\DotpayBundle\Event;

final class cgDotpayEvents
{
	/**
	 * Event triggered when user clicked payment button
	 */
    const CREATE = 'cg.dotpay.create';
    
    /**
     * Event trggered when payment changed status to confirmed or canceled
     */
    const CHANGE_STATUS = 'cg.dotpay.change_status';
    
    /**
     * Event trggered when payment was canceled
     */
    const CANCEL = 'cg.dotpay.cancel';
    
    /**
     * Event trggered when payment was confirmed
     */
    const CONFIRM = 'cg.dotpay.confirm';
}
