<?php

namespace Routmoute\Bundle\RoutmouteSireneBundle\Exception;

final class SireneServiceUnavailableException extends \Exception
{
    public function getReason(): string
    {
        return 'Service unavailable.';
    }
}
