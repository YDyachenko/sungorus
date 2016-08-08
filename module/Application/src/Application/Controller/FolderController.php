<?php

namespace Application\Controller;

use Application\Form;
use Application\Model\FolderEntity;
use Application\Exception\FolderNotFoundException;
use Application\Exception\ForbiddenException;
use Zend\Form\Element\Csrf;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class FolderController extends AbstractActionController
{

    /**
     * @var \Application\Model\FolderModel
     */
    protected $folderModel;

    /** @var \Application\Model\AccountModel */
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
     * List accounts
     * @return array
     * @throws ForbiddenException
     */
    public function viewAction()
    {
        $user     = $this->identity();
        $folderId = $this->params('folderId', 0);

        try {
            $folder = $this->folderModel->fetchById($folderId);
        } catch (FolderNotFoundException $e) {
            return $this->notFoundAction();
        }

        if ($folder->getUserId() != $user->getId())
            throw new ForbiddenException("Folder of another user");

        return [
            'folders'  => $this->folderModel->fetchByUser($user),
            'folderId' => $folderId,
            'accounts' => $this->accountModel->fetchByFolder($folder),
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
                $folder = new FolderEntity();
                $folder->exchangeArray($form->getData());
                $folder->setUserId($this->identity()->getId());

                $this->folderModel->saveFolder($folder);

                return $this->redirect()->toRoute('folder', ['folderId' => $folder->getId()]);
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
        $user     = $this->identity();
        $folderId = $this->params('folderId', 0);

        try {
            $folder = $this->folderModel->fetchById($folderId);
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
                $this->folderModel->saveFolder($folder);

                return $this->redirect()->toRoute('folder', ['folderId' => $folder->getId()]);
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
        $user     = $this->identity();
        $folderId = $this->params('folderId', 0);

        try {
            $folder = $this->folderModel->fetchById($folderId);
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
                $this->folderModel->deleteFolder($folder);

                $return['location'] = $this->url()->fromRoute('home');
                $return['success']  = true;
            }
        } else {
            $return['token'] = $token->getValue();
        }

        return new JsonModel($return);
    }

}
