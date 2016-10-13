<?php
namespace WellGedaan\UrenRegistratie\Controller;

use Doctrine\ORM\EntityManager;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WellGedaan\UrenRegistratie\Manager\UserManager;
use WellGedaan\UrenRegistratie\Model\User;

/**
 * Class SecurityController
 * @package WellGedaan\UrenRegistratie\Controller
 */
class SecurityController extends BaseController implements ControllerProviderInterface
{


    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(\Twig_Environment $twig, UserManager $userManager, EntityManager $entityManager)
    {
        parent::__construct($twig);
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
    }

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

        $controllers->post('/register_check', [$this, 'registerUserFromRequest']);

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
     * handles the register request
     * @param Request $request
     * @return Response
     */
    public function registerUserFromRequest(Request $request)
    {
        $username = $request->get('_username');
        $password = $request->get('_password');
        $firstName = $request->get('_first_name');
        $lastName = $request->get('_last_name');


        /** @var User */
        $user = new User($username);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $this->userManager->setUserPassword($user, $password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response("WOW");
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