<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Service;

use Symfony\Bridge\Monolog\Logger as Monolog;
use Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\User;
use Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\Vote;
use Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\Favorite;
use Doctrine\ORM\EntityManager;

class Votes
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
     * @var $votesRepo
     */
    protected $votesRepo = null;

    public function __construct(EntityManager $em, Monolog $logger) 
    {
        $this->logger = $logger;
        $this->em = $em;
    }
    
    public function getVotesRepo()
    {
        if (null === $this->votesRepo) {
            $this->votesRepo = $this->em->getRepository('BestbadtweetsBundle:Vote');
        }
        return $this->votesRepo;
    }
    
    public function findByScore($limit = 20, $offset = 0, $reverse = false, $date = null)
    {
        $order = (!$reverse) ? 'ASC' : 'DESC';
        $q  = 'SELECT v, SUM(v.score) as score ';
        $q .= 'FROM Jessegreathouse\\Bundle\\BestbadtweetsBundle\\Entity\\Vote v ';
        if ($date instanceof \DateTime) {
            $q .= 'WHERE v.timestamp > \'' . $date->format('Y-m-d H:i:s') . '\' ';
        }
        $q .= 'GROUP BY v.favorite ';
        $q .= 'ORDER BY score DESC';
        $query = $this->em->createQuery($q);
        $query->setMaxResults($limit)
              ->setFirstResult($offset);
        return $query->getResult();
    }
    
    public function findByAvg($limit = 20, $offset = 0, $reverse = false, $date = null)
    {
        $order = (!$reverse) ? 'ASC' : 'DESC';
        $q  = 'SELECT SUM(v.score) as score, AVG(v.score) as average, v ';
        $q .= 'FROM Jessegreathouse\\Bundle\\BestbadtweetsBundle\\Entity\\Vote v ';
        if ($date instanceof \DateTime) {
            $q .= 'WHERE v.timestamp > \'' . $date->format('Y-m-d H:i:s') . '\' ';
        }
        $q .= 'GROUP BY v.favorite ';
        $q .= 'ORDER BY average DESC, score DESC';
        $query = $this->em->createQuery($q);
        $query->setMaxResults($limit)
              ->setFirstResult($offset);
        return $query->getResult();
    }
    
    public function findUserVote(Favorite $favorite, User $user)
    {
        $votes = $this->getVotesRepo()
                      ->findBy(array('favorite' => $favorite->getId(), 'user' => $user->getId()));
                      
        if (count($votes)) {
            return $votes[0];
        } else {
            return false;
        }
    }
    
    public function findFavoriteVotes(Favorite $favorite)
    {
        $votes = $this->getVotesRepo()
                      ->findBy(array('favorite' => $favorite->getId()));
                      
        if (count($votes)) {
            return $votes;
        } else {
            return false;
        }
    }
    
    public function findFavoriteScore(Favorite $favorite)
    {
        $query = $this->em->createQuery('SELECT SUM(v.score) FROM Jessegreathouse\\Bundle\\BestbadtweetsBundle\\Entity\\Vote v WHERE v.favorite = :favorite');
        $query->setParameters(array('favorite' => $favorite));
        return $query->getSingleScalarResult();
    }
    
    public function findFavoriteTotalVotes(Favorite $favorite)
    {
        $query = $this->em->createQuery('SELECT v.score FROM Jessegreathouse\\Bundle\\BestbadtweetsBundle\\Entity\\Vote v WHERE v.favorite = :favorite GROUP BY v.user');
        $query->setParameters(array('favorite' => $favorite));
        $result = $query->getResult();
        return count($result);
    }
    
    public function addVote(Favorite $favorite, User $user, $score)
    {
        if (!$vote = $this->findUserVote($favorite, $user)) {
            $vote = new Vote();
            $vote->setFavorites($favorite)
                 ->setUser($user);
        }
        $vote->setTimestamp(new \DateTime("now"))
             ->setScore($score);
        $this->em->persist($vote);
        $this->em->flush();
    }
    
}
