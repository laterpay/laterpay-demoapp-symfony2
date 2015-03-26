<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    /**
     * Declare bundle as a child of the FOSUserBundle so we can override the parent bundle's templates
     *
     * @return string
     */
    public function getParent()
    {
        return 'SonataUserBundle';
    }
}
