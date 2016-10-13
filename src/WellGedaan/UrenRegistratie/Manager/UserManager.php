<?php
namespace WellGedaan\UrenRegistratie\Manager;

use Doctrine\ORM\EntityManager;
use Silex\Application;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use WellGedaan\UrenRegistratie\Model\User;

/**
 * Class UserManager
 * @package WellGedaan\UrenRegistratie\Manager
 */
class UserManager implements UserProviderInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->entityManager = $app['orm.em'];

    }

    /**
     * Loads the user for the given username or email address.
     *
     * Required by UserProviderInterface.
     *
     * @param string $username The username
     * @return UserInterface
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository('\WellGedaan\UrenRegistratie\Model\User')->findOneBy([
            'username' => $username
        ], []);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
        return $user;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     * @return UserInterface
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        return $this->getUser($user->getId());
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class
     * @return Boolean
     */
    public function supportsClass($class)
    {
        return ($class === 'WellGedaan\UrenRegistratie\Model\User');
    }

    /**
     * Get a User instance by its ID.
     *
     * @param int $id
     * @return User|null The User, or null if there is no User with that ID.
     */
    public function getUser($id)
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository('\WellGedaan\UrenRegistratie\Model\User')->findOneBy([
            'id' => $id
        ], []);

        return $user;
    }

    /**
     * Factory method for creatin   g a new User instance.
     *
     * @param array $data
     * @return User
     */
    public function createUser($data = array())
    {
        /** @var User */
        $user = new User($data['email']);
        $user->setUsername($data['username']);
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $this->setUserPassword($user, $data['password']);
        return $user;

    }

    /**
     * Encode a plain text password and set it on the given User object.
     *
     * @param User $user
     * @param string $password A plain text password.
     */
    public function setUserPassword(User $user, $password)
    {
        $user->setPassword($this->encodeUserPassword($user, $password));
    }

    /**
     * Encode a plain text password for a given user. Hashes the password with the given user's salt.
     *
     * @param User $user
     * @param string $password A plain text password.
     * @return string An encoded password.
     */
    public function encodeUserPassword(User $user, $password)
    {
        $encoder = $this->getEncoder($user);
        return $encoder->encodePassword($password, $user->getSalt());
    }

    /**
     * Get the password encoder to use for the given user object.
     *
     * @param UserInterface $user
     * @return PasswordEncoderInterface
     */
    protected function getEncoder(UserInterface $user)
    {
        return $this->app['security.encoder_factory']->getEncoder($user);
    }

    /**
     * Test whether a given plain text password matches a given User's encoded password.
     *
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function checkUserPassword(User $user, $password)
    {
        return $user->getPassword() === $this->encodeUserPassword($user, $password);
    }

    /**
     * Get a User instance for the currently logged in User, if any.
     *
     * @return UserInterface|null
     */
    public function getCurrentUser()
    {
        if ($this->isLoggedIn()) {
            return $this->app['security']->getToken()->getUser();
        }
        return null;
    }

    /**
     * Test whether the current user is authenticated.
     *
     * @return boolean
     */
    function isLoggedIn()
    {
        $token = $this->app['security']->getToken();
        if (null === $token) {
            return false;
        }
        return $this->app['security']->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * Delete a User from the database.
     *
     * @param User $user
     */
    public function delete(User $user)
    {
        //meh
    }

    /**
     * checks if a username is unique
     * @param $username
     * @return bool
     */
    public function isUsernameUnique($username)
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository('\WellGedaan\UrenRegistratie\Model\User')->findOneBy([
            'username' => $username
        ], []);
        if ($user == null) {
            return true;
        }
        return false;
    }
}