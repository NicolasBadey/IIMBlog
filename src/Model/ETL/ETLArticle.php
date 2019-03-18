<?php
namespace App\Model\ETL;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Console\Output\OutputInterface;

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

    public function indexAll(array $ids, bool $live, OutputInterface $output = null)
    {
        $this->loadArticle->setLiveMode($live);

        $this->loadArticle->preLoad();

        //Extract
        $adapter = $this->extractArticle->getAdapter($ids);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(500);
        $nbPages = $pagerfanta->getNbPages();

        if ($pagerfanta->getNbResults() === 0) {
            $output->writeln('no documents to index for '.LoadArticle::getAlias());

            return;
        }

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
            $this->loadArticle->bulkLoad($articlesTransformed);

            if (null !== $output) {
                $output->write('.');
            }
        }

        $this->loadArticle->postLoad();

        if (null !== $output) {
            $output->writeln("\n".$pagerfanta->getNbResults().' documents indexed in '.LoadArticle::getAlias());
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
