<?php
namespace App\Model\ETL\Article;

use App\Model\ETL\AbstractETLBuilder;
use Doctrine\ORM\EntityManagerInterface;

class ArticleETLBuilder extends AbstractETLBuilder
{

    /**
     * ETLBuilder constructor.
     * @param ArticleLoad $load
     * @param ArticleExtract $extract
     * @param ArticleTransform $transform
     */
    public function __construct(ArticleLoad $load, ArticleExtract $extract, ArticleTransform $transform,EntityManagerInterface $em)
    {
        $this->load = $load;
        $this->extract = $extract;
        $this->transform = $transform;
    }
}
