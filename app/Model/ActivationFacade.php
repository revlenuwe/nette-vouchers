<?php

namespace App\Model;

use Nette\Database\Explorer;

class ActivationFacade
{
    public const TABLE = 'activations';

    public Explorer $database;

    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }

    public function findOneBy($voucherId, $ipAddress)
    {
        return $this->database->table(self::TABLE)
            ->where('voucher_id', $voucherId)
            ->where('ip_address', $ipAddress)->fetch();
    }

    public function createActivation(array $data)
    {
        return $this->database->table(self::TABLE)->insert($data);
    }
}