<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class SearchController extends Controller
{
    public function indexAction()
    {   
        $reverse = (null   != $this->get('request')->query->get('reverse')) ? true : false;
        $offset =  ($offset = $this->get('request')->query->get('offset')) ? $offset : 0;
        $q =       ($q = $this->get('request')->query->get('q')) ? $q : null;
        $tweets = array();

        $favorites = $this->get('favorites')->findByQuery($q, 20, $offset, $reverse);
        
        foreach ($favorites as $favorite) {
            array_push($tweets, $favorite->getTweet()->getTweet());
        }
        
        return $this->render('BestbadtweetsBundle:Default:index.html.twig', array(
            'q'      => $q,
            'tweets' => $tweets,
        ));
    }
}
