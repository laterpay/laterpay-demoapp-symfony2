<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * AbstractController
 */
class AbstractController extends Controller
{
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param string $name
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getEntityRepository($name)
    {
        return $this->getEntityManager()->getRepository($name);
    }
}
