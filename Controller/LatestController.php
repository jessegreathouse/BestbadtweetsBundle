<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class LatestController extends Controller
{
    public function indexAction($_format)
    {   
        $reverse = (null   != $this->get('request')->query->get('reverse')) ? true : false;
        $offset =  ($offset = $this->get('request')->query->get('offset')) ? $offset : 0;
        $limit =  ($limit = $this->get('request')->query->get('limit')) ? $limit : 20;
        $tweets = array();

        $favorites = $this->get('favorites')->findByDate($limit, $offset, $reverse);
        
        foreach ($favorites as $favorite) {
            array_push($tweets, $favorite->getTweet()->getTweet());
        }

        return $this->render('BestbadtweetsBundle:Default:index.' . $_format . '.twig', array(
            'tweets' => $tweets,
        ));
    }
}
