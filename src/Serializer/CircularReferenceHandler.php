<?php

namespace App\Serializer;

class CircularReferenceHandler
{
    public function __invoke($object)
    {
        if (method_exists($object, 'getId')) {
            return $object->getId();
        }

        return null;
    }
}
