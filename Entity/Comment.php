<?php
namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="bestbadtweets_comment")
 */
class Comment
{

    public function __construct() {
        $this->replies = new ArrayCollection;
        $this->commentVotes = new ArrayCollection;
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
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $content;
    
        public function getContent()
        {
            return $this->content;
        }
        
        public function setContent($content)
        {
            $this->content = $content;
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
     * @ORM\ManyToOne(targetEntity="User")
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
        
    /**
     * @ORM\ManyToOne(targetEntity="Tweet")
     * @ORM\JoinColumn(name="tweet_id", referencedColumnName="id")
     */
    private $tweet;

        public function getTweet()
        {
            return $this->tweet;
        }

        public function setTweet($tweet)
        {
            $this->tweet = $tweet;
            return $this;
        }
        

    /**
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="replies")
     * @ORM\JoinColumn(name="replyto_id", referencedColumnName="id")
     */
    private $replyTo;
        
        public function getReplyTo()
        {
            return $this->replyTo;
        }
        
        public function setReplyTo($replyTo)
        {
            $this->replyTo = $replyTo;
            return $this;
        }
        
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="replyTo")
     */
    private $replies;

        public function getReplies()
        {
            return $this->replies;
        }

        public function setTwitterUser($replies)
        {
            $this->replies = $replies;
            return $this;
        }
        
    /**
     * @ORM\OneToMany(targetEntity="CommentVote", mappedBy="comment")
     */
    private $commentVotes;
    
        public function getCommentVotes()
        {
            return $this->commentVotes;
        }
        
        public function setCommentVotes($commentVotes)
        {
            $this->commentVotes = $commentVotes;
            return $this;
        }
}
