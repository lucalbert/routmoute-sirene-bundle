<?php

namespace Routmoute\Bundle\RoutmouteSireneBundle\Exception;

final class SireneServerErrorException extends \Exception
{
    public function getReason(): string
    {
        return 'Sirene API Internal Server Error.';
    }
}
