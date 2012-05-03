<?php

namespace Sb\SendBoxBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;


class MediaType extends AbstractType
{
    /**
    * @Recaptcha\True
    */
    public $recaptcha;
    
    public function buildForm(FormBuilder $builder, array $options)
    {
    }

    public function getName()
    {
        return 'sb_sendboxbundle_mediatype';
    }
}
