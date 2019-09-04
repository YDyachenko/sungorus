<?php

namespace Application\Controller;

use Application\Controller\Plugin\EncryptionKeyCookiePlugin;
use Application\Form\EncryptionKeyForm;
use Application\Model\User;
use Application\Service\UserKeyService;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container as SessionContainer;

/**
 * @method User identity()
 * @method EncryptionKeyCookiePlugin encryptionKeyCookie()
 */
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


    public function __construct(EncryptionKeyForm $form, UserKeyService $keyService)
    {
        $this->form       = $form;
        $this->keyService = $keyService;
    }

    /**
     * Form for input encryption key
     */
    public function indexAction()
    {
        /* @var $request Request */
        $request = $this->getRequest();
        $user    = $this->identity();

        $this->form->setKeyHash($user->getKeyHash());

        if ($request->isPost()) {
            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $data        = $this->form->getData();
                $cookieValue = $this->keyService->saveUserKey($data['key'], $user, $data['remember']);
                $container   = new SessionContainer('EncryptionKey');

                $this->encryptionKeyCookie()->send($cookieValue, $data['remember']);

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
     * @return Response
     */
    public function clearAction()
    {
        $plugin = $this->encryptionKeyCookie();
        $value  = $plugin->getValue();

        $this->keyService->deleteKey($value, $this->identity());
        $plugin->delete();

        return $this->redirect()->toRoute('home');
    }
}
