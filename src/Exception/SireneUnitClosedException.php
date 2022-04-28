<?php

namespace Routmoute\Bundle\RoutmouteSireneBundle\Exception;

final class SireneUnitClosedException extends \Exception
{
    public function getReason(): string
    {
        return 'Legal unit closed due to duplication.';
    }
}
