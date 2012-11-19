<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Controller;

use Jessegreathouse\Bundle\BestbadtweetsBundle\Form\CommentType;
use Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\Comment;
use Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\CommentVote;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentController extends Controller
{
    public function indexAction()
    {   
        $request = $this->getRequest();
        $tweetId = $this->get('request')->query->get('id_str');
        $tweet = $this->get('tweets')->findByTweetId($tweetId);
        if (null == $tweet) {
            throw new NotFoundHttpException('Tweet not found');
        }
        
        $reverse = (null   != $request->query->get('reverse')) ? true : false;
        $offset =  ($offset = $request->query->get('offset')) ? $offset : 0;
        $user = ($user = $this->get('security.context')->getToken()->getUser()) ? $user : null;

        $entity = new Comment();
        $form   = $this->createForm(new CommentType(), $entity);

        if (null !== $user && 'POST' === $request->getMethod()) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $entity->setCreatedDate(new \DateTime("now"));
                $entity->setUser($user);
                $entity->setTweet($tweet);
                $em->persist($entity);
                $em->flush();
            }
        }
        
        $comments = $this->get('comments')->findPrimeByTweet($tweet, 20, $offset, $reverse);
        $params = array('id_str' => $tweetId);
        if (0 !== $offset) { $params['offset'] = $offset; }
        if (false !== $reverse) { $params['reverse'] = $reverse; }

        return $this->render('BestbadtweetsBundle:Comment:index.html.twig', array(
            'comments'    => $comments,
            'form_action' => $this->generateUrl('comments', $params),
            'form'        => $form->createView(),
            'tweetId'     => $tweetId,
        ));
    }
    
    public function recentAction($_format)
    {
        $reverse = (null   != $this->get('request')->query->get('reverse')) ? true : false;
        $offset =  ($offset = $this->get('request')->query->get('offset')) ? $offset : 0;
        $limit =  ($limit = $this->get('request')->query->get('limit')) ? $limit : 20;
       
        $result = $this->get('comments')->findByRecent($limit, $offset, $reverse);
        if (false !== $result) {
            $comments = array();
            foreach ($result as $c) {
                $comment = array();
                $comment['content'] = $c->getContent();
                $comment['created'] = $c->getcreatedDate()->format("c");
                $comment['user'] = $c->getUser()->getTwitterUser()->getTwitterUser();
                $comment['tweet'] = $c->getTweet()->getTweet();
                array_push($comments, $comment);
            }
        } else {
            $comments = null;
        }

        return $this->render('BestbadtweetsBundle:Comment:recent.' . $_format . '.twig', array(
            'comments' => $comments,
        ));
    }
    
    public function likeAction($_format)
    {
        $comment = $this->get('comments')->findById($this->get('request')->query->get('comment_id'));
        $user = ($user = $this->get('security.context')->getToken()->getUser()) ? $user : null;

        if (null == $user || null == $comment || !$this->get('request')->isXmlHttpRequest()) {
            return $this->render('BestbadtweetsBundle:Comment:like.'. $_format . '.twig', array(
                'like' => array(
                    'result' => 'fail'
                ),
            ));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $user_id = $user->getId();
        foreach ($comment->getCommentVotes() as $commentVote) {
            if ($commentVote->getUser()->getId() == $user_id) {
                $vote = $commentVote;
                break;
            }
        }
        if (isset($vote)) {
            $action = 'remove';
            $comment->getCommentVotes()->remove($vote->getId());
            $em->remove($vote);
        } else {
            $action = 'add';
            $vote = new CommentVote();
            $vote->setUser($user)
                ->setComment($comment)
                ->setTimestamp(new \DateTime("now"));
            $comment->getCommentVotes()->add($vote);
            $em->persist($vote);
        }
        $em->persist($comment);
        $em->flush();
        $em->refresh($comment);
        
        return $this->render('BestbadtweetsBundle:Comment:like.'. $_format . '.twig', array(
            'like' => array(
                'result' => 'success',
                'action' => $action,
                'commentVoteCount' => (count($comment->getCommentVotes())),
            ),
        ));
    }
}
