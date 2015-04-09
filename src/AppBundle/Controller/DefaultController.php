<?php

namespace AppBundle\Controller;

class DefaultController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('app_posts'));
    }
}
