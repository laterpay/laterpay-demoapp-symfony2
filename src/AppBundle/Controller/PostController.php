<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Post;
use AppBundle\Entity\Category;

class PostController extends AbstractController
{
    /**
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getEntityManager();

        $category = null;

        if (null !== ($categoryId = $request->get('category_id'))) {
            $category = $em->getRepository('AppBundle:Category')->find($categoryId);
        }

        /* @var $repository \AppBundle\Entity\PostRepository */
        $repository = $em->getRepository('AppBundle:Post');

        $posts = $repository->getPublishedPosts($category);

        return array(
            'posts' => $posts,
        );
    }

    /**
     * @Template()
     */
    public function viewAction(Post $post)
    {
        return array(
            'post' => $post,
        );
    }
}
