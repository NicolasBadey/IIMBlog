<?php

namespace App\Model\ETL;

use App\Repository\ArticleRepository;

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
    public function getEntities(): array
    {
        return $this->articleRepository->findAll();
    }
}
