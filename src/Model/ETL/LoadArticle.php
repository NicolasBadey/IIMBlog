<?php

namespace App\Model\ETL;


use App\Model\ClientElasticSearch;
use Symfony\Component\Validator\Mapping\Loader\AbstractLoader;

class LoadArticle extends AbstractLoad
{
    public function getAlias()
    {
        return 'article';
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