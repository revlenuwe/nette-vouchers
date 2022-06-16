<?php

namespace App\Modules\Auth\Presenters;

use App\Presenters\BasePresenter;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

class SigninPresenter extends BasePresenter
{

    public function createComponentSigninForm()
    {
        $form = new Form();

        $form->addText('username')->setRequired('Enter username');
        $form->addText('password')->setRequired('Enter password');

        $form->onSuccess[] = [$this, 'signinFormSucceeded'];

        return $form;
    }

    public function signinFormSucceeded(Form $form, array $data)
    {
        try {
            $this->getUser()->login($data['username'], $data['password']);

            $this->redirect(':Base:Voucher:default');
        } catch (AuthenticationException $e) {
            $this->redirect('this');
        }
    }

    public function actionOut()
    {
        if($this->getUser()->isLoggedIn()) {
            $this->getUser()->logout();
        }

        $this->redirect(':Base:Voucher:default');
    }
}