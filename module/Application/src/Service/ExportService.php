<?php

namespace Application\Service;

use XMLWriter;
use Application\Model\User;
use Application\Service\AccountDataCipher;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Crypt\BlockCipher;

class ExportService
{

    const ROOT_ELEMENT_NAME = 'sungorus';

    /**
     *
     * @var Sql
     */
    protected $sql;

    /**
     *
     * @var AccountDataCipher
     */
    protected $cipher;

    public function __construct(AdapterInterface $adapter, AccountDataCipher $cipher)
    {
        $this->sql    = new Sql($adapter);
        $this->cipher = $cipher;
    }

    /**
     * Export user accounts in XML format
     * @param User $user
     * @return string XML
     */
    public function process(User $user)
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->setIndentString(str_repeat(' ', 4));

        $writer->startDocument('1.0', 'UTF-8');
        $writer->startElement(self::ROOT_ELEMENT_NAME);

        $select = $this->sql->select('folders');
        $select->where(['user_id' => $user->getId()]);

        $statement = $this->sql->prepareStatementForSqlObject($select);

        foreach ($statement->execute() as $folder) {
            $writer->startElement('folder');
            $writer->writeAttribute('name', $folder['name']);

            $this->writeAccounts($folder['id'], $writer);

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
    protected function writeAccounts($folder_id, XMLWriter $writer)
    {
        $select = $this->sql->select('accounts');
        $select
            ->columns(['name', 'favorite'])
            ->join('accounts_data', 'account_id = accounts.id', ['data'])
            ->where(['folder_id' => $folder_id]);

        $statement = $this->sql->prepareStatementForSqlObject($select);

        foreach ($statement->execute() as $account) {
            $writer->startElement('account');
            $writer->writeAttribute('name', $account['name']);
            $writer->writeAttribute('favorite', $account['favorite']);

            $json = $this->cipher->decrypt($account['data']);

            foreach (json_decode($json, true) as $key => $value) {
                $writer->writeElement($key, $value);
            }

            $writer->endElement();
        }
    }
}
