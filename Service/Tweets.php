<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Service;

use Symfony\Bridge\Monolog\Logger as Monolog;
use Doctrine\ORM\EntityManager;

class Tweets
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
     * @var $tweets
     */
    protected $tweetsRepo = null;

    public function __construct(EntityManager $em, Monolog $logger) 
    {
        $this->logger = $logger;
        $this->em = $em;
    }
    
    public function getTweetsRepo()
    {
        if (null === $this->tweetsRepo) {
            $this->tweetsRepo = $this->em->getRepository('BestbadtweetsBundle:Tweet');
        }
        return $this->tweetsRepo;
    }
    
    public function findByTweetId($tweetId)
    {
        return $this->getTweetsRepo()
                    ->findOneBy(array('tweetId' => $tweetId));
    }
    
    public function findByScreenName($screenName, $limit = 20, $offset = 0, $reverse = false)
    {
        $order = (!$reverse) ? 'ASC' : 'DESC';
        $q  = 'SELECT t ';
        $q .= 'FROM Jessegreathouse\\Bundle\\BestbadtweetsBundle\\Entity\\Tweet t, ';
        $q .= 'Jessegreathouse\\Bundle\\BestbadtweetsBundle\\Entity\\TwitterUser u ';
        $q .= 'WHERE u.screenName = \'' . $screenName . '\' ';
        $q .= 'AND t.twitterUser = u ';
        $q .= 'ORDER BY t.createdDate ' . $order;
        $query = $this->em->createQuery($q);
        $query->setMaxResults($limit)
              ->setFirstResult($offset);
        $results = $query->getResult();
        
        if (count($results)) {
            return $results;
        } else {
            return false;
        }
    }
    
    
}
