<?php

namespace Application\Controller;

use Application\Model\User;
use Application\Repository\AccountRepositoryInterface;
use Application\Repository\FolderRepositoryInterface;
use Application\Service\AuthLogService;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * @method User identity()
 */
class IndexController extends AbstractActionController
{

    /**
     * @var FolderRepositoryInterface
     */
    protected $folders;

    /**
     * @var AccountRepositoryInterface
     */
    protected $accounts;

    /**
     * @var AuthLogService
     */
    protected $authLogService;

    public function __construct(
        FolderRepositoryInterface $folders,
        AccountRepositoryInterface $accounts,
        AuthLogService $authLogService
    ) {
        $this->folders        = $folders;
        $this->accounts       = $accounts;
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
            'folders'  => $this->folders->findByUser($user)->buffer(),
            'accounts' => $this->accounts->findUserFavorites($user),
            'lastAuth' => $this->authLogService->getLastSuccess($user),
        ];
    }
}
