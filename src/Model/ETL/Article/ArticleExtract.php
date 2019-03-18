<?php

namespace App\Model\ETL\Article;

use App\Model\ETL\ExtractInterface;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * @param array $ids
     * @return AdapterInterface
     */
    public function getAdapter(array $ids): AdapterInterface
    {
        return new DoctrineORMAdapter($this->articleRepository->getSearchQueryBuilder($ids));
    }

    public function purgeData(): void
    {
        $this->em->clear();
        gc_collect_cycles();
    }
}
