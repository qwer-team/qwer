<?php

namespace Itc\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SearchMenuType extends AbstractType
{
    private $locale;
    public function __construct($locale)
    {
        $this->locale = $locale;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', 'text', array('required'=>NULL));
/*            ->add('tag', 'text', array('required'=>NULL))
            ->add('translit', 'text', array('required'=>NULL))
            ->add('id', 'integer', array('required'=>NULL))
            ->add('parent_id', 'integer', array('required'=>NULL))
            ->add('from', 'genemu_jquerydate', array(
            'widget' => 'single_text', 'required'=>NULL
        ))
            ->add('to', 'genemu_jquerydate', array(
            'widget' => 'single_text', 'required'=>NULL
        ));*/
    }


    public function getName()
    {
        return 'itc_adminbundle_searchmenutype';
    }
}
