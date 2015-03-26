<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CategoryController extends AbstractController
{
    /**
     * @Template()
     */
    public function listAction()
    {
        $em = $this->getEntityManager();

        $categories = $em->getRepository('AppBundle:Category')->findAll();

        return array(
            'categories'    => $categories,
        );
    }

}
