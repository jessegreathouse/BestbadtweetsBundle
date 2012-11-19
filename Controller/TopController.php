<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class TopController extends Controller
{
    public function indexAction()
    {   
        return $this->redirect($this->generateUrl('top_score'));
    }
    
    public function avgAction($span)
    { 
        if ($span == 't') {
            $date = new \DateTime(date('Y-m-d', strtotime("today")));
        } elseif ($span == 'w') {
            $date = new \DateTime(date('Y-m-d', strtotime("last Sunday")));
        } else {
            $date = null;
        }
        
        $reverse = (null   != $this->get('request')->query->get('reverse')) ? true : false;
        $offset =  ($offset = $this->get('request')->query->get('offset')) ? $offset : 0;
        $limit =  ($limit = $this->get('request')->query->get('limit')) ? $limit : 20;

        $tweets = array();
        foreach ($this->get('votes')->findByAvg($limit, $offset, $reverse, $date) as $vote) {
            array_push($tweets, $vote[0]->getFavorite()->getTweet()->getTweet());
        }
        return $this->render('BestbadtweetsBundle:Default:index.html.twig', array(
            'tweets' => $tweets,
        ));
    }
    
    public function scoreAction($_format, $span)
    {   
        if ($span == 't') {
            $date = new \DateTime(date('Y-m-d', strtotime("today")));
        } elseif ($span == 'w') {
            $date = new \DateTime(date('Y-m-d', strtotime("last Sunday")));
        } else {
            $date = null;
        }
        
        $reverse = (null   != $this->get('request')->query->get('reverse')) ? true : false;
        $offset =  ($offset = $this->get('request')->query->get('offset')) ? $offset : 0;
        $limit =  ($limit = $this->get('request')->query->get('limit')) ? $limit : 20;
       
        $tweets = array();
        foreach ($this->get('votes')->findByScore($limit, $offset, $reverse, $date) as $vote) {
            array_push($tweets, $vote[0]->getFavorite()->getTweet()->getTweet());
        }
        return $this->render('BestbadtweetsBundle:Default:index.' . $_format . '.twig', array(
            'tweets' => $tweets,
        ));
    }
}
