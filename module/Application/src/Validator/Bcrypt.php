<?php

namespace Application\Validator;

use Application\Exception\InvalidArgumentException;
use Traversable;
use Zend\Validator\AbstractValidator;

class Bcrypt extends AbstractValidator
{
    const NOT_MATCH = 'bcryptNotMatch';

    protected $messageTemplates = [
        self::NOT_MATCH => "'%value%' is not a valid value",
    ];

    /**
     * @var string
     */
    protected $hash;

    /**
     * Sets validator options
     * @param array|Traversable $options
     * @throws InvalidArgumentException
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
     * @return self
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
            $this->error(self::NOT_MATCH, $value);
            return false;
        }

        return true;
    }
}
