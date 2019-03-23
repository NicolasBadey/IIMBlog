<?php
namespace App\tests\Model\ETL\Article;

use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ArticleLoadTest extends KernelTestCase
{

    public function testGetAlias()
    {
        self::bootKernel();
        $container = self::$container;

        $alias = $container->get('App\Model\ETL\Article\ArticleLoad')->getAlias();

        $this->assertEquals('article_test', $alias);
    }

    public function testGetMappingProperties()
    {
        self::bootKernel();
        $container = self::$container;

        $mapping = $container->get('App\Model\ETL\Article\ArticleLoad')->getMappingProperties();

        $this->assertEquals([
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
            ]
        ], $mapping);

    }
}
