<?php


namespace App\Presenters;


use Nette\Application\UI\Presenter;

class BasePresenter extends Presenter
{
    public function redirectWithFlash(string $route, string $message, string $messageType = 'info')
    {
        $this->flashMessage($message, $messageType);
        $this->redirect($route);
    }
}