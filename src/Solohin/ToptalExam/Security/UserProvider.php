<?php
/**
 * Created by solohin.i@gmail.com.
 * http://data5.pro
 * https://www.upwork.com/freelancers/~0110e79b44736be7ab
 * Date: 06/11/16
 * Time: 16:37
 */

namespace Solohin\ToptalExam\Security;

use Solohin\ToptalExam\Services\UsersService;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $usersService;

    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }

    /**
     * Loads the user for the given token instead of username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface|null
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($token)
    {
        $fromDB = $this->usersService->getUserByToken($token);

        if (isset($fromDB['id'])) {
            return new \Symfony\Component\Security\Core\User\User(
                $fromDB['username'], $fromDB['password'], $fromDB['roles']
            );
        } else {
            return null;
        }
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
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        $fromDB = $this->usersService->getByUsername($user->getUsername());
        if (isset($fromDB['id'])) {
            return new \Symfony\Component\Security\Core\User\User(
                $fromDB['username'], $fromDB['password'], $fromDB['roles']
            );
        } else {
            return null;
        }
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'Symfony\Component\Security\Core\User\User';
    }
}