<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\SmartObject;
use Nette\Utils\DateTime;

class VoucherFacade
{
    use SmartObject;

    public const TABLE = 'vouchers';

    protected Explorer $database;

    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }

    public function isExpired($voucher)
    {
        return $voucher->expired_at < new DateTime();
    }

    public function isDepleted($voucher)
    {
        return $voucher->related('activations')->count() > $voucher->max_uses_count;
    }

    public function findAll()
    {
        return $this->database->table(self::TABLE)->fetchAll();
    }

    public function findOneById(int $id)
    {
        return $this->database->table(self::TABLE)->get($id);
    }

    public function findOneByCode(string $code)
    {
        return $this->database->table(self::TABLE)->where('code', $code)->fetch();
    }

    public function createVoucher(array $data)
    {
        return $this->database->table(self::TABLE)->insert($data);
    }

    public function updateVoucher(int $id, array $data)
    {
        $voucher = $this->findOneById($id);

        if(!$voucher) {
            return false;
        }

        return $voucher->update($data);
    }

    public function deleteVoucher(int $id)
    {

    }

}