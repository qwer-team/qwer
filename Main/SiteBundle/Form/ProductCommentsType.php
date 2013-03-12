<?php

namespace Main\SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductCommentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment')
            //->add('data')
            //->add('lang')
            //->add('visible', null, array('required'=>NULL))
            //->add('prod_id')
            //->add('autor')
            //->add('product')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Itc\AdminBundle\Entity\Comments\ProductComments'
        ));
    }

    public function getName()
    {
        return 'main_sitebundle_comments_productcommentstype';
    }
}
