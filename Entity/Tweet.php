<?php
namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bestbadtweets_tweet")
 */
class Tweet
{
    
    //magic for extrapolating raw tweet data
    public function __get($value) 
    {
        return (isset($this->getTweet()->$value)) ? $this->getTweet()->$value : null;
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;
        
        public function getId()
        {
            return $this->id;
        }
        
        public function setId($id)
        {
            $this->id = $id;
            return $this;
        }
    
    /**
     * @ORM\Column(type="string")
     */
    protected $tweetId;
    
        public function getTweetId()
        {
            return $this->tweetId;
        }
        
        public function setTweetId($tweetId)
        {
            $this->tweetId = $tweetId;
            return $this;
        }
        
    /**
     * @ORM\Column(type="string")
     */
    protected $tweetText;
    
        public function getTweetText()
        {
            return $this->tweetText;
        }
        
        public function setTweetText($tweetText)
        {
            $this->tweetText = $tweetText;
            return $this;
        }
        
    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdDate;
    
        public function getCreatedDate()
        {
            return $this->createdDate;
        }
        
        public function setCreatedDate($createdDate)
        {
            $this->createdDate = $createdDate;
            return $this;
        }
    
    /**
     * @ORM\Column(type="text")
     */
    protected $tweet;
    
        public function getTweet()
        {
            return unserialize(base64_decode($this->tweet));
        }
        
        public function setTweet($tweet)
        {
            $this->tweetId = $tweet->id_str;
            $this->tweetText = $tweet->text;
            $this->createdDate = new \DateTime($tweet->created_at);
            $this->tweet = base64_encode(serialize($tweet));
            return $this;
        }
    
    /**
     * @ORM\ManyToOne(targetEntity="TwitterUser")
     * @ORM\JoinColumn(name="twitter_user_id", referencedColumnName="id")
     */
    private $twitterUser;

        public function getTwitterUser()
        {
            return $this->twitterUser;
        }

        public function setTwitterUser($twitterUser)
        {
            $this->twitterUser = $twitterUser;
            return $this;
        }
    
    public function __toString()
    {
        return $this->getTweetId();
    }
}
