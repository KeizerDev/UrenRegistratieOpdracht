<?php
namespace  WellGedaan\UrenRegistratie\Controller;

/**
 * Class BaseController
 * @package WellGedaan\UrenRegistratie\Controller
 */
class BaseController 
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }


    public function render($template, $args = [])
    {
        return $this->twig->render($template, $args);
    }

}