<?php

namespace Routmoute\Bundle\RoutmouteSireneBundle\Exception;

final class SireneInvalidPermissionsException extends \Exception
{
    public function getReason(): string
    {
        return 'Insufficient rights to view data from this unit.';
    }
}
