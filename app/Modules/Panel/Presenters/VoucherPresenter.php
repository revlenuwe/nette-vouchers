<?php

namespace App\Modules\Panel\Presenters;

use App\Model\UserFacade;
use App\Model\VoucherFacade;
use App\Presenters\BasePresenter;
use App\Services\VoucherGenerator;
use Nette\Application\UI\Form;
use Ublaboo\DataGrid\DataGrid;

class VoucherPresenter extends BasePresenter
{

    private VoucherFacade $voucherFacade;

    private UserFacade $userFacade;

    private VoucherGenerator $voucherGenerator;

    private $voucher;

    public function __construct(VoucherFacade $voucherFacade, UserFacade $userFacade, VoucherGenerator $voucherGenerator)
    {
        parent::__construct();

        $this->voucherFacade = $voucherFacade;
        $this->userFacade = $userFacade;
        $this->voucherGenerator = $voucherGenerator;
    }

    public function startup()
    {
        parent::startup();

        if(!$this->getUser()->isLoggedIn()) {
            $this->redirect(':Base:Voucher:default');
        }
    }

    public function actionCreate()
    {
        $form = $this->getComponent('createVoucherForm');

        $form->onSuccess[] = [$this, 'createVoucherFormSucceeded'];
    }

    public function actionEdit(int $id)
    {
        $voucher = $this->voucherFacade->findOneById($id);

        if(!$voucher) {
            $this->redirect('Voucher:default');
        }

        $this->voucher = $voucher;

        $form = $this->getComponent('editVoucherForm');
        $form->onSuccess[] = [$this, 'editVoucherFormSucceeded'];
    }

    public function createComponentVouchersTable($name)
    {
        $userVouchers = $this->userFacade->findOneById($this->getUser()->getId())->related('vouchers')->fetchAll();

        DataGrid::$iconPrefix = 'fa-solid fa-';

        $grid = new DataGrid($this, $name);
        $grid->setDataSource($userVouchers);

        $grid->addColumnText('code', 'Code');
        $grid->addColumnNumber('max_uses_count', 'Max uses count')->setSortable();
        $grid->addColumnDateTime('expired_at', 'Expired at')->setFormat('d.m.Y H:i')->setSortable();

        $isActiveStatus = $grid->addColumnStatus('is_active', 'Status')->setAlign('right');


        $isActiveStatus
            ->addOption(1, 'Active')->setClass('btn btn-success')->endOption()
            ->addOption(0, 'Inactive')->setClass('btn btn-danger')->endOption()
            ->onChange[] = [$this, 'updateActiveStatus'];

        $grid->addAction('actions', '','edit')->setClass('btn btn-sm btn-warning')->setIcon('pencil');

        return $grid;
    }

    public function createComponentCreateVoucherForm()
    {
        $form = new Form();
        $form->addSelect('mask', null, [
            '****-****' => '****-****',
            '***-***-***' => '***-***-***'
        ]);
        $form->addInteger('max_uses_count');
        $form->addText('expired_at');
        $form->addTextArea('content');

        return $form;
    }


    public function createComponentEditVoucherForm()
    {
        $form = $this->createComponent('createVoucherForm');

        $form['mask']->setDisabled();
        $form['expired_at']->setDisabled();//temporary
        $form['content']->setDisabled();

        $form->addCheckbox('is_active');
        $form->setDefaults($this->voucher);

        return $form;
    }

    public function createVoucherFormSucceeded(Form $form, array $data)
    {
        $this->voucherFacade->createVoucher([
            'user_id' => $this->getUser()->getId(),
            'code' => $this->voucherGenerator->setMask($data['mask'])->generateCode(),
            'max_uses_count' => $data['max_uses_count'],
            'expired_at' => $data['expired_at'],
            'content' => $data['content'],
        ]);

        $this->redirect('Voucher:default');
    }

    public function editVoucherFormSucceeded(Form $form, array $data)
    {
        $voucher = $this->voucherFacade->updateVoucher($this->voucher->id, $data);

        if(!$voucher) {
            //flash
        }

        $this->redirect('Voucher:default');
    }

    public function updateActiveStatus($id, $newStatus)
    {
        $voucher = $this->voucherFacade->findOneById($id);

        $voucher->update(['is_active' => $newStatus]);

        if($this->isAjax()) {
            $this['vouchersTable']->redrawControl();
//            $this['vouchersTable']->redrawItem($id);
        }
    }
}