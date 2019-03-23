<?php

namespace App\Model\ETL\Article;

use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use ElasticsearchETL\ExtractInterface;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class ArticleExtract implements ExtractInterface
{
    /**
     * @var ArticleRepository
     */
    protected $articleRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * ExtractArticle constructor.
     * @param ArticleRepository $articleRepository
     */
    public function __construct(ArticleRepository $articleRepository, EntityManagerInterface $em)
    {
        $this->articleRepository = $articleRepository;
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public function getAdapter(array $ids =[]): AdapterInterface
    {
        return new DoctrineORMAdapter($this->articleRepository->getSearchQueryBuilder($ids));
    }

    /**
     * @inheritdoc
     */
    public function purgeData(): void
    {
        $this->em->clear();
        gc_collect_cycles();

        //test memory usage
        //echo "\n".(round(memory_get_usage()/1000000,2)).' mo';
    }
}
