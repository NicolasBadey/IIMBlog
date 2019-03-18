<?php

namespace App\Model\ETL\Article;

use App\Model\ClientElasticSearch;
use App\Model\ETL\AbstractLoad;
use App\Model\ETL\LoadInterface;
use Symfony\Component\Validator\Mapping\Loader\AbstractLoader;

class Load extends AbstractLoad implements LoadInterface
{
    /**
     * @return string
     */
    public static function getAlias(): string
    {
        return 'article_'.strtolower($_SERVER['APP_ENV']);
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
