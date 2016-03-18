## DotpayBundle

DotpayBundle provides basic functionality to create online payments for [dotpay.pl](http://dotpay.pl/)

## Installation

The recommended way to install this bundle is to rely on [Composer](https://getcomposer.org/):
```javascript
"require" : {
	// ...
    "cogitech/dotpay-bundle": "dev-master"
}
```

The second step is to register this bundle in the `AppKernel` class:

```php
public function registerBundles()
{
	$bundles = array(
       // ...
       new Cogitech\DotpayBundle\CogitechDotpayBundle(),
    );
}
```

### Configuration

Add following section in your `config.yml` file:
```yaml
cogitech_dotpay:
	production: true
    login: 'login'
    password: 'password'
    shop_id: '123456'
```

Parameters description:

- **production** - `false` value idicates test mode, `true` value indicates production mode. This parameter is required.

- **login** - Login to your dotpay account. This parameter is required.

- **password** - Password to your dotpay account. This parameter is required.

- **shop_id** - This is your shop id. This parameter is required.

- **firewall** - This parameter is not required. By default payment confirmation can be done from Dotpay IP servers only (`195.150.9.37`). You can pass here array of valid IP addresses or `false` to disable firewall (not recommended).

- **route_complete** - This is the symfony2 route where youser will be redirected from Dotpay website. This parameter is optional and default value is `cg_dotpay_thanks`.


## Basic usage
Render payment button in twig template by calling `dotpay_button` function:

```twig
{{ dotpay_button({
      title: 'Dotpay - 1.23 zł',
      class: 'btn btn-success',
      email: 'test@example.com',
      price: 1.23,
      first_name: 'Lorem',
      last_name: 'Ipsum',
      street: 'Lorem street',
      building_number: '1',
      postcode: '60-001',
      city: 'Lorem',
      description: 'Lorem Product',
      params: {
      	custom_parameter: '123'
	  }
   })
}}
```

Key `params` is optional. You can pass custom parameters to event this way.

### Events

Register event listeners to process DotpayBundle events:

Services file `services.yml`:
```yaml
services:
    cg.dotpay.listeners:
        class: AppBundle\EventListener\cgDotpayEventListener
        tags:
        	- { name: kernel.event_listener, event: cg.dotpay.create, method: create }
            - { name: kernel.event_listener, event: cg.dotpay.change_status, method: change_status }
            - { name: kernel.event_listener, event: cg.dotpay.confirm, method: confirm }
            - { name: kernel.event_listener, event: cg.dotpay.cancel, method: cancel }
```

Event listener class `cgDotpayEventListener`:
```php
<?php
namespace AppBundle\EventListener;

use Cogitech\DotpayBundle\Event\cgDotpayEvent;

class cgDotpayEventListener
{
	public function create(cgDotpayEvent $e){
		// handle payment create here...
	}
	
	public function confirm(cgDotpayEvent $e){
		// handle confirmation here...
	}

	public function cancel(cgDotpayEvent $e){
		// handle payment cancellation here...
	}

	public function change_status(cgDotpayEvent $e){
		// handle any status changes here...
	}
}
```