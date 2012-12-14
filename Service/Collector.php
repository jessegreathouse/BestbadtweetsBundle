<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Service;

use Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\Tweet;
use Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\Favorite;
use Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\TwitterUser;
use Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\Suggestion;
use Jessegreathouse\Bundle\BestbadtweetsBundle\Service\Twitter\Api as Twitter;
use Jessegreathouse\Yfrog\YfrogClient;
use Jessegreathouse\Yfrog\YfrogRequest;

use Symfony\Bridge\Monolog\Logger as Monolog;
use Doctrine\ORM\EntityManager;

class Collector
{
    /**
     * @var $twitter Twitter
     */
    private $twitter;

    /**
     * @var $yfrog YfrogClient
     */
    private $yfrog;
    
    /**
     * @var $em  EntityManager
     */
    private $em;
    
    /**
     * @var $logger Monolog 
     */
    private $logger;
    
    /**
     * @var $repo Array
     */
    private $repo;
     
     
    public function __construct(Twitter $twitter, YfrogClient $yfrog, EntityManager $em, Monolog $logger)
    {
        $this->twitter = $twitter;
        $this->yfrog = $yfrog;
        $this->em = $em;
        $this->logger = $logger;
        
        $this->repo = $this->loadRepositories();
    }
    
    public function collect()
    {
        $this->collectFavorites();
        $this->collectSystemMessages();
        $this->collectSuggestions();
    }
    
    public function loadRepositories()
    {
        return array(
            'faves'        => $this->em->getRepository('BestbadtweetsBundle:Favorite'),
            'tweets'       => $this->em->getRepository('BestbadtweetsBundle:Tweet'),
            'users'        => $this->em->getRepository('BestbadtweetsBundle:TwitterUser'),
            'suggestions'  => $this->em->getRepository('BestbadtweetsBundle:Suggestion'),
        );
    }
    
    public function getFavorites($page = 1)
    {
        return $this->twitter->getFavorites($page, false);
    }
    
    public function getUserTimeline($page = 1)
    {
        return $this->twitter->getUserTimeline('bestbadtweets', $page, false);
    }
    
    public function getMentions($page = 1)
    {
        return $this->twitter->getMentions($page, false);
    }
    
    public function getTweet($tweetId)
    {
        return $this->twitter->getTweet($tweetId, false);
    }
    
    public function addFave($tweet)
    {
        $f = new Favorite;
        $f->setTweet($tweet);
        $this->em->persist($f);
        $this->em->flush();
        $this->logger->info('Collector added new favorite by:"' . $tweet->getTweet()->user->screen_name 
                            . '" message:"' . $tweet->getTweet()->text . '"');
    }
    
    public function addSuggestion($tweet, $suggestedBy)
    {
        $s = new Suggestion;
        $s->setTweet($tweet);
        $s->setSuggestedBy($suggestedBy);
        $this->em->persist($s);
        $this->em->flush();
        $this->logger->info('Collector added new suggestion by:"' . $suggestedBy->getTwitterUser()->screen_name 
                            . '" message:"' . $tweet->getTweet()->text . '"');
    }
    
    public function addTweet($tweet, $user)
    {
        $t = new Tweet;
        $tweet->text = $this->saveMedia($tweet->text);
        $t->setTweet($tweet);
        $t->setTwitterUser($user);
        $this->em->persist($t);
        $this->em->flush();
        $this->logger->info('Collector added new tweet by:"' . $tweet->user->screen_name 
                            . '" message:"' . $tweet->text . '"');
        return $t;
    }

    public function saveMedia($text)
    {
        $matches = array();

        $text = preg_replace_callback(
            "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/",
            array(&$this, 'saveMediaBridge'),
            $text
        );

        return $text;
    }

    public function saveMediaBridge(array $matches)
    {
        return $this->swapMedia($matches[0]);
    }

    public function swapMedia($url)
    {
        $imageUrl = false;
        $checkImg = getimagesize($url);

        if (is_array($checkImg)) {
            $imageUrl = $url;
        } else {
            //
        }

        if (false !== $imageUrl) {
            $yfrogRequest = new YfrogRequest();
            $yfrogRequest->setUrl($imageUrl);
            $yfrog = $this->yfrog->transload($yfrogRequest);
            $url = $yfrog->getMediaUrl();
        }

        return $url;
    }
    
