<?php

namespace Itc\AdminBundle\Form\Translation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TranslationNamespaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bundle')
            ->add('namespace')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Itc\AdminBundle\Entity\Translation\TranslationNamespace'
        ));
    }
    
    public function getName()
    {
        return 'itc_adminbundle_translation_translationnamespacetype';
    }
    
}
