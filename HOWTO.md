Requirements
------------

Symfony is only supported on PHP 5.3.3 and up.


Install Simple CMS
------------

####Clone repository
```
$ git clone git@github.com:laterpay/laterpay-demoapp-symfony2.git
$ cd laterpay-demoapp-symfony2
$ git checkout feature/simple-cms
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


Integration with LaterPay
-----------

####Configure repository

Modify `composer.json` and add repositories:
```
    ...
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/laterpay/laterpay-client-php"
        },
        {
            "type": "vcs",
            "url": "https://github.com/laterpay/laterpay-php-browscap-library"
        }
    ],
    ...
```
####Install vendors
```
$ php composer.phar require laterpay/laterpay-client-php:dev-develop laterpay/laterpay-php-browscap-library:dev-develop
```

####Create LaterPay manager

```php
<?php

// src/AppBundle/Services/LaterPayManager.php

namespace AppBundle\Services;

use Symfony\Component\DependencyInjection\Container;

use AppBundle\Entity\Post;
use AppBundle\Entity\TimePass;

use AppBundle\Interfaces\RevenueModel;

class LaterPayManager
{
    /**
     * @var \LaterPay_Client
     */
    public $client;

    /**
     * @var \LaterPayBrowscap
     */
    public $browscap;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container    = $container;
        $config             = $container->getParameter('laterpay');

        $this->config = $config['use_sandbox'] ? $config['sandbox'] : $config['live'];

        $this->client = new \LaterPay_Client(
            $this->config['cp_key'],
            $this->config['api_key'],
            $this->config['api_root'],
            $this->config['web_root']
        );

        $this->browscap = new \LaterPayBrowscap($container->getParameter('kernel.cache_dir'));

        $this->browscap->cacheFilename  = 'browscap.php';
        $this->browscap->doAutoUpdate   = true;

        if (!$this->browscap->isBrowserSupportsCookies() || $this->browscap->isCrawler()) {
            $this->enabled = false;
        }

    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return string
     */
    public function getJavaScriptUrl()
    {
        return $this->config['js_url'];
    }

    /**
     * @return string
     */
    public function getBalanceUrl()
    {
        return $this->client->get_controls_balance_url();
    }

    /**
     * @param string $show
     * @param string $css
     * @return string
     */
    public function getControlsLinksUrl($show = null, $css = null)
    {
        return $this->client->get_account_links_url($show, $css, $this->getCurrentUrl());
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->client->get_login_dialog_url($this->getCurrentUrl(), true);
    }

    /**
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->client->get_logout_dialog_url($this->getCurrentUrl(), true);
    }

    /**
     * @return string
     */
    public function getSignupUrl()
    {
        return $this->client->get_signup_dialog_url($this->getCurrentUrl(), true);
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $this->container->get('request');

        $url = $request->getSchemeAndHttpHost() . $request->getRequestUri();

        return $url;
    }

    /**
     * @param Post $post
     * @param array $timepasses
     * @return bool
     */
    public function hasAccess(Post $post, $timepasses = array())
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $ids = array(
            $this->getArticleId($post),
        );

        foreach ($timepasses as $timepass) {
            /* @var $timepass \AppBundle\Entity\Timepass */
            $ids[] = $this->getArticleId($timepass);
        }

        $access = $this->client->get_access($ids);

        $res = false;

        if (isset($access['articles'])) {
            foreach ($access['articles'] as $article) {
                if ($article['access']) {
                    $res = true;
                    break;
                }
            }
        }

        return $res;
    }

    /**
     * @param RevenueModel $item
     * @return bool
     */
    public function isPPU(RevenueModel $item)
    {
        return !$item->getPrice() || $item->getRevenueModel() !== RevenueModel::RM_SIS;
    }

    /**
     * @param RevenueModel $item
     * @return string
     */
    public function getPrice(RevenueModel $item)
    {
        return sprintf('%0.2f EUR', $item->getPrice());
    }

    /**
     * @param RevenueModel $item
     * @return string
     */
    protected function getArticleId(RevenueModel $item)
    {
        return md5(get_class($item)). '-' . $item->getId();
    }

    /**
     * @param RevenueModel $item
     * @return string
     */
    public function getPayUrl(RevenueModel $item)
    {
        $data = array(
            'article_id'    => $this->getArticleId($item),
            'pricing'       => 'EUR' . ceil($item->getPrice() * 100),
            'title'         => (string)$item,
            'url'           => $this->getCurrentUrl(),
        );

        if ($item instanceof TimePass) {
            /* @var $item \AppBundle\Entity\TimePass */
            $data['expiry'] = '+' . $item->getTimeValid();
        }

        if ($this->isPPU($item)) {
            $url = $this->client->get_add_url($data);
        } else {
            $url = $this->client->get_buy_url($data);
        }

        return $url;
    }
}
```

#### Create LaterPay request listener

```php
<?php

