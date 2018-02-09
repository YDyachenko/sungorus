<?php

namespace Application\Controller;

use Application\Exception\ForbiddenException;
use Application\Form\SignupForm;
use Application\Repository\UserRepositoryInterface;
use Application\Service\UserKeyService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container as SessionContainer;

class RegistrationController extends AbstractActionController
{

    /**
     *
     * @var array
     */
    protected $config;

    /**
     *
     * @var SignupForm
     */
    protected $form;

    /**
     *
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     *
     * @var UserKeyService
     */
    protected $keyService;

    /**
     *
     * @var UserRepositoryInterface
     */
    protected $users;

    public function __construct(array $config, SignupForm $form, AuthenticationServiceInterface $authService, UserKeyService $keyService, UserRepositoryInterface $users)
    {
        $this->config      = $config;
        $this->form        = $form;
        $this->authService = $authService;
        $this->keyService  = $keyService;
        $this->users       = $users;
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
                $user = $this->users->createUser($data);

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
