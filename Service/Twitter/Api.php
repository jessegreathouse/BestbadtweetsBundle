<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Service\Twitter;

use \TwitterOAuth;
use \TwitterApiException;

class Api 
{
    /**
     * @var $client \TwitterOAuth
     */
    var $client;
    
    /**
     * @var $account \stdClass
     */
    var $account;
    
    public function __construct($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret)
    {
        $this->client = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
        $this->account = $this->client->get('account/verify_credentials');
    }
    
    public function isVerified()
    {
        return (!isset($this->account->name) || NULL === $this->account->name) ? false : true;
    }
    
    public function getAccount()
    {
        return $this->account;
    }
    
    public function getFavorites($page = 1, $expanded = true)
    {
        return $this->client->get('favorites', array('include_entities' => $expanded, 'page' => $page));
    }
    
    public function getUserTimeline($screenName, $page = 1, $expanded = true)
    {
        return $this->client->get('statuses/user_timeline', array('screen_name' => $screenName, 'include_entities' => $expanded, 'page' => $page));
    }
    
    public function getMentions($page = 1, $expanded = true)
    {
        return $this->client->get('statuses/mentions', array('include_entities' => $expanded, 'page' => $page));
    }
    
    public function getTweet($tweetId, $expanded = true)
    {
        return $this->client->get('statuses/show/' . $tweetId, array('include_entities' => $expanded));
    }
    
    public function getRateLimitStatus()
    {
        return $this->client->get('account/rate_limit_status', array());
    }
}
