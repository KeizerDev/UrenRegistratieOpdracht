<?php
namespace WellGedaan\UrenRegistratie\Controller;

use Doctrine\ORM\EntityManager;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WellGedaan\UrenRegistratie\Model\HourRegistration;
use WellGedaan\UrenRegistratie\Model\User;

class PageController extends BaseController implements ControllerProviderInterface
{


    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(\Twig_Environment $twig, EntityManager $entityManager)
    {
        parent::__construct($twig);
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

        $controllers->get('/', [$this, 'renderIndexPage']);
        $controllers->get('/uur-registratie/nieuw', [$this, 'renderNewHourRegistration'])->bind('hourregistration.new');
        $controllers->post('uur-registratie/niew/post', [$this, 'newHourRegistrationFromRequest'])->bind('hourregistration.new.post');

        return $controllers;
    }


    /**
     * @param Application $app
     * @return string
     */
    public function renderIndexPage(Application $app)
    {

        /** @var User $user */
        $user = $app['user'];


        if($app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
           $searchCriteria = [];
        }else {
            $searchCriteria = [
                'user' => $user->getId()
            ];
        }

        /** @var HourRegistration $hourRegistrations */
        $hourRegistrations = $this->entityManager->getRepository('\WellGedaan\UrenRegistratie\Model\HourRegistration')->findBy($searchCriteria, [
            'day' => 'DESC',
            'id' => 'DESC'
        ]);

        return $this->render('index.twig', [
            'registrations' => $hourRegistrations
        ]);
    }


    /**
     * @return string
     */
    public function renderNewHourRegistration()
    {
        return $this->render('hourregistration/new.twig', []);
    }

    public function newHourRegistrationFromRequest(Request $request, Application $app)
    {
        /** @var User $user */
        $user = $app['user'];

        $day = $request->get('_day');
        $start = $request->get('_start');
        $end = $request->get('_end');


        $hourRegistration = new HourRegistration();
        $hourRegistration->setUser($user);
        $hourRegistration->setDay(new \DateTime($day));
        $hourRegistration->setStartingTime(new \DateTime($start));
        $hourRegistration->setEndTime(new \DateTime($end));

        $this->entityManager->persist($hourRegistration);
        $this->entityManager->flush();

        return new RedirectResponse('/');
    }

}