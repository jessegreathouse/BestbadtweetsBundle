<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    public function indexAction()
    {
        $twitterUserId = ($twitterUserId = $this->get('request')->query->get('id_str')) ? $twitterUserId : null;
        $twitterUser = ($twitterUser = $this->get('tweeters')->findByTwitterUserId($twitterUserId)) ? $twitterUser: null;
        if (null == $twitterUser) {
            throw new NotFoundHttpException('Twitter user not found');
        }
        $tweets = array();
        $favorites = ($favorites = $this->get('favorites')->findByTwitterUserId($twitterUserId)) ? $favorites : array();
        foreach ($favorites as $favorite) {
            array_push($tweets, $favorite->getTweet()->getTweet());
        }

        return $this->render('BestbadtweetsBundle:User:index.html.twig', array(
            'user'   => $twitterUser->getTwitterUser(),
            'tweets' => $tweets,
        ));
    }
}
