<?php

namespace Itc\AdminBundle\Form\Gallery;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SmallImageType extends AbstractType {
   
    public function getName() {
        return 'itc_adminbundle_gallery_smallimagetype';
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add("x", "hidden", array("attr" => array("class" => "x")))
                ->add("y", "hidden", array("attr" => array("class" => "y")))
                ->add("w", "hidden", array("attr" => array("class" => "w")))
                ->add("h", "hidden", array("attr" => array("class" => "h")));
    }

}