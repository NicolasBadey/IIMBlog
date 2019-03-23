<?php
namespace App\tests\Model\ETL\Article;

use Pagerfanta\Adapter\AdapterInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ArticleExtractTest extends KernelTestCase
{

    public function testGetAdapter()
    {
        self::bootKernel();
        $container = self::$container;

        $adapter = $container->get('App\Model\ETL\Article\ArticleExtract')->getAdapter();

        $this->assertInstanceOf(AdapterInterface::class, $adapter);
    }
}
