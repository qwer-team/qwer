<?php

namespace Main\SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',      null, array('label' => "email"))
            ->add('tel',        null, array('label' => "phone"))
            ->add('address',    null, array('label' => "address"))
            ->add('surname',    null, array('label' => "surname"))
            ->add('name',       null, array('label' => "name"))
            ->add('patronymic', null, array('label' => "patronymic"));

            if($options["attr"]["new"]){
                $builder->add('password', 'repeated', 
                    array(
                        'type' => 'password',
                        'first_options'  => array('label' => 'password'),
                        'second_options' => array('label' => 'password_confirmation'),
                    )
                )
                ->add('captcha', 'captcha', 
                        array('label'  => "captcha",
                              'width'  => 100,
                              'height' => 40,
                              'length' => 4,
                        )
                )
                ->add('username',   null, array('label' => "login"));
            }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Itc\AdminBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'hoffice_sitebundle_usertype';
    }
}
