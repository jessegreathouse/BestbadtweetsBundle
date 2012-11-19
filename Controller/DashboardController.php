<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;


class DashboardController extends Controller
{
    public function indexAction()
    {
        return $this->render('BestbadtweetsBundle:Dashboard:index.html.twig', array());
    }
    
    public function systemMessageAction($_format)
    {
        $screenName = $this->container->getParameter('bestbadtweets_twitter_login');
        $tweets = $this->get('tweets')->findByScreenName('bestbadtweets', 1);
        if (!$tweets) {
            $tweet = null;
        } else {
            $tweet = $tweets[0]->getTweet();
            $tweet->created_at = date("c", strtotime($tweet->created_at));
        }
        
        return $this->render('BestbadtweetsBundle:Dashboard:system_message.'. $_format . '.twig', array(
            'message' => $tweet,
        ));
    }
}
