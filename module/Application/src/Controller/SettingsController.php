<?php


namespace Application\Controller;

use Application\Form\ChangePasswordForm;
use Application\Model\User;
use Application\Repository\UserRepositoryInterface;
use Zend\Crypt\Password\Bcrypt;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * @method User identity()
 */
class SettingsController extends AbstractActionController
{
    /**
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

    public function indexAction()
    {
        return $this->redirect()->toRoute('settings/password');
    }

    /**
     * Change password form
     * @return array
     */
    public function passwordAction()
    {
        /* @var Request $request */
        $request = $this->getRequest();
        $changed = false;

        if ($request->isPost()) {
            $user = $this->identity();

            $this->form->setPasswordHash($user->getPassword())
                       ->setData($request->getPost());

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
