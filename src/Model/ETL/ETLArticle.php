<?php
namespace App\Model\ETL;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Pagerfanta\Pagerfanta;

class ETLArticle
{
    /**
     * @var LoadArticle
     */
    protected $loadArticle;

    /**
     * @var ExtractArticle
     */
    protected $extractArticle;

    /**
     * @var TransformArticle
     */
    protected $transform;

    /**
     * ETLArticle constructor.
     * @param LoadArticle $loadArticle
     * @param ExtractArticle $extractArticle
     * @param TransformArticle $transform
     */
    public function __construct(LoadArticle $loadArticle, ExtractArticle $extractArticle, TransformArticle $transform)
    {
        $this->loadArticle = $loadArticle;
        $this->extractArticle = $extractArticle;
        $this->transform = $transform;
    }

    public function indexAll(bool $alias = true, $output = null)
    {
        $this->loadArticle->preLoad();

        //Extract
        $adapter = $this->extractArticle->getAdapter();

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(500);
        $nbPages = $pagerfanta->getNbPages();

        for ($page = 1 ; $page <= $nbPages ;$page++) {
            $pagerfanta->setCurrentPage($page);

            /**
             * @var $articlesEntities \ArrayIterator
             */
            $articlesEntities = $pagerfanta->getCurrentPageResults();

            //Transform
            $articlesTransformed = $this->transform->transformArticles($articlesEntities->getArrayCopy());
            $articlesEntities = null;

            //Load
            $this->loadArticle->bulkLoad($articlesTransformed, $alias);

            if (null !== $output) {
                $output->write('.');
            }
        }

        $this->loadArticle->postLoad();

        if (null !== $output) {
            $output->writeln('');
            $output->writeln($pagerfanta->getNbResults().' documents indexed');
        }
    }

    /**
     * @param Article $article
     */
    public function indexOne(Article $article)
    {
        $articleTransformed = $this->transform->transformArticle($article);

        $this->loadArticle->singleLoad($articleTransformed);
    }
}
