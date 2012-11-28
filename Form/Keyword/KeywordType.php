<?php

namespace Itc\AdminBundle\Form\Keyword;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Itc\AdminBundle\Tools\LanguageHelper;

class KeywordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $languages = $this->getLanguages();
        
        foreach( $languages as $k => $lang ){
            $builder->add( $lang.'Translation.keyword', null,  
                            array("label" => "Keyword") );
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Itc\AdminBundle\Entity\Keyword\Keyword'
        ));
    }
    
    private function getLanguages()
    {
        $locale = LanguageHelper::getLocale();
        $lngs = LanguageHelper::getLanguages();
        /* @var $languages type */
        $languages = !\is_null($lngs)? $lngs: array($locale);
        return $languages;
    }

    public function getName()
    {
        return 'itc_adminbundle_keyword_keywordtype';
    }
}
