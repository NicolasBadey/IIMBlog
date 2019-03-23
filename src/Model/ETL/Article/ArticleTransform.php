<?php
namespace App\Model\ETL\Article;

use App\Entity\Article;
use ElasticsearchETL\AbstractTransform;


class ArticleTransform extends AbstractTransform
{
    /**
     * @param $article Article
     * @return array
     */
    public function transformObject($article) : array
    {
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'category' => $article->getCategory() ? $article->getCategory()->getName(): null ,
            'location' => [
                'lat' => $article->getLatitude(),
                'lon' => $article->getLongitude(),
            ],
        ];
    }
}
