<?php


namespace App\Modules\Base\Presenters;


use App\Model\VoucherFacade;
use App\Presenters\BasePresenter;
use App\Services\ActivationManager;
use Nette\Application\UI\Form;
use Nette\Database\Explorer;

class VoucherPresenter extends BasePresenter
{
    protected VoucherFacade $voucherFacade;

    protected ActivationManager $activationManager;

    public function __construct(VoucherFacade $voucherFacade, ActivationManager $activationManager)
    {
        parent::__construct();

        $this->voucherFacade = $voucherFacade;
        $this->activationManager = $activationManager;
    }

//    public function renderDefault()
//    {
//        $this->template->vouchers = $this->database->table('voucher')->fetchAll();
//
//    }

    protected function createComponentRedeemVoucherForm()
    {
        $form = new Form();
        $form->addText('code', 'Code')
            ->setRequired('Enter code');

        $form->onSuccess[] = [$this, 'redeemVoucherFormSucceeded'];

        return $form;
    }

    public function redeemVoucherFormSucceeded(Form $form, array $data)
    {
        if($form->isSuccess()) {
            $voucher = $this->voucherFacade->findOneByCode($data['code']);
            if(!$voucher) {
                $this->redirectWithFlash('Voucher:default','Voucher does not exist');
            }

            if($this->voucherFacade->isExpired($voucher) || !$voucher->is_active) {
                $this->redirectWithFlash('Voucher:default','Voucher is expired or inactive, try another');
            }

            if($this->voucherFacade->isDepleted($voucher)) {
                $this->redirectWithFlash('Voucher:default', 'Voucher is depleted, try another');
            }

            $this->activationManager->handleUniqueActivate($voucher->id, $this->getHttpRequest()->getRemoteAddress());

            $this->template->voucher = $voucher;
        }
    }
}