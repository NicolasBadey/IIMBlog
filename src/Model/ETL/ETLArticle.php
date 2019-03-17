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

    public function indexAll(bool $alias = true)
    {
        $this->loadArticle->preLoad();

        //Extract
        $articlesEntities = $this->extractArticle->getEntities();

        //Transform
        $articlesTransformed = $this->transform->transformArticles($articlesEntities);

        //without Alias, the alias name become the index name
        $index = $alias ? $this->loadArticle->getIndex():$this->loadArticle->getAlias();

        //Load
        $this->loadArticle->bulkLoad($articlesTransformed, $index);

        $this->loadArticle->postLoad();
    }

    /**
     * @param Article $article
     */
    public function indexOne(Article $article)
    {
        $articleTransformed = $this->transform->transformArticle($article);

        if ($this->loadArticle->aliasExists()) {
            $this->loadArticle->singleLoad($articleTransformed, $this->loadArticle->getAlias());
        } else {
            $this->loadArticle->preLoad();

            $this->loadArticle->singleLoad($articleTransformed, $this->loadArticle->getIndex());

            $this->loadArticle->postLoad();
        }


    }
}
