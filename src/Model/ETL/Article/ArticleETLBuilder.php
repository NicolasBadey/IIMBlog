<?php
namespace App\Model\ETL\Article;

use ElasticsearchETL\AbstractETLBuilder;

class ArticleETLBuilder extends AbstractETLBuilder
{

    /**
     * ETLBuilder constructor.
     * @param ArticleLoad $load
     * @param ArticleExtract $extract
     * @param ArticleTransform $transform
     */
    public function __construct(ArticleLoad $load, ArticleExtract $extract, ArticleTransform $transform)
    {
        $this->load = $load;
        $this->extract = $extract;
        $this->transform = $transform;
    }
}
