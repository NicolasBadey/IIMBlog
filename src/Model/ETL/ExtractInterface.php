<?php
namespace App\Model\ETL;

use Pagerfanta\Adapter\AdapterInterface;

interface ExtractInterface
{
    public function getAdapter(array $ids): AdapterInterface;
    public function purgeData(): void;
}
