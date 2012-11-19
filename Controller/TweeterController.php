<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class TweeterController extends Controller
{
    public function indexAction()
    {   
        return $this->redirect($this->generateUrl('tweeters_latest'));
    }
    
    public function latestAction($span)
    { 
        if ($span == 't') {
            $date = new \DateTime(date('Y-m-d', strtotime("today")));
        } elseif ($span == 'w') {
            $date = new \DateTime(date('Y-m-d', strtotime("Sunday")));
        } else {
            $date = null;
        }
        
        $reverse = (null   != $this->get('request')->query->get('reverse')) ? true : false;
        $offset =  ($offset = $this->get('request')->query->get('offset')) ? $offset : 0;

        $users = array();
        foreach ($this->get('tweeters')->findByDate(20, $offset, $reverse, $date) as $favorite) {
            array_push($users, $favorite->getTweet()->user);
        }
        return $this->render('BestbadtweetsBundle:Default:users.html.twig', array(
            'users' => $users,
        ));
    }
    
    public function avgAction($span)
    { 
        if ($span == 't') {
            $date = new \DateTime(date('Y-m-d', strtotime("today")));
        } elseif ($span == 'w') {
            $date = new \DateTime(date('Y-m-d', strtotime("Sunday")));
        } else {
            $date = null;
        }
        
        $reverse = (null   != $this->get('request')->query->get('reverse')) ? true : false;
        $offset =  ($offset = $this->get('request')->query->get('offset')) ? $offset : 0;

        $users = array();
        foreach ($this->get('votes')->findByAvg(20, $offset, $reverse, $date) as $vote) {
            array_push($users, $vote[0]->getFavorite()->getTweet()->user);
        }
        return $this->render('BestbadtweetsBundle:Default:users.html.twig', array(
            'users' => $users,
        ));
    }
    
    public function scoreAction($span)
    {   
        if ($span == 't') {
            $date = new \DateTime(date('Y-m-d', strtotime("today")));
        } elseif ($span == 'w') {
            $date = new \DateTime(date('Y-m-d', strtotime("Sunday")));
        } else {
            $date = null;
        }
        
        $reverse = (null   != $this->get('request')->query->get('reverse')) ? true : false;
        $offset =  ($offset = $this->get('request')->query->get('offset')) ? $offset : 0;
       
        $users = array();
        foreach ($this->get('votes')->findByScore(20, $offset, $reverse, $date) as $vote) {
            array_push($users, $vote[0]->getFavorite()->getTweet()->user);
        }
        return $this->render('BestbadtweetsBundle:Default:users.html.twig', array(
            'users' => $users,
        ));
    }
}