// src/AppBundle/Listeners/LaterPayRequestListener.php

namespace AppBundle\Listeners;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\RedirectResponse;

use AppBundle\Services\LaterPayManager;

class LaterPayRequestListener
{
    /**
     * @var LaterPayManager
     */
    protected $manager;

    /**
     * @param LaterPayManager $manager
     */
    public function __construct(LaterPayManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param GetResponseEvent $event
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            // don't do anything if it's not the master request
            return;
        }

        if (!$this->manager->isEnabled()) {
            return;
        }

        $client = $this->manager->client;

        if (null !== ($token = $event->getRequest()->get('lptoken'))) {

            $client->set_token($token);

            $url = $client->get_current_url();

            $event->setResponse(new RedirectResponse($url));

        } elseif (!$client->has_token()) {

            $url = $client->_get_token_redirect_url($client->get_current_url());

            $event->setResponse(new RedirectResponse($url));

        }

    }

}
```

#### Create LaterPay Twig extension
```php
<?php

// src/AppBundle/Twig/Extension/LaterPayExtension.php

namespace AppBundle\Twig\Extension;

use AppBundle\Services\LaterPayManager;

class LaterPayExtension extends \Twig_Extension
{
    /**
     * @var LaterPayManager
     */
    protected $manager;

    /**
     * @param LaterPayManager $manager
     */
    public function __construct(LaterPayManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'LaterPay' => new \Twig_Function_Method($this, 'LaterPay'),
        );
    }

    /**
     * @return \AppBundle\Services\LaterPayManager
     */
    public function LaterPay()
    {
        return $this->manager;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app_laterpay';
    }
}
```

#### Register services

Create `laterpay.yml`
```yml
# src/AppBundle/Resources/config/services/laterpay.yml
parameters:
    laterpay:

        use_sandbox: "%laterpay_sandbox_mode%"

        sandbox:
            cp_key:     %laterpay_sandbox_merchant_id%
            api_key:    %laterpay_sandbox_api_key%
            api_root:   "https://api.sandbox.laterpaytest.net"
            web_root:   "https://web.sandbox.laterpaytest.net"
            js_url:     "//sandbox.lpstatic.net/combo?yui/3.17.2/build/yui/yui-min.js&client/1.0.0/config-sandbox.js"

        live:
            cp_key:     %laterpay_live_merchant_id%
            api_key:    %laterpay_live_api_key%
            api_root:   "https://api.laterpay.net"
            web_root:   "https://web.laterpay.net"
            js_url:     "//lpstatic.net/combo?yui/3.17.2/build/yui/yui-min.js&client/1.0.0/config.js"

services:
    app.laterpay:
        class: AppBundle\Services\LaterPayManager
        arguments: [@service_container]

    kernel.listener.app.laterpay:
        class: AppBundle\Listeners\LaterPayRequestListener
        arguments: [@app.laterpay]
        tags:
            - { name: kernel.event_listener, event: kernel.request, method: onKernelRequest }

    twig.extension.laterpay:
        class: AppBundle\Twig\Extension\LaterPayExtension
        arguments: [@app.laterpay]
        tags:
            - { name: twig.extension }
```

Import `laterpay.yml`

```yml
# src/AppBundle/Resources/config/services.yml
imports:
    - { resource: services/laterpay.yml }
    ...
```

Add LaterPay vars to `app/config/parameters.yml.dist`
```yml
# app/config/parameters.yml.dist
parameters:
    ...
    laterpay_sandbox_merchant_id:   "LaterPay-WordPressDemo"
    laterpay_sandbox_api_key:       "decafbaddecafbaddecafbaddecafbad"
    laterpay_live_merchant_id:      "Live Merchant Id"
    laterpay_live_api_key:          "Live API Key"
    laterpay_sandbox_mode:          true
