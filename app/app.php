<?php
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Provider\DoctrineServiceProvider;

require_once "../vendor/autoload.php";
$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\SessionServiceProvider());

/* database service provider */
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'host' => '127.0.0.1',
        'dbname' => 'uren',
        'user' => 'homestead',
        'password' => 'secret',
    ),
));

$app->register(new DoctrineOrmServiceProvider, array(
    'orm.proxies_dir' => __DIR__ . '/../cache/doctrine',
    'orm.em.options' => array(
        'mappings' => array(
            array(
                'type' => 'annotation',
                'namespace' => 'WellGedaan\UrenRegistratie\Model',
                'path' => __DIR__ . '/../src/WellGedaan/UrenRegistratie/Model',
            )
        ),
    ),
));


$app["security.firewalls"] = array();
$app->register(new \Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'default' => array(
            'pattern' => '^/.*$',
            'anonymous' => true,
            'form' => array(
                'login_path' => '/security/login',
                'check_path' => '/security/login_check',
            ),
            'users' => function ($app) {
                return $app['UserManager'];
            },
        ),
    ),
    'security.access_rules' => array(
        array('^/security/login', 'IS_AUTHENTICATED_ANONYMOUSLY'),
        array('^/security/register', 'IS_AUTHENTICATED_ANONYMOUSLY'),
        array('/', 'ROLE_USER')
    ),
));

//services
$app['UserManager'] = new \WellGedaan\UrenRegistratie\Manager\UserManager($app);

$app->boot();

/* twig service provider */
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../src/WellGedaan/UrenRegistratie/Views'
));


$app->mount('', new \WellGedaan\UrenRegistratie\Controller\PageController($app['twig']));
$app->mount('/security', new \WellGedaan\UrenRegistratie\Controller\SecurityController($app['twig'], $app['UserManager'], $app['orm.em']));

return $app;