<?php

namespace Itc\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('id', NULL, array('disabled'=>true))
            //->add('date_registrate', 'date', array('widget' => 'single_text', 'disabled'=>true))
            ->add('username')
            ->add( 'fio' )
            ->add( 'status' )
            ->add('email')
            ->add('tel')
            ->add('address')
            ->add('password', 'repeated', array(
                'type' => 'password',
                'first_options' => array('label' => 'password'),
                'second_options' => array('label' => 'password_confirmation'),
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Itc\AdminBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'itc_adminbundle_usertype';
    }
}
