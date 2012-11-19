<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{

    public function loginAction()
    {
        $request    = $this->get('request');
        $attributes = $request->attributes;
        $session    = $request->getSession();
        
        $error = $attributes->has(SecurityContext::AUTHENTICATION_ERROR)
               ? $attributes->get(SecurityContext::AUTHENTICATION_ERROR)
               : $session->get(SecurityContext::AUTHENTICATION_ERROR);

        return $this->render('BestbadtweetsBundle:Security:login.html.twig', array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }

}

