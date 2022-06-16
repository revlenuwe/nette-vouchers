<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Security\Passwords;
use Nette\SmartObject;

class UserFacade
{
    use SmartObject;

    public const TABLE = 'users';

    protected Explorer $database;

    protected Passwords $passwords;

    public function __construct(Explorer $database, Passwords $passwords)
    {
        $this->database = $database;
        $this->passwords = $passwords;
    }

    public function findOneById(int $id)
    {
        return $this->database->table(self::TABLE)->get($id);
    }

    public function findOneByUsername(string $username)
    {
        return $this->database->table(self::TABLE)->where('username', $username)->fetch();
    }

    public function createUser(array $data)
    {
        return $this->database->table(self::TABLE)->insert([
            'username' => $data['username'],
            'password' => $this->passwords->hash($data['password'])
        ]);
    }

}