<?php

namespace App\Model\ETL;

use App\Repository\ArticleRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class ExtractArticle
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
     * @return array
     */
    public function getAdapter(array $ids): DoctrineORMAdapter
    {
        return new DoctrineORMAdapter($this->articleRepository->getSearchQueryBuilder($ids));
    }
}
