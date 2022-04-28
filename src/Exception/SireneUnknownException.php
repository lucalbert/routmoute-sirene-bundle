<?php

namespace Routmoute\Bundle\RoutmouteSireneBundle\Exception;

final class SireneUnknownException extends \Exception
{
    public function getReason(): string
    {
        return 'Unknown error.';
    }
}
