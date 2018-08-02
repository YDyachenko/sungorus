<?php

namespace Application\Controller;

use Application\Form\AccountForm;
use Application\Model\Account;
use Application\Model\AccountData;
use Application\Exception\ForbiddenException;
use Application\Exception\AccountNotFoundException;
use Application\Exception\FolderNotFoundException;
use Application\Repository\AccountRepositoryInterface;
use Application\Repository\AccountDataRepositoryInterface;
use Application\Repository\FolderRepositoryInterface;
use Application\Service\FaviconService;
use Zend\Form\Element\Csrf;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
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
     * @var AccountDataRepositoryInterface
     */
    protected $dataRepository;

    /**
     *
     * @var FaviconService
     */
    protected $iconService;

    public function __construct(FolderRepositoryInterface $folders, AccountRepositoryInterface $accounts, AccountDataRepositoryInterface $data, FaviconService$iconService)
    {
        $this->folders        = $folders;
        $this->accounts       = $accounts;
        $this->dataRepository = $data;
        $this->iconService    = $iconService;
    }

    /**
     * Create new account
     * @return ViewModel
     * @throws ForbiddenException
     */
    public function addAction()
    {
        $user = $this->identity();

        try {
            $folder = $this->folders->findById((int) $this->params('folderId'));
        } catch (FolderNotFoundException $e) {
            return $this->notFoundAction();
        }

        $folders = $this->folders->findByUser($user)->buffer();

        if ($folder->getUserId() != $user->getId())
            throw new ForbiddenException("Folder of another user");

        $form    = new AccountForm();
        $request = $this->getRequest();

        $form
            ->setFoldersOptions($folders)
            ->setFolderId($folder->getId());

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data        = $form->getData();
                $accountData = new AccountData();
                $account     = new Account();

                $accountData->setData($data['data']);

                unset($data['data']);

                $account->exchangeArray($data);
                $account->setUserId($user->getId());
                $this->accounts->save($account);

                $accountData->setAccountId($account->getId());
                $this->dataRepository->save($accountData);

                return $this->redirect()->toRoute('folders/folder/accounts', ['folderId' => $folder->getId()]);
            }
        }

        $viewModel = new ViewModel([
            'form'     => $form,
            'folders'  => $folders,
            'folderId' => $folder->getId(),
        ]);

        return $viewModel;
    }

    /**
     * Edit existing account
     * @return array
     * @throws ForbiddenException
     */
    public function editAction()
    {
        $user = $this->identity();

        try {
            $account = $this->accounts->findById($this->params('accountId'));
        } catch (AccountNotFoundException $e) {
            return $this->notFoundAction();
        }

        if ($account->getUserId() != $user->getId())
            throw new ForbiddenException("Folder of another user");

        if ($account->getFolderId() != $this->params('folderId'))
            return $this->notFoundAction();

        $accountData = $this->dataRepository->findByAccount($account);
        $folders     = $this->folders->findByUser($user)->buffer();

        $form = new AccountForm();
        $form->setFoldersOptions($folders);

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $accountData->setData($data['data']);

                unset($data['data']);

                $account->exchangeArray($data);
                $this->dataRepository->save($accountData);
                $this->accounts->save($account);

                return $this->redirect()->toRoute('folders/folder/accounts', ['folderId' => $account->getFolderId()]);
            }
        } else {
            $data         = $account->getArrayCopy();
            $data['data'] = $accountData->getData();
            $form->setData($data);
        }

        return [
            'form'     => $form,
            'folders'  => $folders,
            'folderId' => $this->params('folderId'),
            'account'  => $account,
        ];
    }

    /**
     * Delete existing account
     * @return JsonModel
     * @throws ForbiddenException
     */
    public function deleteAction()
    {
        $user      = $this->identity();
        $accountId = (int) $this->params('accountId');

        try {
            $account = $this->accounts->findById($accountId);
        } catch (AccountNotFoundException $e) {
            return $this->notFoundAction();
        }

        if ($account->getUserId() != $user->getId())
            throw new ForbiddenException("Account of another user");

        if ($account->getFolderId() != $this->params('folderId'))
            return $this->notFoundAction();

        $request = $this->getRequest();
        $token   = new Csrf('deleteAccount');

        if ($request->isPost()) {
            $validator = $token->getCsrfValidator();
            $success   = false;

            if ($validator->isValid($request->getPost('token'))) {
                $this->accounts->delete($account);
                $success = true;
            }
            return new JsonModel([
                'success' => $success
            ]);
        }

        return new JsonModel([
            'token' => $token->getValue(),
        ]);
    }

    /**
     * Open URL from account
     * @return Response
     * @throws ForbiddenException
     */
    public function openUrlAction()
    {
        $user      = $this->identity();
        $accountId = (int) $this->params('accountId');

        try {
            $account = $this->accounts->findById($accountId);
        } catch (AccountNotFoundException $e) {
            return $this->notFoundAction();
        }

        if ($account->getUserId() != $user->getId())
            throw new ForbiddenException("Account of another user");

        if ($account->getFolderId() != $this->params('folderId'))
            return $this->notFoundAction();

        $data         = $this->dataRepository->findByAccount($account)->getData();
        $url          = $data['url'];
        $validSchemes = ['http', 'https'];

        if (in_array(parse_url($url, PHP_URL_SCHEME), $validSchemes)) {
            return $this->redirect()->toUrl($url);
        }
    }

    /**
     * Show favicon
     * @throws ForbiddenException
     * @return Zend\Http\Response\Stream
     */
    public function faviconAction()
    {
        $user      = $this->identity();
        $accountId = (int) $this->params('accountId');

        try {
            $account = $this->accounts->findById($accountId);
        } catch (AccountNotFoundException $e) {
            return $this->notFoundAction();
        }

        if ($account->getUserId() != $user->getId())
            throw new ForbiddenException("Account of another user");

        if ($account->getFolderId() != $this->params('folderId'))
            return $this->notFoundAction();

        $accountData = $this->dataRepository->findByAccount($account);

        $filepath = $this->iconService->getFileFromAccount($accountData);

        $response = new \Zend\Http\Response\Stream();
        $response->setStream(fopen($filepath, 'r'));

        $headers = $response->getHeaders();
        $headers
            ->addHeaderLine('Content-Type', 'image/png')
            ->addHeaderLine('Expires', '+ 7 day')
            ->addHeaderLine('Cache-Control', 'public, max-age=604800')
            ->addHeaderLine('Pragma', 'cache')
            ->addHeaderLine('Content-Length', filesize($filepath));

        return $response;
    }

}
