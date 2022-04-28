<?php

namespace Routmoute\Bundle\RoutmouteSireneBundle\Exception;

final class SireneEmptyParamsException extends \Exception
{
    public function getReason(): string
    {
        return 'Array of params is empty.';
    }
}
