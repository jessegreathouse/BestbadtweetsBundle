<?php
namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Entity;

use Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\TwitterUser;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    
    public function __construct()
    {
        parent::__construct();
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $twitter_username;

        public function getTwitterUsername()
        {
            return $this->twitter_username;
        }
    
        public function setTwitterUsername($twitterUsername)
        {
            $this->twitter_username = $twitterUsername;
            return $this;
        }
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $twitterId;

        public function getTwitterId()
        {
            return $this->twitterId;
        }
        
        public function setTwitterId($twitterId)
        {
            $this->twitterId = $twitterId;
            $this->setUsername($twitterId);
            $this->salt = '';
            return $this;
        }
    
    /**
     * @ORM\ManyToMany(targetEntity="Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
    
        public function getGroups()
        {
            return $this->groups;
        }
    
        public function setGroups($groups)
        {
            $this->groups = $groups;
            return $this;
        }
    
    /**
     * @ORM\OneToOne(targetEntity="TwitterUser", mappedBy="user", cascade={"all"})
     */
    private $twitterUser;

        public function getTwitterUser()
        {
            return $this->twitterUser;
        }
    
        public function setTwitterUser($twitterUser)
        {
            if ($twitterUser instanceof TwitterUser) {
                $this->twitterUser = $twitterUser;
                $this->twitterUser->setUser($this);

                $tweeter = $twitterUser->getTwitterUser();
                $this->setTwitterId($tweeter->id_str);
                $this->setUsername($tweeter->id_str);
                $this->setTwitterUsername($twitterUser->screen_name);
                $this->setEmail('@' . $tweeter->screen_name);
                $this->addRole('ROLE_TWITTER');

                return $this;
            }

            if (isset($twitterUser->id_str)) {
                $this->setTwitterId($twitterUser->id_str);
                $this->setUsername($twitterUser->id_str);
                $this->addRole('ROLE_TWITTER');
            }

            if (isset($twitterUser->screen_name)) {
                $this->setEmail('@' . $twitterUser->screen_name);
                $this->setTwitterUsername($twitterUser->screen_name);
            }

            $t = new TwitterUser;
            $t->setUser($this);
            $t->setTwitterUser($twitterUser);
            $this->twitterUser = $t;

            return $this;
        }
}
