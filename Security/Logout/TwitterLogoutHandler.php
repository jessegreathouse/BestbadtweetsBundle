<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Security\Logout;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use \TwitterOAuth as BaseTwitter;

/**
 * Handler for clearing twitter session
 */
class TwitterLogoutHandler implements LogoutHandlerInterface
{
    /**
     * @var \Twitter
     */
    protected $twitter;

    public function __construct(BaseTwitter $twitter)
    {
        $this->twitter = $twitter;
    }

    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        //There is no way to log a user out from twitter yet. So this will return null until the rest api supports it
        //https://groups.google.com/forum/#!msg/twitter-development-talk/PH5HfT7SJqw/rtEzx-jrDQIJ
        return null;
    }
}
