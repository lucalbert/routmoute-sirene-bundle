<?php

namespace Routmoute\Bundle\RoutmouteSireneBundle\Exception;

final class SireneNotExistException extends \Exception
{
    public function getReason(): string
    {
        return 'Company not found in the Sirene database.';
    }
}