```

Update `app/config/parameters.yml` and enter LaterPay credentials
```
$ php composer.phar install
```

####Layout

Add Invoice and ControlsLinks iframes to layout
```twig
{# app/Resources/views/layout.html.twig #}
<!DOCTYPE html>
<html>
    ...
    <body>
        <div class="main-container">
            ...

            <div id="laterpay-balance" class="laterpay-balance"></div>
            <div id="laterpay-controls" class="laterpay-controls"></div>
        </div>
        ...
        {% block javascripts %}
            {% if LaterPay().isEnabled %}
                <script type="text/javascript" src="{{ LaterPay().getJavaScriptUrl() }}"></script>
                <script type="text/javascript">
                    var lpOptions = {
                        login: "{{ LaterPay().getLoginUrl() | raw }}",
                        logout: "{{ LaterPay().getLogoutUrl() | raw }}",
                        signup: "{{ LaterPay().getSignupUrl() | raw }}",
                        balance: "{{ LaterPay().getBalanceUrl() | raw }}",
                        controlsLinks: "{{ LaterPay().getControlsLinksUrl('lsg', asset('css/laterpay.css', absolute=true)) | raw }}"
                    };
                </script>

                <script type="text/javascript">
                    YUI().use("node", "laterpay-dialog", "laterpay-iframe", "laterpay-easyxdm", function(Y) {

                        var params = {
                            scrolling: "no",
                            frameborder: "0"
                        };

                        new Y.LaterPay.IFrame(
                            Y.one("#laterpay-balance"),
                            lpOptions.balance,
                            params
                        );

                        new Y.LaterPay.IFrame(
                            Y.one("#laterpay-controls"),
                            lpOptions.controlsLinks,
                            params
                        );

                        var accountManager = new Y.LaterPay.AccountActionHandler(
                            new Y.LaterPay.DialogManager(),
                            lpOptions.login,
                            lpOptions.logout,
                            lpOptions.signup
                        );

                        Y.on("laterpay:iFrameMessage", accountManager.onDialogXDMMessage, accountManager);

                        Y.on("laterpay:dialogMessage", function(e) {
                            switch (e.msg) {
                                case "laterpay.user.login":
                                case "laterpay.user.logout":
                                    location.reload();
                                break;
                            }
                        });

                        var linksDM = new Y.LaterPay.DialogManager();

                        linksDM.attachToLinks(".paylater", true);

                    });
                </script>
            {% endif %}
        {% endblock %}
    </body>
</html>
```

Update `main.css` and put new styles
```css
/* web/css/main.css */

...

.laterpay-balance {
    display: inline-block;
}

.laterpay-balance iframe {
    display: inline-block;
    margin-left: 30px;
    margin-bottom: -10px;
    width: 110px;
    height: 30px;
}

.laterpay-controls {
    display: inline-block;
}

.laterpay-controls iframe {
    display: inline-block;
    margin-bottom: -10px;
    width: 270px;
    height: 30px;
}

.post-view .timepass {
    padding: 5px;
    margin: 2px;
    display: inline-block;
    border: 1px solid #E0E0E0;
}

/* LaterPay Post View */
iframe {
    border: 0px;
}

.paylater {
    display: block;
    margin-top: 10px;
    margin-bottom: 10px;
}

.yui3-widget-mask {
    opacity: 0.89;
    filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=89);
    background: black;
    height: 100%;
    left: 0;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000000;
}

.yui3-panel:focus, .yui3-overlay:focus {
    outline: 0;
}

.lp_closebtn {
    padding: 5px;
    position: fixed;
    right: 0;
    top: 0;
}

.lp_closebtn a {
    color: white;
    padding: 11px;
    text-decoration: none;
}
```

And create `laterpay.css` for ControlsLinks iframe
```css
/* web/css/laterpay.css */
.container {
    display: inline-block;
    margin-top: 5px;
}

```

####View post

Add new method to `TimePassRepository`
```php
<?php

// src/AppBundle/Entity/TimePassRepository.php

...

class TimePassRepository extends EntityRepository
{
    ...

    /**
     * @param \AppBundle\Entity\Post $post
     * @return array
     */
    public function findByPost(Post $post)
    {
        $query = $this->createQueryBuilder('tp');

        $query
            ->where('tp.category IS NULL OR tp.category = :category')
            ->setParameter('category', $post->getCategory())
            ->addOrderBy('tp.category', 'desc')
            ->addOrderBy('tp.price', 'asc')
        ;

        return $query->getQuery()->getResult();
    }

