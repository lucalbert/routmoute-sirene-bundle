<?php

namespace Routmoute\Bundle\RoutmouteSireneBundle\Exception;

final class SireneMisformattedParametersException extends \Exception
{
    public function getReason(): string
    {
        return 'Incorrect number of parameters or parameters are incorrectly formatted.';
    }
}
