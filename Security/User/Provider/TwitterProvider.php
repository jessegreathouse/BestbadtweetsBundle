<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Security\User\Provider;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Jessegreathouse\Bundle\BestbadtweetsBundle\Service\Tweeters;

use \TwitterOAuth as BaseTwitter;
use \TwitterApiException;

class TwitterProvider implements UserProviderInterface
{
    /**
     * @var \Twitter
     */
    protected $twitter;
    protected $userManager;
    protected $validator;
    protected $tweetersService;

    public function __construct(BaseTwitter $twitter, $userManager, $validator, Tweeters $tweetersService)
    {
        $this->twitter = $twitter;
        $this->userManager = $userManager;
        $this->validator = $validator;
        $this->tweetersService = $tweetersService;
    }

    public function supportsClass($class)
    {
        return $this->userManager->supportsClass($class);
    }

    public function findUserByTwitterID($twitterId)
    {
        return $this->userManager->findUserBy(array('twitterId' => $twitterId));
    }
    
    public function findUserByUsername($twitterId)
    {
        return $this->userManager->findUserBy(array('username' => $twitterId));
    }

    public function loadUserByUsername($username)
    {
        $user = $this->findUserByTwitterID($username);
        if (!$user || !$twitterdata = $user->getTwitterUser()->getTwitterUser()) {
            $twitterUser = $this->tweetersService->findByTwitterUserId($username);
            if (null == $twitterUser) {
                try {
                    $twitterdata = $this->twitter->get('users/show', array('id' => $username));
                } catch (TwitterApiException $e) {
                    $twitterdata = null;
                }
            }

            if ((null != $twitterUser) || (null != $twitterdata)) {
                $user = $this->userManager->createUser();
                $user->setEnabled(true);
                $user->setPassword('');
                $user->setAlgorithm('');
                if (null != $twitterUser) {
                    $user->setTwitterUser($twitterUser);
                } else {
                    $user->setTwitterUser($twitterdata);
                }

                if (count($this->validator->validate($user, 'Twitter'))) {
                    throw new UsernameNotFoundException('The twitter user could not be stored');
                }
                
                $this->userManager->updateUser($user);
            }
        }

        if (empty($user)) {
            throw new UsernameNotFoundException('The user is not authenticated on twitter');
        }

        return $this->userManager->refreshUser($user);
    }

    public function refreshUser(UserInterface $userObject)
    {
        if (!$this->supportsClass(get_class($userObject))) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($userObject)));
        }
        
        if (!$user = $this->findUserByUsername($userObject->getUsername())) {
            throw new \Exception(sprintf('User with id: "%s" not found.', $userObject->getUsername()));
        };

        return $this->loadUserByUsername($user->getTwitterID());
    }
}
