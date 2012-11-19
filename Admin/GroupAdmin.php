<?php
namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class GroupAdmin extends Admin
{
    protected $list = array(
        'name' => array('identifier' => true),
        'roles'
    );

    protected $form = array(
        'name',
    );

    public function getNewInstance()
    {
        $class = $this->getClass();

        return new $class('', array());
    }

    public function configureFormFields(FormMapper $formMapper)
    {
    
        $formMapper->addType('roles', 'sonata_security_roles', array(
            'multiple' => true,
//            'expanded' => true,
        ), array(
            'type' => 'choice'
        ));
    }
}
