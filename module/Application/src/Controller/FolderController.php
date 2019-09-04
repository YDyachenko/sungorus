<?php

namespace Application\Controller;

use Application\Exception\FolderNotFoundException;
use Application\Exception\ForbiddenException;
use Application\Form\FolderForm;
use Application\Model\Folder;
use Application\Model\User;
use Application\Repository\AccountRepositoryInterface;
use Application\Repository\FolderRepositoryInterface;
use Zend\Form\Element\Csrf;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

/**
 * @method User identity()
 */
class FolderController extends AbstractActionController
{

    /**
     * @var FolderRepositoryInterface
     */
    protected $repository;

    /**
     * @var AccountRepositoryInterface
     */
    protected $accounts;

    /**
     * @var FolderForm
     */
    protected $form;

    public function __construct(
        FolderRepositoryInterface $repository,
        AccountRepositoryInterface $accounts,
        FolderForm $form
    ) {
        $this->repository = $repository;
        $this->accounts   = $accounts;
        $this->form       = $form;
    }

    /**
     * List accounts
     * @throws ForbiddenException
     */
    public function accountsAction()
    {
        $user = $this->identity();
        $id   = (int)$this->params('folderId', 0);

        try {
            $folder = $this->repository->findById($id);
        } catch (FolderNotFoundException $e) {
            return $this->notFoundAction();
        }

        if ($folder->getUserId() != $user->getId()) {
            throw new ForbiddenException("Folder of another user");
        }

        return [
            'folders'  => $this->repository->findByUser($user),
            'folder'   => $folder,
            'folderId' => $id,
            'accounts' => $this->accounts->findByFolder($folder),
        ];
    }

    /**
     * Create new folder
     */
    public function addAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $folder = new Folder();
                $folder->exchangeArray($this->form->getData());
                $folder->setUserId($this->identity()->getId());

                $this->repository->save($folder);

                return $this->redirect()->toRoute('folders/folder/accounts', ['folderId' => $folder->getId()]);
            }
        }

        return [
            'form' => $this->form,
        ];
    }

    /**
     * Edit existing folder
     * @throws ForbiddenException
     */
    public function editAction()
    {
        $user = $this->identity();
        $id   = (int)$this->params('folderId', 0);

        try {
            $folder = $this->repository->findById($id);
        } catch (FolderNotFoundException $e) {
            return $this->notFoundAction();
        }

        if ($folder->getUserId() != $user->getId()) {
            throw new ForbiddenException("Folder of another user");
        }

        $this->form->bind($folder);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $this->repository->save($folder);

                return $this->redirect()->toRoute('folders/folder/accounts', ['folderId' => $folder->getId()]);
            }
        }

        return [
            'form'   => $this->form,
            'folder' => $folder,
        ];
    }

    /**
     * Delete existing folder
     * @throws ForbiddenException
     */
    public function deleteAction()
    {
        $user = $this->identity();
        $id   = (int)$this->params('folderId', 0);

        try {
            $folder = $this->repository->findById($id);
        } catch (FolderNotFoundException $e) {
            return $this->notFoundAction();
        }

        if ($folder->getUserId() != $user->getId()) {
            throw new ForbiddenException("Folder of another user");
        }

        $request = $this->getRequest();
        $token   = new Csrf('deleteFolder');
        $return  = [];

        if ($request->isPost()) {
            $validator = $token->getCsrfValidator();

            $return['success'] = false;

            if ($validator->isValid($request->getPost('token'))) {
                $this->repository->delete($folder);

                $return['location'] = $this->url()->fromRoute('home');
                $return['success']  = true;
            }
        } else {
            $return['token'] = $token->getValue();
        }

        return new JsonModel($return);
    }
}
