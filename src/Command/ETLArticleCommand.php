<?php
namespace App\Command;

use App\Model\ETL\AbstractETLCommand;
use App\Model\ETL\Article\ETLBuilder;

class ETLArticleCommand extends AbstractETLCommand
{

    /**
     * ETLArticleCommand constructor.
     * @param ETLBuilder $ETLArticleBuilder
     */
    public function __construct(ETLBuilder $ETLArticleBuilder)
    {
        parent::__construct('app:etl:article');

        $this->ETLBuilder = $ETLArticleBuilder;
    }
}
