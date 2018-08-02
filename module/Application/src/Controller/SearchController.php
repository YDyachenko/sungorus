<?php

namespace Application\Controller;

use Application\Repository\AccountRepositoryInterface;
use Application\Repository\FolderRepositoryInterface;
use Zend\Mvc\Controller\AbstractActionController;

class SearchController extends AbstractActionController
{

    /**
     *
     * @var FolderRepositoryInterface
     */
    protected $folders;

    /**
     *
     * @var AccountRepositoryInterface
     */
    protected $accounts;

    public function __construct(FolderRepositoryInterface $folders, AccountRepositoryInterface $accounts)
    {
        $this->folders  = $folders;
        $this->accounts = $accounts;
    }

    /**
     * Search accounts
     * @return array
     */
    public function indexAction()
    {
        $user = $this->identity();
        $name = $this->params()->fromQuery('name', '');

        if (empty($name))
            $accounts = [];
        else
            $accounts = $this->accounts->findByName($name, $user);

        if (count($accounts) === 1) {
            $account = $accounts->current();
            return $this->redirect()->toRoute('folders/folder/accounts/account', [
                    'folderId'  => $account->getFolderId(),
                    'accountId' => $account->getId(),
            ]);
        }

        return [
            'folders'  => $this->folders->findByUser($user)->buffer(),
            'accounts' => $accounts,
            'name'     => $name,
        ];
    }

}
