<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Post;
use AppBundle\Entity\Category;

use AppBundle\Feed\Post as PostFeedItem;

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
     * @return Response XML Feed
     */
    public function feedAction()
    {
        $em = $this->getEntityManager();

        $posts = $em->getRepository('AppBundle:Post')->getPublishedPosts(null, 10);

        $feed = $this->get('eko_feed.feed.manager')->get('posts');

        foreach ($posts as $post) {

            /* @var $post \AppBundle\Entity\Post */

            $item = new PostFeedItem(
                $post->getTitle(),
                $this->renderView('AppBundle:Feed:post.html.twig', array('post' => $post)),
                $this->generateUrl('app_post_view', array('id' => $post->getId()), true),
                $post->getPublishedAt()
            );

            $feed->add($item);
        }

        return new Response($feed->render('rss'));
    }

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
}
