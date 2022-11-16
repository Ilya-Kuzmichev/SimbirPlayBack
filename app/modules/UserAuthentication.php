<?php

namespace modules;

class UserAuthentication
{
    private string $login;
    private string $password;
    private array $authUser;

    private array $users = [
        [
            'id' => '76ed427d-2096-4525-a96b-308db7bca823',
            'login' => 'admin',
            'password' => 'admin',
            'name' => 'Администратор',
        ],
    ];

    /**
     * @param string $login
     * @param string $password
     */
    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * @return bool
     */
    public function check()
    {
        foreach ($this->users as $user) {
            if ($this->login == $user['login'] && $this->password == $user['password']) {
                $this->authUser = $user;
                return true;
            }
        }
        return false;
    }

    /**
     * @return mixed|null
     */
    public function getAuthUserId()
    {
        return $this->authUser && isset($this->authUser['id']) ? $this->authUser['id'] : null;
    }

    /**
     * @return mixed|null
     */
    public function getAuthUserName()
    {
        return $this->authUser && isset($this->authUser['name']) ? $this->authUser['name'] : null;
    }
}