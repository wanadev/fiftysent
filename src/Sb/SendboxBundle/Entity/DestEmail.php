<?php

namespace Sb\SendboxBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\Email;

class DestEmail
{
    private $email;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('email', new Email());
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }
}