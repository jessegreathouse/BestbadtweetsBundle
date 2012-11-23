<?php
namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bestbadtweets_twitter_user")
 */
class TwitterUser
{
    
    //magic for extrapolating raw tweet data
    public function __get($value) 
    {
        $this->tweets = new ArrayCollection;
        return (isset($this->getTwitterUser()->$value)) ?$this->getTwitterUser()->$value : null;
    }

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
     * @ORM\Column(type="string")
     */
    protected $screenName;
    
        public function getScreenName()
        {
            return $this->screenName;
        }
        
        public function setScreenName($screenName)
        {
            $this->screenName = $screenName;
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
    protected $twitterUser;
    
        public function getTwitterUser()
        {
            return unserialize(base64_decode($this->twitterUser));
        }
        
        public function setTwitterUser($twitterUser)
        {
            $this->twitterUserId = $twitterUser->id_str;
            $this->screenName = $twitterUser->screen_name;
            $this->createdDate = new \DateTime($twitterUser->created_at);
            $this->twitterUser = base64_encode(serialize($twitterUser));
            return $this;
        }
    
    /**
     * @ORM\OneToMany(targetEntity="Tweet", mappedBy="twitterUser")
     */
    private $tweets;

        public function getTweets()
        {
            return $this->tweets;
        }

        public function setTweets($tweets)
        {
            $this->tweets = $tweets;
            return $this;
        }
    
    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="twitterUser", cascade={"all"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
        public function getUser()
        {
            return $this->user;
        }

        public function setUser($user)
        {
            $this->user = $user;
            return $this;
        }
}
