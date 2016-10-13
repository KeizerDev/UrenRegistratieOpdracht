<?php
namespace WellGedaan\UrenRegistratie\Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

class PageController extends BaseController implements ControllerProviderInterface
{

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'renderIndexPage']);

        return $controllers;
    }


    public function renderIndexPage(Application $app)
    {
        return $this->render('index.twig');
    }


}