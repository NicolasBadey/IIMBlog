<?php
namespace App\Model\ETL\Article;

use App\Entity\Article;
use App\Model\ETL\AbstractTransform;
use App\Model\ETL\TransformInterface;

class ArticleTransform extends AbstractTransform implements TransformInterface
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
            'location' => [
                'lat' => $article->getLatitude(),
                'lon' => $article->getLongitude(),
            ],
        ];
    }
}
