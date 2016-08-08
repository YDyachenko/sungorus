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
     * @var \Zend\Db\TableGateway\TableGatewayInterface
     */
    protected $authLogTable;

    public function __construct($folderModel, $accountModel, $authLogTable)
    {
        $this->folderModel  = $folderModel;
        $this->accountModel = $accountModel;
        $this->authLogTable = $authLogTable;
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

        $lastAuth = $this->authLogTable->select(function ($select) use ($user) {
                $select->where(['user_id' => $user->getId()])
                    ->order('datetime DESC')
                    ->limit(1)
                    ->offset(1);
            })->current();

        return [
            'folders'  => $this->folderModel->fetchByUser($user)->buffer(),
            'accounts' => $this->accountModel->fetchUserFavorites($user),
            'lastAuth' => $lastAuth
        ];
    }

}
