<?php

namespace App\Model\ETL\Article;

use App\Model\ETL\ExtractInterface;
use App\Repository\ArticleRepository;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class Extract implements ExtractInterface
{
    /**
     * @var ArticleRepository
     */
    protected $articleRepository;

    /**
     * ExtractArticle constructor.
     * @param ArticleRepository $articleRepository
     */
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param array $ids
     * @return AdapterInterface
     */
    public function getAdapter(array $ids): AdapterInterface
    {
        return new DoctrineORMAdapter($this->articleRepository->getSearchQueryBuilder($ids));
    }
}
