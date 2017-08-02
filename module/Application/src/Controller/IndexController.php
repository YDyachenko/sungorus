<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

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
