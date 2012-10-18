<?php
namespace Sb\SendBoxBundle\Extension;

class TwigExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            'var_dump'   => new \Twig_Filter_Function('var_dump'),
        );
    }

    public function getName()
    {
        return 'twig_extension';
    }
}
