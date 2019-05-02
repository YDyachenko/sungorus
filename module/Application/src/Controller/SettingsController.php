<?php


namespace Application\Controller;

use Application\Form\ChangePasswordForm;
use Application\Repository\UserRepositoryInterface;
use Zend\Crypt\Password\Bcrypt;
use Zend\Mvc\Controller\AbstractActionController;

class SettingsController extends AbstractActionController
{
    /**
     *
     * @var UserRepositoryInterface
     */
    protected $users;

    /**
     * @var ChangePasswordForm
     */
    protected $form;

    public function __construct(UserRepositoryInterface $users, ChangePasswordForm $form)
    {
        $this->users = $users;
        $this->form  = $form;
    }

    /**
     * Change password form
     *
     * @return array
     */
    public function changePasswordAction()
    {
        $request = $this->getRequest();
        $changed = false;

        if ($request->isPost()) {
            $user = $this->identity();

            $this->form->setData($request->getPost())
                       ->setPasswordHash($user->getPassword());

            if ($this->form->isValid()) {
                $bcrypt  = new Bcrypt();
                $data    = $this->form->getData();
                $hash    = $bcrypt->create($data['new']);
                $changed = true;

                $user->setPassword($hash);
                $this->users->save($user);
            }
        }

        return [
            'form'    => $this->form,
            'changed' => $changed,
        ];
    }
}
