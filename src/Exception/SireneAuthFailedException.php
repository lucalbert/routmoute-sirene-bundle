<?php

namespace Routmoute\Bundle\RoutmouteSireneBundle\Exception;

final class SireneAuthFailedException extends \Exception
{
    public function getReason(): string
    {
        return 'Authentication failed.';
    }
}
