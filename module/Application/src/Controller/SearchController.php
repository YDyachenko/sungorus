<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

class SearchController extends AbstractActionController
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

    public function __construct($folderModel, $accountModel)
    {
        $this->folderModel  = $folderModel;
        $this->accountModel = $accountModel;
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
        $user     = $this->identity();
        $name     = $this->params()->fromQuery('name', '');
        
        if (empty($name))
            $accounts = [];
        else
            $accounts = $this->accountModel->searchByName($name, $user);

        return [
            'folders'  => $this->folderModel->fetchByUser($user)->buffer(),
            'accounts' => $accounts,
            'name'     => $name,
        ];
    }

}
