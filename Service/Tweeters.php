<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Service;

use Symfony\Bridge\Monolog\Logger as Monolog;
use Doctrine\ORM\EntityManager;
use Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\Tweet;

class Tweeters
{
    
    /**
     * @var $em  EntityManager
     */
    protected $em;
    
    /**
     * @var $logger Monolog 
     */
    protected $logger;
    
    /**
     * @var $favoritesRepo
     */
    protected $favoritesRepo = null;
    
    /**
     * @var $votesRepo
     */
    protected $votesRepo = null;
    
    /**
     * @var $twitterUsersRepo
     */
    protected $twitterUsersRepo = null;

    public function __construct(EntityManager $em, Monolog $logger) 
    {
        $this->logger = $logger;
        $this->em = $em;
    }
    
    public function getFavoritesRepo()
    {
        if (null === $this->favoritesRepo) {
            $this->favoritesRepo = $this->em->getRepository('BestbadtweetsBundle:Favorite');
        }
        return $this->favoritesRepo;
    }
    
    public function getVotesRepo()
    {
        if (null === $this->votesRepo) {
            $this->votesRepo = $this->em->getRepository('BestbadtweetsBundle:Vote');
        }
        return $this->votesRepo;
    }
    
    public function getTwitterUsersRepo()
    {
        if (null === $this->twitterUsersRepo) {
            $this->twitterUsersRepo = $this->em->getRepository('BestbadtweetsBundle:TwitterUser');
        }
        return $this->twitterUsersRepo;
    }
    
    public function findByDate($limit = 20, $offset = 0, $reverse = false, $date = null)
    {
                    
        $order = (!$reverse) ? 'ASC' : 'DESC';
        $q  = 'SELECT f ';
        $q .= 'FROM Jessegreathouse\\Bundle\\BestbadtweetsBundle\\Entity\\Favorite f, ';
        $q .= 'Jessegreathouse\\Bundle\\BestbadtweetsBundle\\Entity\\Tweet t ';
        $q .= 'WHERE t.id = f.tweet ';
        if ($date instanceof \DateTime) {
            $q .= 'AND t.createdDate > \'' . $date->format('Y-m-d H:i:s') . '\' ';
        }
        $q .= 'GROUP BY f.twitterUserId ';
        $q .= 'ORDER BY t.createdDate DESC ';

        $query = $this->em->createQuery($q);
        $query->setMaxResults($limit)
              ->setFirstResult($offset);
        return $query->getResult();
    }
    
    public function findByTwitterUserId($twitterUserId)
    {
       $users = $this->getTwitterUsersRepo()
                    ->findBy(array('twitterUserId' => $twitterUserId));
                    
        if (count($users)) {
            return $users[0];
        } else {
            return false;
        }
    }
    
    public function findByScreenName($screenName)
    {
       $users = $this->getTwitterUsersRepo()
                    ->findBy(array('screenName' => $screenName));
                    
        if (count($users)) {
            return $users[0];
        } else {
            return false;
        }
    }
    
    
}
