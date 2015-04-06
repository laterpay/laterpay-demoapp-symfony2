<?php

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
