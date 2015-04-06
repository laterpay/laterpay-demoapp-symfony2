<?php

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

        // TODO: move parameter name to LaterPay Client PHP
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
