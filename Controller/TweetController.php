<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;


class TweetController extends Controller
{
    public function indexAction()
    {
        $tweet = $this->get('tweets')->findByTweetId($this->get('request')->query->get('id_str'));
        if (NULL == $tweet) {
            throw new NotFoundHttpException('Tweet not found');
        }
        $suggestedBy = false;
        $suggestion = $this->get('suggestions')->findByTweetId($tweet->getTweetId(), 1);

        if (!$suggestion) {
            $formerSuggestion = $this->get('suggestions')->findByTweetId($tweet->getTweetId());
            if (false !== $formerSuggestion) {
                $suggestedBy = $formerSuggestion->getSuggestedBy()->getTwitterUser();
            }
        }

        return $this->render('BestbadtweetsBundle:Tweet:index.html.twig', array(
            'tweet'       => $tweet->getTweet(),
            'suggestion'  => $suggestion,
            'suggestedBy' => $suggestedBy,
        ));
    }
    
    public function voteAction($_format)
    {
        $vote = array();
        $tweet = $this->get('tweets')->findByTweetId($this->get('request')->query->get('id_str'));
        
        //if tweet isnt found
        //or if the request isnt ajax
        //redirect to homepage 
        if (NULL == $tweet || !$this->get('request')->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('_homepage'));
        }

        $favorite = $this->get('favorites')->findByTweetId($tweet->getTweet()->id_str);
        $user = ($user = $this->get('security.context')->getToken()->getUser()) ? $user : null;
        
        if ($favorite) {
            if (($score = $this->get('request')->query->get('score')) 
                && null !== $user && 'anon.' !== $user) {
                $this->get('votes')->addVote($favorite, $user, $score);
            }

            if (null === $user || 'anon.' === $user) {
                $vote['userScore'] = 0;
            } else {
                if ($userVote = $this->get('votes')->findUserVote($favorite, $user)) {
                    $vote['userScore'] = $userVote->getScore();
                } else {
                    $vote['userScore'] = 0;
                }
                
            }
            
            $vote['tweetScore'] = ($total = $this->get('votes')->findFavoriteScore($favorite)) ? $total : 0;
            $vote['tweetVotes'] = $this->get('votes')->findFavoriteTotalVotes($favorite);
            if ($vote['tweetVotes'] > 0) {
                $vote['tweetAvg'] = round(($vote['tweetScore']/$vote['tweetVotes']));
            } else {
                $vote['tweetAvg'] = 0;
            }
        }

        return $this->render('BestbadtweetsBundle:Tweet:vote.'. $_format . '.twig', array(
            'vote' => $vote,
        ));
    }
}
