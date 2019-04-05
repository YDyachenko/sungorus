<?php

namespace Application\Controller;

use Application\Authentication\AuthEvent;
use Application\Form\LoginForm;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Mvc\Controller\AbstractActionController;

class AuthController extends AbstractActionController
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * @var LoginForm
     */
    protected $form;


    public function __construct(AuthenticationServiceInterface $authService, array $config, LoginForm $form)
    {
        $this->authService = $authService;
        $this->config      = $config;
        $this->form        = $form;
    }

    /**
     * Login form
     *
     * @return array
     */
    public function loginAction()
    {
        if ($this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $error   = false;
        $message = '';
        $events  = $this->getEventManager();

        $results = $events->trigger(AuthEvent::EVENT_AUTHENTICATION);
        if ($results->stopped()) {
            $result  = $results->last();
            $message = $result['message'];
            $error   = true;
            $this->form->get('submit')->setAttribute('disabled', 'disabled');
        }


        $request = $this->getRequest();

        if (!$error && $request->isPost()) {
            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $authAdapter = $this->authService->getAdapter();
                $data        = $this->form->getData();

                $authAdapter->setIdentity($data['identity'])
                            ->setCredential($data['credential']);

                $result = $this->authService->authenticate();

                $event = new AuthEvent();
                $event->setTarget($this);
                $event->setRequest($request);

                if ($result->isValid()) {
                    $event->setName(AuthEvent::EVENT_AUTHENTICATION_SUCCESS);
                    $events->triggerEvent($event);
                    return $this->redirect()->toRoute('home');
                } else {
                    $event->setName(AuthEvent::EVENT_AUTHENTICATION_FAILURE);
                    $events->triggerEvent($event);
                    $error = true;
                }
            }
        }

        $this->layout('layout/login');

        return [
            'form'                => $this->form,
            'error'               => $error,
            'message'             => $message,
            'registrationEnabled' => $this->config['application']['registration']['enabled'],
        ];
    }

    /**
     * Logout and clear identity
     *
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {
        $this->authService->clearIdentity();

        return $this->redirect()->toRoute('login');
    }
}
