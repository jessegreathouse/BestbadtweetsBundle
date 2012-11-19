<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Service;

use Symfony\Bridge\Monolog\Logger as Monolog;
use Doctrine\ORM\EntityManager;
use Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\Favorite;

class Suggestions
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
    protected $suggestionsRepo = null;

    public function __construct(EntityManager $em, Monolog $logger) 
    {
        $this->logger = $logger;
        $this->em = $em;
    }
    
    public function getSuggestionsRepo()
    {
        if (null === $this->suggestionsRepo) {
            $this->suggestionsRepo = $this->em->getRepository('BestbadtweetsBundle:Suggestion');
        }
        return $this->suggestionsRepo;
    }

    public function convertToFavorite($suggestion)
    {
        $favorite = new Favorite();
        $favorite->setTweet($suggestion->getTweet())
                 ->setSuggestedBy($suggestion->getSuggestedBy());
        $suggestion->setActive(0);
        $this->em->persist($favorite);
        $this->em->persist($suggestion);
        $this->em->flush();
        return $favorite;
    }
    
    public function findByDate($limit = 20, $offset = 0, $reverse = false, $date = null)
    {
                    
        $order = (!$reverse) ? 'ASC' : 'DESC';
        $q  = 'SELECT s ';
        $q .= 'FROM Jessegreathouse\\Bundle\\BestbadtweetsBundle\\Entity\\Suggestion s, ';
        $q .= 'Jessegreathouse\\Bundle\\BestbadtweetsBundle\\Entity\\Tweet t ';
        $q .= 'WHERE t.id = s.tweet ';
        $q .= 'AND s.active = 1 ';
        if ($date instanceof \DateTime) {
            $q .= 'AND t.createdDate > \'' . $date->format('Y-m-d H:i:s') . '\' ';
        }
        $q .= 'ORDER BY t.createdDate DESC';
        $query = $this->em->createQuery($q);
        $query->setMaxResults($limit)
              ->setFirstResult($offset);
        return $query->getResult();
    }
    
    public function findByTweetId($tweetId, $active = false)
    {
        $params = array('tweetId' => $tweetId);
        if (false !== $active) {
            $params['active'] = $active;
        }
        $suggestions =  $this->getSuggestionsRepo()
                           ->findBy($params);
                           
        if (count($suggestions)) {
            return $suggestions[0];
        } else {
            return false;
        }
    }

    public function findById($id)
    {
        $suggestion = ($result = $this->getSuggestionsRepo()->findById($id)) ? $result[0] : false;
        return $suggestion;
    }
}
