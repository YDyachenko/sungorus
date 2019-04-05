<?php

namespace Application\Controller;


use Application\Form\EncryptionKeyForm;
use Application\Model\User;
use Application\Service\UserKeyService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container as SessionContainer;

class EncryptionKeyController extends AbstractActionController
{

    /**
     * @var EncryptionKeyForm
     */
    protected $form;

    /**
     * @var UserKeyService
     */
    protected $keyService;

    /**
     * @var array
     */
    protected $config;

    public function __construct(EncryptionKeyForm $form, UserKeyService $keyService, array $config)
    {
        $this->form       = $form;
        $this->keyService = $keyService;
        $this->config     = $config;
    }

    /**
     * Form for input encryption key
     */
    public function indexAction()
    {
        /* @var User $user */
        $user    = $this->identity();
        $request = $this->getRequest();

        $this->form->setKeyHash($user->getKeyHash());

        if ($request->isPost()) {
            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $data        = $this->form->getData();
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
            'form' => $this->form,
        ];
    }

    /**
     * Clear cookie with encryption key
     *
     * @return \Zend\Http\Response
     */
    public function clearAction()
    {
        $this->setEncryptionKeyCookie(null, 0);

        return $this->redirect()->toRoute('home');
    }
}