<?php

namespace Application\Controller;

use Application\Form;
use Application\Model\Folder;
use Application\Repository\FolderRepositoryInterface;
use Application\Repository\AccountRepositoryInterface;
use Application\Exception\FolderNotFoundException;
use Application\Exception\ForbiddenException;
use Zend\Form\Element\Csrf;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

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

    public function __construct(FolderRepositoryInterface $repository, AccountRepositoryInterface $accounts)
    {
        $this->repository = $repository;
        $this->accounts   = $accounts;
    }

    /**
     * List accounts
     * @return array
     * @throws ForbiddenException
     */
    public function accountsAction()
    {
        $user = $this->identity();
        $id   = (int) $this->params('folderId', 0);

        try {
            $folder = $this->repository->findById($id);
        } catch (FolderNotFoundException $e) {
            return $this->notFoundAction();
        }

        if ($folder->getUserId() != $user->getId())
            throw new ForbiddenException("Folder of another user");

        return [
            'folders'  => $this->repository->findByUser($user),
            'folderId' => $id,
            'accounts' => $this->accounts->findByFolder($folder),
        ];
    }

    /**
     * Create new folder
     * @return array
     */
    public function addAction()
    {
        $form    = new Form\FolderForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $folder = new Folder();
                $folder->exchangeArray($form->getData());
                $folder->setUserId($this->identity()->getId());

                $this->repository->save($folder);

                return $this->redirect()->toRoute('folders/folder', ['folderId' => $folder->getId()]);
            }
        }

        return [
            'form' => $form
        ];
    }

    /**
     * Edit existing folder
     * @return array
     * @throws ForbiddenException
     */
    public function editAction()
    {
        $user = $this->identity();
        $id   = (int) $this->params('folderId', 0);

        try {
            $folder = $this->repository->findById($id);
        } catch (FolderNotFoundException $e) {
            return $this->notFoundAction();
        }

        if ($folder->getUserId() != $user->getId())
            throw new ForbiddenException("Folder of another user");

        $form = new Form\FolderForm();
        $form->bind($folder);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->repository->save($folder);

                return $this->redirect()->toRoute('folders/folder', ['folderId' => $folder->getId()]);
            }
        }

        return [
            'form' => $form
        ];
    }

    /**
     * Delete existing folder
     * @return JsonModel
     * @throws ForbiddenException
     */
    public function deleteAction()
    {
        $user = $this->identity();
        $id   = (int) $this->params('folderId', 0);

        try {
            $folder = $this->repository->findById($id);
        } catch (FolderNotFoundException $e) {
            return $this->notFoundAction();
        }

        if ($folder->getUserId() != $user->getId())
            throw new ForbiddenException("Folder of another user");

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
