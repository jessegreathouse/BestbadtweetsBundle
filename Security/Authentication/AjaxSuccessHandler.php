<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Security\Authentication;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AjaxSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();

        return new Response(json_encode(array(
            'email'     => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname'  => $user->getLastname(),
        )), 200, array('Content-Type', 'application/json'));
    }
}
