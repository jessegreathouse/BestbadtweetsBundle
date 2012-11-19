<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class SuggestionController extends Controller
{
    public function indexAction($_format)
    {   
        $reverse = (null   != $this->get('request')->query->get('reverse')) ? true : false;
        $offset =  ($offset = $this->get('request')->query->get('offset')) ? $offset : 0;
        $limit =  ($limit = $this->get('request')->query->get('limit')) ? $limit : 20;
        $tweets = array();

        $suggestions = $this->get('suggestions')->findByDate($limit, $offset, $reverse);
        
        foreach ($suggestions as $suggestion) {
            array_push($tweets, $suggestion->getTweet()->getTweet());
        }

        return $this->render('BestbadtweetsBundle:Default:index.' . $_format . '.twig', array(
            'tweets' => $tweets,
        ));
    }

    public function approveAction($_format)
    {
        $action = array();
        
        if (!$this->get('request')->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('_homepage'));
        }

        $suggestion = $this->get('suggestions')->findById($this->get('request')->query->get('id'));
        $tweet = $suggestion->getTweet();
        $user = ($user = $this->get('security.context')->getToken()->getUser()) ? $user : null;
        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_SUPER_ADMIN', $roles)) {
            if (!$this->get('favorites')->findByTweetId($tweet->getTweetId())) {
                if ($favorite = $this->get('suggestions')->convertToFavorite($suggestion)) {
                    $action['message'] = 'Tweet Approved.';
                    $action['status'] = 'success';
                } else {
                    $action['message'] = 'Technical problem encountered.';
                    $action['status'] = 'fail';
                }
            } else {
                $action['message'] = 'Tweet already previously approved.';
                $action['status'] = 'success';
            }
        } else {
            $action['message'] = 'Insufficient Privelages to approve tweet.';
            $action['status'] = 'fail';
        }
        
        return $this->render('BestbadtweetsBundle:Tweet:approve.'. $_format . '.twig', array(
            'action' => $action,
        ));
    }
}
