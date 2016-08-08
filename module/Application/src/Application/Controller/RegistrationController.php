<?php

namespace Application\Controller;

use Application\Exception\ForbiddenException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container as SessionContainer;

class RegistrationController extends AbstractActionController
{

    /**
     *
     * @var \Zend\Config\Config
     */
    protected $config;

    /**
     *
     * @var \Application\Form\SignupForm
     */
    protected $form;

    /**
     *
     * @var \Application\Authentication\AuthenticationService
     */
    protected $authService;

    /**
     *
     * @var \Application\Service\UserKeyService
     */
    protected $keyService;

    /**
     *
     * @var \Application\Model\UserModel
     */
    protected $userModel;

    public function __construct($config, $form, $authService, $keyService, $userModel)
    {
        $this->config      = $config;
        $this->form        = $form;
        $this->authService = $authService;
        $this->keyService  = $keyService;
        $this->userModel   = $userModel;
    }

    /**
     * Registration form
     * @throws ForbiddenException
     */
    public function indexAction()
    {
        if (!$this->config['application']['registration']['enabled']) {
            throw new ForbiddenException('Signup disabled');
        }

        if ($this->authService->hasIdentity())
            return $this->redirect()->toRoute('home');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                $user = $this->userModel->createUser($data);

                $cookieValue = $this->keyService->generateCookie($data['key'], $user);
                $this->setEncryptionKeyCookie($cookieValue);

                $this->authService->getStorage()->write($user->getEmail());
                SessionContainer::getDefaultManager()->regenerateId();

                return $this->redirect()->toRoute('home');
            }
        }

        $this->layout('layout/login');

        return ['form' => $this->form];
    }

}
