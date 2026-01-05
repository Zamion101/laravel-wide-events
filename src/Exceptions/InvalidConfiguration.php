<?php

namespace Zamion101\WideEvents\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Zamion101\WideEvents\Models\WideEvent;

class InvalidConfiguration extends Exception
{
    public static function modelIsNotValid(string $className): self
    {
        return new static("The given model class `{$className}` does not implement `".WideEvent::class.'` or it does not extend `'.Model::class.'`');
    }
}
