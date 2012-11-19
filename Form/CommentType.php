<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilder $builder, Array $options)
    {
        $builder
            ->add('content', 'textarea', array(
                    'label' => 'Leave a comment',
            ))
            ->add('replyTo', 'entity_id', array( 
                  'required' => false, 
                  'class' => 'Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\Comment', 
                  'query_builder' => function(EntityRepository $repo, $id) { 
                        return $repo->createQueryBuilder('c') 
                                    ->where('c.id = :id') 
                                    ->setParameter('id', $id);
                    }
            ))
        ;    
    }
    
    public function getDefaultOptions(Array $options)
    {
        return array_merge($options, array(
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'bestbadtweets_comment';
    }

}
