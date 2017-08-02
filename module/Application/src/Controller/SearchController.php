<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

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
