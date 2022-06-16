<?php

namespace App\Services;

use App\Model\ActivationFacade;

class ActivationManager
{

    public ActivationFacade $activationFacade;

    public function __construct(ActivationFacade $activationFacade)
    {
        $this->activationFacade = $activationFacade;
    }

    public function handleUniqueActivate(int $voucherId, string $ipAddress)
    {
        $activation = $this->activationFacade->findOneBy($voucherId, $ipAddress);

        if($activation) {
            return false;
        }

        return $this->activationFacade->createActivation([
            'voucher_id' => $voucherId,
            'ip_address' => $ipAddress
        ]);
    }
}