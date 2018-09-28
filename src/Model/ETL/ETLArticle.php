<?php
namespace App\Model\ETL;

use App\Entity\Article;
use App\Model\ClientElasticSearch;
use App\Repository\ArticleRepository;

class ETLArticle extends AbstractETL
{
    /**
     * @var ClientElasticSearch
     */
    protected $client;

    /**
     * @var ArticleRepository
     */
    protected $articleRepository;

    /**
     * @var Transform
     */
    protected $transform;

    /**
     * ETLCommand constructor.
     * @param ClientElasticSearch $client
     */
    public function __construct(ClientElasticSearch $client, ArticleRepository $articleRepository, Transform $transform)
    {
        $this->client = $client;
        $this->articleRepository = $articleRepository;
        $this->transform = $transform;
    }

    public function indexAll(string $type)
    {
        $aliase = $this->client->getIndex();
        $index = $aliase.'_'.(new \DateTime())->format('U');

        //create Index
        $this->client->indices()->create([
            'index' => $index
        ]);

        //update mapping
        $this->client->indices()->putMapping($this->getMapping($index, $type));

        //Extract : make a Class Model if it become more complex
        $articlesORM = $this->articleRepository->findAll();

        //Transform
        $articlesTransformed = $this->transform->transformArticles($articlesORM);

        //Load
        $this->client->bulk($articlesTransformed, $index, $type);

        //invert aliase
        $this->invertAliase($index, $aliase);

        //delete unused indices
        $this->deleteUnusedIndices($index, $aliase);
    }

    public function indexOne(Article $article)
    {
        $articleTransformed = $this->transform->transformArticle($article);

        $this->client->index($articleTransformed, $this->client->getIndex());
    }
}