    public function addUser($user)
    {
        $u = new TwitterUser;
        $u->setTwitterUser($user);
        $this->em->persist($u);
        $this->em->flush();
        $this->logger->info('Collector added new user :"' . $user->screen_name . '"');
        return $u;
    }
    
    public function updateUser($user, $u)
    {
        
        $u->setTwitterUser($user);
        $this->em->persist($u);
        $this->em->flush();
        $this->logger->info('Collector updated user :"' . $user->screen_name . '"');
        return $u;
    }
    
    public function collectFavorites()
    {
        $new = 1;
        $page = 1;
        while($new) {
            $new = 0;
            $favorites = $this->getFavorites($page);
            
            foreach ($favorites as $favorite) {   
                if((is_object($favorite)) && ($favorite->id_str !== null) &&
                   (!$this->repo['faves']->findOneBy(array('tweetId' => $favorite->id_str)))) {
                    $new = 1;
                    
                    //if the twitter user doesnt exist create one
                    //if it does, update with all current user info
                    if (!$u = $this->repo['users']->findOneBy(array('twitterUserId' => $favorite->user->id_str))) {
                        $u = $this->addUser($favorite->user);
                    } else {
                        $this->updateUser($favorite->user, $u);
                    }
                    
                    //add the tweet to the database
                    if (!$t = $this->repo['tweets']->findOneBy(array('tweetId' => $favorite->id_str))) {
                        $t = $this->addTweet($favorite, $u);
                    }
                    
                    //add favorite
                    $this->addFave($t);
                    unset($t);
                    unset($u);
                }
            }
            $page ++;
        }
        
    }
    
    public function collectSystemMessages()
    {
        $new = 1;
        $page = 1;
        while($new) {
            $new = 0;
            $tweets = $this->getUserTimeline($page);
            
            foreach ($tweets as $tweet) {   
                if((is_object($tweet)) && ($tweet->id_str !== null) &&
                   (!$this->repo['tweets']->findOneBy(array('tweetId' => $tweet->id_str)))) {
                    $new = 1;

                    if (!$u = $this->repo['users']->findOneBy(array('twitterUserId' => $tweet->user->id_str))) {
                        $u = $this->addUser($tweet->user);
                    } else {
                        $this->updateUser($tweet->user, $u);
                    }
                    
                    $this->addTweet($tweet, $u);
                    unset($u);
                }
            }
            $page ++;
        }
    }

    public function collectSuggestions()
    {
        $new = 1;
        $page = 1;
        while($new) {
            $new = 0;
            $mentions = $this->getMentions($page);
            
            foreach ($mentions as $mention) {
                if((is_object($mention)) && (NULL != $mention->in_reply_to_status_id_str)) {
                    if (!$t = $this->repo['tweets']->findOneBy(array('tweetId' => $mention->in_reply_to_status_id_str))) {
                        $tweet = $this->getTweet($mention->in_reply_to_status_id_str);
                        if (property_exists($tweet , 'user')) {
                            if (!$u = $this->repo['users']->findOneBy(array('twitterUserId' => $tweet->user->id_str))) {
                                $u = $this->addUser($tweet->user);
                            } else {
                                $this->updateUser($tweet->user, $u);
                            }
                            
                            $t = $this->addTweet($tweet, $u);
                        } else {
                            continue;
                        }
                    }
                    $suggestions =  $this->repo['suggestions']->findBy(array('tweetId' => $t->getTweetId()));
                    if (count($suggestions) < 1) {
                        $new = 1;
                        if (!count($s = $this->repo['users']->findBy(array('twitterUserId' => $mention->user->id_str)))) {
                            $s = $this->addUser($mention->user);
                        } else {
                            $s = $this->updateUser($mention->user, $s[0]);
                        }
                        //add favorite
                        $this->addSuggestion($t, $s);
                        unset($s);
                        unset($t);
                        unset($u);
                    }
                }
            }
            $page ++;
        }
    }
}
