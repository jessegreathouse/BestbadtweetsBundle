<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    public function indexAction()
    {   
        return $this->redirect($this->generateUrl('latest'));
    }

    public function helpAction()
    {   
        return $this->render('BestbadtweetsBundle:Default:help.html.twig', array());
    }

    public function privacyAction()
    {   
        return $this->render('BestbadtweetsBundle:Default:privacy.html.twig', array());
    }

    public function aboutAction()
    {   
        return $this->render('BestbadtweetsBundle:Default:about.html.twig', array());
    }
}
