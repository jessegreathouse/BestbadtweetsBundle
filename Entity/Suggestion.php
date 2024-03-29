<?php
namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bestbadtweets_suggestion")
 */
class Suggestion
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
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
    protected $twitterUserId;
    
        public function getTwitterUserId()
        {
            return $this->twitterUserId;
        }
        
        public function setTwitterUserId($twitterUserId)
        {
            $this->twitterUserId = $twitterUserId;
            return $this;
        }
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $active = 1;
    
        public function getActive()
        {
            return $this->active;
        }
        
        public function setActive($active)
        {
            $this->active = $active;
            return $this;
        }
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $collectedDate;
    
        public function getCollectedDate()
        {
            return $this->collectedDate;
        }
        
        public function setCollectedDate(\DateTime $collectedDate)
        {
            $this->collectedDate = $collectedDate;
            return $this;
        }
    
    /**
     * @ORM\OneToOne(targetEntity="Tweet",  cascade={"persist", "merge"})
     * @ORM\JoinColumn(name="tweet_id", referencedColumnName="id")
     */
    private $tweet;

        public function getTweet()
        {
            return $this->tweet;
        }

        public function setTweet($tweet)
        {
            
            $this->tweetId = $tweet->getTweet()->id_str;
            $this->twitterUserId = $tweet->getTweet()->user->id_str;
            $this->collectedDate = new \DateTime();
            $this->tweet = $tweet;
            return $this;
        }
        
    /**
     * @ORM\ManyToOne(targetEntity="TwitterUser",  cascade={"persist", "merge"})
     * @ORM\JoinColumn(name="suggester_twitter_user_id", referencedColumnName="id")
     */
    private $suggestedBy;

        public function getSuggestedBy()
        {
            return $this->suggestedBy;
        }

        public function setSuggestedBy($suggestedBy)
        {
            
            $this->suggestedBy = $suggestedBy;
            return $this;
        }
}
