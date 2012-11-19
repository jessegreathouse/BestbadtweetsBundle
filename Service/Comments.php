<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Service;

use Symfony\Bridge\Monolog\Logger as Monolog;
use Doctrine\ORM\EntityManager;

class Comments
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
    protected $commentsRepo = null;

    public function __construct(EntityManager $em, Monolog $logger) 
    {
        $this->logger = $logger;
        $this->em = $em;
    }
    
    public function getCommentsRepo()
    {
        if (null === $this->commentsRepo) {
            $this->commentsRepo = $this->em->getRepository('BestbadtweetsBundle:Comment');
        }
        return $this->commentsRepo;
    }
    
    public function findByRecent($limit = 20, $offset = 0, $reverse = false)
    {
        $order = (!$reverse) ? 'DESC' : 'ASC';
        $result = $this->getCommentsRepo()->findBy(array('active' => 1), array('createdDate' => $order), $limit, $offset);
        if (count($result) < 1) {
            return false;
        } else {
            return $result;
        }
    }
    
    public function findPrimeByTweet($tweet, $limit = 20, $offset = 0, $reverse = false, $date = null)
    {
        $order = (!$reverse) ? 'DESC' : 'ASC';
        $query = $this->getCommentsRepo()->createQueryBuilder('c')
                    ->where('c.replyTo is NULL')
                    ->andWhere('c.tweet = :tweet')
                    ->orderBy('c.createdDate', $order)
                    ->setParameter('tweet', $tweet)
                    ->getQuery();

        $query->setMaxResults($limit)
              ->setFirstResult($offset);
        $result = $query->getResult();
                    
        if (count($result) < 1) {
            return false;
        } else {
            return $result;
        }
    }
    
    public function findById($id)
    {
        $comment = ($result = $this->getCommentsRepo()->findById($id)) ? $result[0] : false;
        return $comment;
    }
    
    
}
