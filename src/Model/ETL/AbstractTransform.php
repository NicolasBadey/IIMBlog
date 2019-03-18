<?php
namespace App\Model\ETL;

class AbstractTransform
{
    public function transformObjects(array $objects) :array
    {
        return array_map([
            $this, 'transformObject'
        ], $objects);
    }
}
