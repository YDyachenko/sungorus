<?php

namespace Application\Validator;

use Zend\Validator\AbstractValidator;
use Application\Exception\InvalidArgumentException;

class Bcrypt extends AbstractValidator
{
    const HASH = 'hash';

    protected $messageTemplates = [
        self::HASH => "'%value%' is not a valid value"
    ];
    protected $hash;

    /**
     * Sets validator options
     *
     * @param  array|Traversable $options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (! array_key_exists('hash', $options)) {
            throw new InvalidArgumentException("Missing option 'hash'");
        }

        $this->setHash($options['hash']);

        parent::__construct($options);
    }

    /**
     * Get hash
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set hash
     * @param string $hash
     * @return Bcrypt
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($value)
    {
        $bcrypt = new \Zend\Crypt\Password\Bcrypt();
        if (! $bcrypt->verify($value, $this->hash)) {
            $this->error(self::HASH, $value);
            return false;
        }

        return true;
    }
}
