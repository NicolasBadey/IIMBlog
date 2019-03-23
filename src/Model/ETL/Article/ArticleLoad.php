<?php

namespace App\Model\ETL\Article;

use App\Model\ElasticSearchClient;
use ElasticsearchETL\AbstractElasticsearchLoad;

class ArticleLoad extends AbstractElasticsearchLoad
{



    /**
     * @return string
     */
    public static function getAlias(): string
    {
        return 'article_'.strtolower(getenv('APP_ENV'));
    }

    /**
     * @return array
     */
    public function getMappingProperties() :array
    {
        // if you are multi language use : https://www.elastic.co/guide/en/elasticsearch/guide/current/mixed-lang-fields.html

        return [
            'location' => [
                'type' => 'geo_point'
            ],
            'title' => [
                'type' => 'text',
                'analyzer' => 'french'
            ],
            'content' => [
                'type' => 'text',
                'analyzer' => 'french'
            ],
        ];
    }
}
