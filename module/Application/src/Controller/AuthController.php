<?php

namespace Application\Controller;

use Application\Authentication\AuthEvent;
use Application\Form\LoginForm;
use Zend\Authentication\Adapter\ValidatableAdapterInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\ManagerInterface;

class AuthController extends AbstractActionController
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var AuthenticationServiceInterface
     */
    protected $service;

    /**
     * @var LoginForm
     */
    protected $form;

    /**
     * @var ManagerInterface
     */
    protected $sessionManager;


    public function __construct(
        AuthenticationServiceInterface $service,
        array $config,
        LoginForm $form,
        ManagerInterface $manager
    ) {
        $this->service        = $service;
        $this->config         = $config;
        $this->form           = $form;
        $this->sessionManager = $manager;
    }

    /**
     * Login form
     * @return Response|array
     */
    public function loginAction()
    {
        if ($this->service->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $this->layout('layout/login');

        $error   = false;
        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                /* @var ValidatableAdapterInterface $adapter */
                $adapter = $this->service->getAdapter();
                $data    = $this->form->getData();

                $adapter->setIdentity($data['identity'])
                        ->setCredential($data['credential']);

                $result = $this->service->authenticate();

                $event = new AuthEvent(AuthEvent::EVENT_AUTHENTICATE_POST, $this);
                $event->setRequest($request);
                $event->setResult($result);

                $events = $this->getEventManager();
                $events->triggerEvent($event);

                if ($result->isValid()) {
                    $this->sessionManager->regenerateId();

                    return $this->redirect()->toRoute('home');
                } else {
                    $error = true;
                }
            }
        }

        return [
            'form'         => $this->form,
            'error'        => $error,
            'registration' => $this->config['application']['registration']['enabled'],
        ];
    }

    /**
     * Logout and clear identity
     * @return Response
     */
    public function logoutAction()
    {
        $this->service->clearIdentity();

        return $this->redirect()->toRoute('login');
    }
}