    ...
}
```

Change `PostController:viewAction`
```php
<?php
// src/AppBundle/Controller/PostController.php

...

class PostController extends AbstractController
{
    ...

    /**
     * @Template()
     */
    public function viewAction(Post $post)
    {
        /* @var $manager \AppBundle\Services\LaterPayManager */
        $manager = $this->container->get('app.laterpay');

        if ($this->getRequest()->get('buy')) {
            if (!$manager->hasAccess($post)) {
                $url = $manager->getPayUrl($post);
            } else {
                $url = $this->generateUrl('app_post_view', array('id' => $post->getId()));
            }

            return $this->redirect($url);
        }

        /* @var $repository \AppBundle\Entity\TimePassRepository */
        $repository = $this->getEntityRepository('AppBundle:TimePass');

        $timepasses = $repository->findByPost($post);

        $timepass = null;

        if (null !== ($code = $this->getRequest()->get('code'))) {

            foreach ($timepasses as $item) {

                if ($item->getVoucherCode() == $code) {

                    $timepass = $item;

                    $timepass
                        ->setPrice(0)
                    ;

                    break;
                }
            }

        }

        return array(
            'post'          => $post,
            'timepasses'    => $timepasses,
            'redeem'        => $timepass,
        );
    }
    ...
}
```

Change `Post/view.html.twig`
```twig
{# src/AppBundle/Resources/views/Post/view.html.twig #}

{% extends "AppBundle::layout.html.twig" %}

{% block content %}
    <div class="post-view">
        {{ post.publishedAt | date('Y-m-d H:i:s') }} by "{{ post.user.username }}", Category: {{ post.category.name }} <br />
        Title: {{ post.title }}<br />

        {% if LaterPay().hasAccess(post, timepasses) %}
            {{ post.content | nl2br }}
        {% else %}
            {{ post.teaserContent ?: post.truncatedContent | nl2br }}

            <a class="paylater" target="_blank" href="{{ LaterPay().getPayUrl(post) }}">
                {% if LaterPay().isPPU(post) %}
                    Read for {{ LaterPay().getPrice(post) }} and pay later
                {% else %}
                    Buy the full post with LaterPay for {{ LaterPay().getPrice(post) }}
                {% endif %}
            </a>

            <br />

            {% for timepass in timepasses %}
                <div class="timepass">
                    name: {{ timepass.name }}<br />
                    description: {{ timepass.description }}<br />

                    <a class="paylater" target="_blank" href="{{ LaterPay().getPayUrl(timepass) }}">
                        {% if LaterPay().isPPU(timepass) %}
                            Buy now for {{ LaterPay().getPrice(timepass) }} and pay later
                        {% else %}
                            Buy now with LaterPay for {{ LaterPay().getPrice(timepass) }}
                        {% endif %}
                    </a>
                </div>
            {% endfor %}

            {% if timepasses | length %}
                <div id="redeem-voucher">
                    <span>Redeem Voucher</span>
                    <form method="post">
                        <input type="text" required="required" name="code" placeholder="Code" />
                        <input type="submit" value="Redeem" />
                    </form>
                </div>
            {% endif %}
        {% endif %}

        {% set params = app.request.get('category_id') ? { 'category_id' : app.request.get('category_id')} : {} %}
        <p><a href="{{ path('app_posts', params) }}">Back to List</a></p>
    </div>
{% endblock %}
```


#### RSS feed

Change `Feed/post.html.twig`
```twig
{# src/AppBundle/Resources/views/Feed/post.html.twig #}
{% if post.price %}
    {{ post.teaserContent ?: post.truncatedContent }}<br>
    <a href="{{ url('app_post_view', { 'id' : post.id, 'buy' : true }) }}">Buy the full post with LaterPay for {{ LaterPay().getPrice(post) }}</a>
{% else %}
    {{ post.truncatedContent }}<br>
    <a href="{{ url('app_post_view', { 'id' : post.id }) }}">Read the full post</a>
{% endif %}

```


####Running the Symfony Application
```
$ php app/console server:run
```

Check admin endpoint: http://127.0.0.1:8000/admin/

Check frontend endpoint: http://127.0.0.1:8000/


Copyright
------------

Copyright 2015 LaterPay GmbH – Released under MIT License
