<?php

namespace Application\Controller;

use Application\Form\AccountForm;
use Application\Model\AccountEntity;
use Application\Model\AccountDataEntity;
use Application\Exception\ForbiddenException;
use Application\Exception\AccountNotFoundException;
use Application\Exception\FolderNotFoundException;
use Zend\Form\Element\Csrf;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{

    /**
     * @var \Application\Model\FolderModel
     */
    protected $folderModel;

    /**
     * @var \Application\Model\AccountModel
     */
    protected $accountModel;

    /**
     *
     * @var \Application\Service\FaviconService
     */
    protected $iconService;

    public function __construct($folderModel, $accountModel, $iconService)
    {
        $this->folderModel  = $folderModel;
        $this->accountModel = $accountModel;
        $this->iconService  = $iconService;
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
     * Create new account
     * @return ViewModel
     * @throws ForbiddenException
     */
    public function addAction()
    {
        $user = $this->identity();

        try {
            $folder = $this->folderModel->fetchById($this->params('folderId'));
        } catch (FolderNotFoundException $e) {
            return $this->notFoundAction();
        }

        $folders = $this->folderModel->fetchByUser($user)->buffer();

        if ($folder->getUserId() != $user->getId())
            throw new ForbiddenException("Folder of another user");

        $form = new AccountForm();
        $form->setFoldersOptions($folders)
            ->setFolderId($folder->getId());

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data        = $form->getData();
                $accountData = new AccountDataEntity();
                $account     = new AccountEntity();

                $accountData->setData($data['data']);

                unset($data['data']);

                $account->exchangeArray($data);
                $account->setUserId($user->getId());
                $this->accountModel->saveAccount($account);

                $accountData->setAccountId($account->getId());
                $this->accountModel->insertAccountData($accountData);

                return $this->redirect()->toRoute('folder', array('folderId' => $folder->getId()));
            }
        }

        $viewModel = new ViewModel(array(
            'form'     => $form,
            'folders'  => $folders,
            'folderId' => $folder->getId(),
        ));

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
            $account = $this->accountModel->fetchById($this->params('accountId'));
        } catch (AccountNotFoundException $e) {
            return $this->notFoundAction();
        }

        if ($account->getUserId() != $user->getId())
            throw new ForbiddenException("Folder of another user");

        if ($account->getFolderId() != $this->params('folderId'))
            return $this->notFoundAction();

        $accountData = $this->accountModel->fetchAccountData($account);
        $folders     = $this->folderModel->fetchByUser($user)->buffer();

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
                $this->accountModel->updateAccountData($accountData);
                $this->accountModel->saveAccount($account);

                return $this->redirect()->toRoute('folder', array('folderId' => $account->getFolderId()));
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
        $accountId = $this->params('accountId');

        try {
            $account = $this->accountModel->fetchById($accountId);
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
                $this->accountModel->deleteAccount($account);
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
        $accountId = $this->params('accountId');

        try {
            $account = $this->accountModel->fetchById($accountId);
        } catch (AccountNotFoundException $e) {
            return $this->notFoundAction();
        }

        if ($account->getUserId() != $user->getId())
            throw new ForbiddenException("Account of another user");

        if ($account->getFolderId() != $this->params('folderId'))
            return $this->notFoundAction();

        $data         = $this->accountModel->fetchAccountData($account)->getData();
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
        $accountId = $this->params('accountId');

        try {
            $account = $this->accountModel->fetchById($accountId);
        } catch (AccountNotFoundException $e) {
            return $this->notFoundAction();
        }

        if ($account->getUserId() != $user->getId())
            throw new ForbiddenException("Account of another user");

        if ($account->getFolderId() != $this->params('folderId'))
            return $this->notFoundAction();

        $accountData = $this->accountModel->fetchAccountData($account);

        $filepath = $this->iconService->getFileFromAccount($accountData);

        $response = new \Zend\Http\Response\Stream();
        $response->setStream(fopen($filepath, 'r'));

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'image/png')
            ->addHeaderLine('Expires', '+ 7 day')
            ->addHeaderLine('Cache-Control', 'public, max-age=604800')
            ->addHeaderLine('Pragma', 'cache')
            ->addHeaderLine('Content-Length', filesize($filepath));

        return $response;
    }

}
