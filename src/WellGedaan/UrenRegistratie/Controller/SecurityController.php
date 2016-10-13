<?php
namespace WellGedaan\UrenRegistratie\Controller;

use Doctrine\ORM\EntityManager;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

/**
 * Class SecurityController
 * @package WellGedaan\UrenRegistratie\Controller
 */
class SecurityController extends BaseController implements ControllerProviderInterface
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

        $controllers->get('/login', [$this, 'renderLoginPage']);
        $controllers->get('/register', [$this, 'renderRegisterPage']);

        return $controllers;
    }


    /**
     * Renders the login page
     * @param Application $app
     * @return string
     */
    public function renderLoginPage(Application $app)
    {
        //$this->initDoctrine($app['orm.em']);
        return $this->render('security/login.twig');
    }


    /**
     * renders the register page
     * @return string
     */
    public function renderRegisterPage()
    {
        return $this->render('security/register.twig');
    }


    /**
     * @param EntityManager $entityManager
     * generates database scheme / structure
     */
    public function initDoctrine(EntityManager $entityManager)
    {
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $classes = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->updateSchema($classes);
    }

}