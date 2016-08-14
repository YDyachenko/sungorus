<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

class IndexController extends AbstractActionController
{

    /**
     *
     * @var \Application\Model\FolderModel
     */
    protected $folderModel;

    /**
     *
     * @var \Application\Model\AccountModel
     */
    protected $accountModel;

    /**
     *
     * @var \Application\Service\AuthLogService
     */
    protected $authLogService;

    public function __construct($folderModel, $accountModel, $authLogService)
    {
        $this->folderModel    = $folderModel;
        $this->accountModel   = $accountModel;
        $this->authLogService = $authLogService;
    }

    /**
     * {@inheritdoc}
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'checkUserEncryptionKey'], 100);
    }

    /**
     * Dashboard
     * @return array
     */
    public function indexAction()
    {
        $user = $this->identity();

        return [
            'folders'  => $this->folderModel->fetchByUser($user)->buffer(),
            'accounts' => $this->accountModel->fetchUserFavorites($user),
            'lastAuth' => $this->authLogService->getLastSuccess($user)
        ];
    }

}
