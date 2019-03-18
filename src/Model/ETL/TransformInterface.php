<?php
namespace App\Model\ETL;

interface TransformInterface
{
    public function transformObjects(array $objects): array;
    public function transformObject($object): array;
}
