<?php
namespace App\ETL;

use App\Entity\Article;
use App\Logger\ElasticsearchLogger;
use Elasticsearch\ClientBuilder;

class Transform
{
    public function transformArticles(array $articles) :array
    {
        return array_map([
            $this, 'transformArticle'
        ], $articles);
    }

    protected function transformArticle(Article $article) : array
    {
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
        ];
    }
}
