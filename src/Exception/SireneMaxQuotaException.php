<?php

namespace Routmoute\Bundle\RoutmouteSireneBundle\Exception;

final class SireneMaxQuotaException extends \Exception
{
    public function getReason(): string
    {
        return 'API query quota exceeded.';
    }
}
