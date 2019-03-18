<?php

namespace App\Model\ETL;

use App\Model\ClientElasticSearch;
use Symfony\Component\Validator\Mapping\Loader\AbstractLoader;

class LoadArticle extends AbstractLoad
{
    public static function getAlias()
    {
        return 'article_'.strtolower($_SERVER['APP_ENV']);
    }

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
