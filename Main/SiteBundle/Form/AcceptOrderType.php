<?php

namespace Main\SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\Collection;

use \Itc\AdminBundle\Tools\LanguageHelper;

class AcceptOrderType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options = NULL )
    {
        $builder
        
            ->add('fio',     null,      array('label' => 'enter_name:'))
            ->add('email',   null,      array('label' => 'enter_email:'))
            ->add('telefon', null,      array('label' => 'enter_phone:'))
            ->add('address', null,      array('label' => 'enter_address:'))
            ->add('body', 'textarea',   array('label' => 'enter_message:', "attr" => array("rows"=>"4", "cols"=>"79")))
            ->add('captcha', 'captcha', array( 'keep_value' => false, ) );
    }

    private function getLanguages()
    {
        $locale = LanguageHelper::getLocale();
        $lngs = LanguageHelper::getLanguages();
        /* @var $languages type */
        $languages = !\is_null($lngs)? $lngs: array($locale);
        return $languages;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            //'data_class' => 'Hansa\AdminBundle\Entity\Menu\Menu'
        ));
        $collectionConstraint = new Collection(array(
            'fio'   => new MinLength( 3 ),
            'telefon' => new MinLength( 5 ),
            'body'    => new MinLength( 10 ),
            'email' => new Email(array('message' => 'Invalid email address')),
        ));

        return array('validation_constraint' => $collectionConstraint);
    }

    public function getName()
    {
        return 'hansa2_sitebundle_acceptorder';
    }
}
