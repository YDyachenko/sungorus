<?php

namespace Application\Service;

use XMLWriter;
use Application\Model\UserEntity;
use Application\Model\FolderEntity;

class ExportService
{
    
    const ROOT_ELEMENT_NAME = 'sungorus';

    /**
     * @var \Application\Model\FolderModel
     */
    protected $folderModel;

    /**
     * @var \Application\Model\AccountModel
     */
    protected $accountModel;

    public function __construct($folderModel, $accountModel)
    {
        $this->folderModel  = $folderModel;
        $this->accountModel = $accountModel;
    }

    /**
     * Export user accounts in XML format
     * @param UserEntity $user
     * @return string XML
     */
    public function process(UserEntity $user)
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->setIndentString(str_repeat(' ', 4));

        $writer->startDocument('1.0', 'UTF-8');
        $writer->startElement(self::ROOT_ELEMENT_NAME);
        
        $folders = $this->folderModel->fetchByUser($user);
        
        foreach ($folders as $folder) {
            $writer->startElement('folder');
            $writer->writeAttribute('name', $folder->getName());
            
            $this->writeAccounts($folder, $writer);
            
            $writer->endElement();
        }
        
        $writer->endElement();
        $writer->endDocument();

        return $writer->outputMemory();
    }
    
    /**
     * Write into XMLWriter accounts in folder
     * @param FolderEntity $folder
     * @param XMLWriter $writer
     */
    protected function writeAccounts(FolderEntity $folder, XMLWriter $writer)
    {
        $result = $this->accountModel->exportAccountsByFolder($folder);
        
        foreach ($result['accounts'] as $account) {
            $writer->startElement('account');
            $writer->writeAttribute('name', $account->getName());
            $writer->writeAttribute('favorite', $account->getFavorite());
            
            $data = $result['data'][$account->getId()];
            
            foreach ($data as $key => $value) {
                $writer->writeElement($key, $value);
            }
            
            $writer->endElement();
        }
    }

}
