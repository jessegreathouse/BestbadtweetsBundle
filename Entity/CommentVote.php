<?php
namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bestbadtweets_comment_vote")
 */
class CommentVote
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
     * @ORM\Column(type="datetime")
     */
    protected $timestamp;
    
        public function getTimestamp()
        {
            return $this->timestamp;
        }
        
        public function setTimestamp($timestamp)
        {
            $this->timestamp = $timestamp;
            return $this;
        }
    
    /**
     * @ORM\ManyToOne(targetEntity="Comment")
     * @ORM\JoinColumn(name="comment_id", referencedColumnName="id")
     */
    private $comment;
        
        public function getComment()
        {
            return $this->comment;
        }
        
        public function setComment($comment)
        {
            $this->comment = $comment;
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

}
