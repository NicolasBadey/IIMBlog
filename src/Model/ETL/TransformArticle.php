<?php
namespace App\Model\ETL;

use App\Entity\Article;
use App\Logger\ElasticsearchLogger;
use Elasticsearch\ClientBuilder;
use phpDocumentor\Reflection\Types\Iterable_;

class TransformArticle
{
    public function transformArticles($articles) :array
    {
        return array_map([
            $this, 'transformArticle'
        ], $articles);
    }

    public function transformArticle(Article $article) : array
    {
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'location' => [
                'lat' => $article->getLatitude(),
                'lon' => $article->getLongitude(),
            ],
        ];
    }
}
