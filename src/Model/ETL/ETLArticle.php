<?php
namespace App\Model\ETL;

use App\Entity\Article;
use App\Repository\ArticleRepository;

class ETLArticle
{
    /**
     * @var LoadArticle
     */
    protected $loadArticle;

    /**
     * @var ExtractArticle
     */
    protected $extractArticle;

    /**
     * @var TransformArticle
     */
    protected $transform;

    /**
     * ETLArticle constructor.
     * @param LoadArticle $loadArticle
     * @param ExtractArticle $extractArticle
     * @param TransformArticle $transform
     */
    public function __construct(LoadArticle $loadArticle, ExtractArticle $extractArticle, TransformArticle $transform)
    {
        $this->loadArticle = $loadArticle;
        $this->extractArticle = $extractArticle;
        $this->transform = $transform;
    }

    public function indexAll()
    {
        $this->loadArticle->preLoad();

        //Extract
        $articlesEntities = $this->extractArticle->getEntities();

        //Transform
        $articlesTransformed = $this->transform->transformArticles($articlesEntities);

        //Load
        $this->loadArticle->bulkLoad($articlesTransformed);

        $this->loadArticle->postLoad();
    }

    /**
     * @param Article $article
     */
    public function indexOne(Article $article)
    {
        $articleTransformed = $this->transform->transformArticle($article);

        $this->loadArticle->singleLoad($articleTransformed);
    }
}
