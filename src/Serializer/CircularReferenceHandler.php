<?php

namespace App\Serializer;

class CircularReferenceHandler
{
    public static function handleCircularReference($object)
    {
        return $object->getId();
    }
}
