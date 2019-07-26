<?php

namespace Application\Controller;

use Application\Controller\Plugin\EncryptionKeyCookiePlugin;
use Application\Exception\ForbiddenException;
use Application\Form\SignupForm;
use Application\Repository\UserRepositoryInterface;
use Application\Service\UserKeyService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container as SessionContainer;
use Zend\Session\ManagerInterface;

/**
 * @method EncryptionKeyCookiePlugin encryptionKeyCookie()
 */
class RegistrationController extends AbstractActionController
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var SignupForm
     */
    protected $form;

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * @var UserKeyService
     */
    protected $keyService;

    /**
     * @var UserRepositoryInterface
     */
    protected $users;

    /**
     * @var ManagerInterface
     */
    protected $sessionManager;

    public function __construct(
        array $config,
        SignupForm $form,
        AuthenticationServiceInterface $authService,
        UserKeyService $keyService,
        UserRepositoryInterface $users,
        ManagerInterface $manager
    ) {
        $this->config         = $config;
        $this->form           = $form;
        $this->authService    = $authService;
        $this->keyService     = $keyService;
        $this->users          = $users;
        $this->sessionManager = $manager;
    }

    /**
     * Registration form
     * @throws ForbiddenException
     */
    public function indexAction()
    {
        if (! $this->config['application']['registration']['enabled']) {
            throw new ForbiddenException('Signup disabled');
        }

        if ($this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                $user = $this->users->createUser($data);

                $cookieValue = $this->keyService->saveUserKey($data['key'], $user);
                $this->encryptionKeyCookie()->send($cookieValue);

                $this->authService->getStorage()->write($user->getEmail());
                $this->sessionManager->regenerateId();

                return $this->redirect()->toRoute('home');
            }
        }

        $this->layout('layout/login');

        return ['form' => $this->form];
    }
}
