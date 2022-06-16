<?php


namespace App\Modules\Auth\Presenters;


use App\Model\UserFacade;
use App\Presenters\BasePresenter;
use Nette\Application\UI\Form;

class SignupPresenter extends BasePresenter
{

    protected UserFacade $userFacade;

    public function __construct(UserFacade $userFacade)
    {
        $this->userFacade = $userFacade;
    }

    public function createComponentSignupForm()
    {
        $form = new Form();

        $form->addText('username')->setRequired('Enter username');
        $form->addPassword('password')
            ->setRequired('Enter password')
            ->addRule($form::MIN_LENGTH, 'Password must contain at least %d characters', 8);
        $form->addPassword('password_confirmation')
            ->setRequired('Enter password')
            ->addRule($form::EQUAL, 'Passwords must be identical', $form['password']);


        $form->onSuccess[] = [$this, 'signupFormSucceeded'];

        return $form;
    }

    public function signupFormSucceeded(Form $form, array $data)
    {
        $user = $this->userFacade->findOneByUsername($data['username']);

        if(!$user) {
            $this->userFacade->createUser($data);
            $this->getUser()->login($data['username'], $data['password']);

            $this->redirect(':Base:Voucher:default');
        }

        $this->redirectWithFlash('this', 'This username is already taken');
    }
}