<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\Category;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository
{
    /**
     * @param \AppBundle\Entity\Category $category
     * @param int $limit
     * @return array
     */
    public function getPublishedPosts(Category $category = null, $limit = 100)
    {
        $query = $this->createQueryBuilder('p');

        $query
            ->where('p.published_at IS NOT NULL')
            ->orderBy('p.published_at', 'desc')
            ->setMaxResults($limit)
        ;

        if ($category) {
            $query
                ->andWhere('p.category = :category')
                ->setParameter('category', $category)
            ;
        }

        return $query->getQuery()->getResult();
    }

}
