<?php
namespace App\Model\ETL\Article;

use App\Model\ETL\AbstractETLBuilder;

class ETLBuilder extends AbstractETLBuilder
{

    /**
     * ETLBuilder constructor.
     * @param Load $load
     * @param Extract $extract
     * @param Transform $transform
     */
    public function __construct(Load $load, Extract $extract, Transform $transform)
    {
        $this->load = $load;
        $this->extract = $extract;
        $this->transform = $transform;
    }
}
