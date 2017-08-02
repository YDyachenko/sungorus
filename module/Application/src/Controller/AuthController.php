<?php

namespace Application\Controller;

use Application\Authentication\AuthEvent;
use Application\Model\UserModel;
use Application\Service\UserKeyService;
use Application\Form;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container as SessionContainer;

class AuthController extends AbstractActionController
{

    /**
     *
     * @var array
     */
    protected $config;

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
     * @var UserModel
     */
    protected $userModel;

    public function __construct(array $config, AuthenticationServiceInterface $authService, UserKeyService $keyService, UserModel $userModel)
    {
        $this->config      = $config;
        $this->authService = $authService;
        $this->keyService  = $keyService;
        $this->userModel   = $userModel;
    }

    /**
     * Login form
     * @return array
     */
    public function loginAction()
    {
        if ($this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $error   = false;
        $message = '';
        $form    = new Form\LoginForm();
        $events  = $this->getEventManager();

        $results = $events->trigger(AuthEvent::EVENT_AUTHENTICATION);
        if ($results->stopped()) {
            $result  = $results->last();
            $message = $result['message'];
            $error   = true;
            $form->get('submit')->setAttribute('disabled', 'disabled');
        }


        $request = $this->getRequest();

        if (!$error && $request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $authAdapter = $this->authService->getAdapter();
                $data        = $form->getData();

                $authAdapter->setIdentity($data['identity'])
                        ->setCredential($data['credential']);

                $result = $this->authService->authenticate();

                $event = new AuthEvent();
                $event->setTarget($this);
                $event->setRequest($request);

                if ($result->isValid()) {
                    $event->setName(AuthEvent::EVENT_AUTHENTICATION_SUCCESS);
                    $events->trigger($event);
                    return $this->redirect()->toRoute('home');
                } else {
                    $event->setName(AuthEvent::EVENT_AUTHENTICATION_FAILURE);
                    $events->trigger($event);
                    $error = true;
                }
            }
        }

        $this->layout('layout/login');

        return [
            'form'                => $form,
            'error'               => $error,
            'message'             => $message,
            'registrationEnabled' => $this->config['application']['registration']['enabled']
        ];
    }

    /**
     * Logout and clear identity
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {
        $this->authService->clearIdentity();

        return $this->redirect()->toRoute('login');
    }

    /**
     * Form for input encryption key
     */
    public function encryptionKeyAction()
    {
        $user    = $this->identity();
        $form    = new Form\EncryptionKeyForm($user->getKeyHash());
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data        = $form->getData();
                $cookieValue = $this->keyService->generateCookie($data['key'], $user);
                $lifetime    = $this->config['application']['enc_key_cookie']['lifetime'];
                $expires     = $data['remember'] ? time() + $lifetime : null;

                $this->setEncryptionKeyCookie($cookieValue, $expires);

                $container = new SessionContainer('EncryptionKey');

                if (isset($container->redirectTo)) {
                    $routeMatch = $container->redirectTo;
                    unset($container->redirectTo);
                    return $this->redirect()->toRoute($routeMatch->getMatchedRouteName(), $routeMatch->getParams());
                } else {
                    return $this->redirect()->toRoute('home');
                }
            }
        }

        return [
            'form' => $form,
        ];
    }

    /**
     * Clear cookie with encryption key
     * @return \Zend\Http\Response
     */
    public function clearEncryptionKeyAction()
    {
        $this->setEncryptionKeyCookie(null, 0);

        return $this->redirect()->toRoute('home');
    }

    /**
     * Change password form
     * @return array
     */
    public function changePasswordAction()
    {
        $form    = new Form\ChangePasswordForm();
        $request = $this->getRequest();
        $changed = false;

        if ($request->isPost()) {
            $user = $this->identity();

            $form->setData($request->getPost())
                    ->setPasswordHash($user->getPassword());

            if ($form->isValid()) {
                $bcrypt  = new \Zend\Crypt\Password\Bcrypt();
                $data    = $form->getData();
                $hash    = $bcrypt->create($data['new']);
                $changed = true;

                $user->setPassword($hash);
                $this->userModel->saveUser($user);
            }
        }

        return [
            'form'    => $form,
            'changed' => $changed,
        ];
    }

}
