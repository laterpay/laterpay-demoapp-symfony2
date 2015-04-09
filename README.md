laterpay-demoapp-symfony2
=========================

Code for the tutorial on how to integrate typical LaterPay functionality into a simple symfony2 based CMS

Requirements
------------

Symfony is only supported on PHP 5.3.3 and up.


Installation
------------

####Clone repository
```
$ git clone git@github.com:laterpay/laterpay-demoapp-symfony2.git
```

```
$ cd laterpay-demoapp-symfony2
```

####Install composer
```
$ curl -sS https://getcomposer.org/installer | php
```

####Install vendors
```
$ php composer.phar install --no-interaction
```

####Check PHP configuration
```
$ php app/check.php
```

####Modify `app/config/parameters.yml`:
 - Chahge DB credentials: `database_driver`, `database_host`, `database_port`, `database_name`, `database_user`, `database_password`
 - Change LaterPay credentials: `laterpay_sandbox_merchant_id`, `laterpay_sandbox_api_key`,  `laterpay_live_merchant_id`, `laterpay_live_api_key`, `laterpay_sandbox_mode`
 - Change `app_host` to you host name


####Create database
```
$ php app/console doctrine:database:create
```

####Setup database
```
$ php app/console doctrine:migrations:migrate --no-interaction
```

####Create admin user
```
$ php app/console fos:user:create --super-admin admin admin@example.com password
```

####Running the Symfony Application
```
$ php app/console server:run
```

Check admin endpoint: http://127.0.0.1:8000/admin/

Check frontend endpoint: http://127.0.0.1:8000/

See also: [Integration with LaterPay HOWTO][1]


Copyright
------------

Copyright 2015 LaterPay GmbH – Released under MIT License

[1]: HOWTO.md
