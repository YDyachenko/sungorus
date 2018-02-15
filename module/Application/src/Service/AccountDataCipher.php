<?php

namespace Application\Service;

use Zend\Crypt\BlockCipher;

class AccountDataCipher extends BlockCipher
{

    /**
     * {@inheritdoc}
     */
    protected $binaryOutput = true;

}
