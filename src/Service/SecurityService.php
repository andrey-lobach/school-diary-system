<?php
/**
 * Created by PhpStorm.
 * User: andrei
 * Date: 12.12.18
 * Time: 17.23
 */

namespace Service;

use Core\HTTP\Session;
use Model\UserModel;

use Core\Security\PasswordHelper;

class SecurityService
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * @var PasswordHelper
     */
    private $passwordHelper;

    /**
     * SecurityService constructor.
     * @param Session $session
     * @param UserModel $userModel
     * @param PasswordHelper $passwordHelper
     */
    public function __construct(Session $session, UserModel $userModel, PasswordHelper $passwordHelper)
    {
        $this->session = $session;
        $this->userModel = $userModel;
        $this->passwordHelper = $passwordHelper;
    }

    /**
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->session->has('user');
    }

    public function logout()
    {
        $this->session->remove('user');
    }

    /**
     * @param array $credentials
     * @return bool
     */
    public function authorize(array $credentials): bool
    {
        if (!$this->isPasswordValid($credentials['login'], $credentials['password'])) {
            return false;
        }
        $user = $this->userModel->findByLogin($credentials['login']);
        $this->session->set('user', $user);

        return true;
    }

    /**
     * @param string $login
     * @return bool
     */
    public function userExist(string $login): bool
    {
        return (bool) $this->userModel->findByLogin($login);
    }

    /**
     * @param string $login
     * @param string $password
     * @return bool
     */
    public function isPasswordValid(string $login, string $password): bool
    {
        $user = $this->userModel->findByLogin($login);
        if (!$user) {
            return false;
        }
        $salt = $this->passwordHelper->getSaltPart($user['password']);
        $hashPart = $this->passwordHelper->getHashPart($user['password']);
        $hash = $this->passwordHelper->getHash($password, $salt);

        return $hash === $hashPart;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        if ($this->isAuthorized()) {
            return $this->session->get('user')['role'];
        }

        return '';
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        if ($this->isAuthorized()) {
            return $this->session->get('user')['id'];
        }

        return '';
    }

    /**
     * @return mixed|array
     */
    public function getUser()
    {
        if ($this->isAuthorized()) {
            return $this->session->get('user');
        }

        return '';
    }
}