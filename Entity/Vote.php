<?php
namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bestbadtweets_vote")
 */
class Vote
{

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
     * @ORM\Column(type="integer")
     */
    protected $score = 0;
    
        public function getScore()
        {
            return $this->score;
        }
        
        public function setScore($score)
        {
            $this->score = $score;
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
     * @ORM\ManyToOne(targetEntity="Favorite")
     * @ORM\JoinColumn(name="favorite_id", referencedColumnName="id")
     */
    private $favorite;
        
        public function getFavorite()
        {
            return $this->favorite;
        }
        
        public function setFavorites($favorite)
        {
            $this->favorite = $favorite;
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
