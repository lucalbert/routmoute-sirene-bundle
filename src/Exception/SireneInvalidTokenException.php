<?php

namespace Routmoute\Bundle\RoutmouteSireneBundle\Exception;

final class SireneInvalidTokenException extends \Exception
{
    public function getReason(): string
    {
        return 'Invalid access token.';
    }
}
