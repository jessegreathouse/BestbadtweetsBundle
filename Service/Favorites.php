<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Service;

use Symfony\Bridge\Monolog\Logger as Monolog;
use Doctrine\ORM\EntityManager;
use Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\Tweet;

class Favorites
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
     * @var $favorites
     */
    protected $favoritesRepo = null;

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
        $q .= 'ORDER BY t.createdDate DESC';
        $query = $this->em->createQuery($q);
        $query->setMaxResults($limit)
              ->setFirstResult($offset);
        return $query->getResult();
    }
    
    public function findByQuery($text, $limit = 20, $offset = 0, $reverse = false, $date = null)
    {
        $order = (!$reverse) ? 'ASC' : 'DESC';
        $q  = 'SELECT f ';
        $q .= 'FROM Jessegreathouse\\Bundle\\BestbadtweetsBundle\\Entity\\Favorite f, ';
        $q .= 'Jessegreathouse\\Bundle\\BestbadtweetsBundle\\Entity\\Tweet t ';
        $q .= 'WHERE t.id = f.tweet ';
        $q .= 'AND t.tweetText LIKE \'%' . $text . '%\' ';
        $q .= 'ORDER BY t.createdDate DESC';
        $query = $this->em->createQuery($q);
        $query->setMaxResults($limit)
              ->setFirstResult($offset);
        return $query->getResult();
    }
    
    public function findByTweetId($tweetId)
    {
        $favorites =  $this->getFavoritesRepo()
                           ->findBy(array('tweetId' => $tweetId));
                           
        if (count($favorites)) {
            return $favorites[0];
        } else {
            return false;
        }
    }
    
    public function findByTwitterUserId($twitterUserId)
    {
        $favorites =  $this->getFavoritesRepo()
                           ->findBy(array('twitterUserId' => $twitterUserId), array('collectedDate' => 'DESC'), 20, 0);
                           
        if (count($favorites)) {
            return $favorites;
        } else {
            return false;
        }
    }
}
