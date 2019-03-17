<?php
namespace App\tests\Model\ETL;

use App\Entity\Article;
use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TransformArticleTest extends KernelTestCase
{

    /**
     * @var Prophet
     */
    private $prophet;

    protected function setup(): void
    {
        $this->prophet = new Prophet();
    }

    protected function tearDown(): void
    {
        $this->prophet->checkPredictions();
    }

    public function testTransformArticles()
    {
        $article = $this->prophet->prophesize('App\Entity\Article');

        $article->getId()->willReturn(42);
        $article->getLongitude()->willReturn(42.24);
        $article->getLatitude()->willReturn(42.24);
        $article->getContent()->willReturn('lorem');
        $article->getTitle()->willReturn('title42');

        self::bootKernel();
        $container = self::$container;

        $articleArray = $container->get('App\Model\ETL\TransformArticle')->transformArticles([$article->reveal()]);

        $this->assertEquals([
            [
                'id' => 42,
                'title' => 'title42',
                'content' => 'lorem',
                'location' => [
                    'lat' => 42.24,
                    'lon' =>42.24
                ],
            ]
        ], $articleArray);
    }
}
