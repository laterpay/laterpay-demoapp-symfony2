<?php

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
        // TODO: move logick to LaterPay Client PHP
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
