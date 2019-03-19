<?php
namespace App\Model\ETL;

use Pagerfanta\Pagerfanta;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ETL
 * @package App\Model\ETL
 *
 * You cannot directly use this class, use a Builder for dependency injection
 */
class ETL
{
    /**
     * @var LoadInterface
     */
    protected $load;
    public $em;

    /**
     * @var ExtractInterface
     */
    protected $extract;

    /**
     * @var TransformInterface
     */
    protected $transform;

    /**
     * @var int
     *
     * Be careful,with SQL, Limit statement become time consuming after 1000, test the good value depending ES load
     */
    protected $maxPerPage = 2000;

    /**
     * @param ExtractInterface $extract
     * @return $this
     */
    public function setExtract(ExtractInterface $extract)
    {
        $this->extract = $extract;

        return $this;
    }

    /**
     * @param TransformInterface $transform
     * @return $this
     */
    public function setTransform(TransformInterface $transform)
    {
        $this->transform = $transform;

        return $this;
    }

    /**
     * @param LoadInterface $load
     * @return $this
     */
    public function setLoad(LoadInterface $load)
    {
        $this->load = $load;

        return $this;
    }

    protected function __construct()
    {
    }

    public static function create()
    {
        return new self();
    }

    /**
     * @param int $maxPerPage
     */
    public function setMaxPerPage(int $maxPerPage)
    {
        $this->maxPerPage = $maxPerPage;
    }

    /**
     * @param OutputInterface $output
     *
     * if you do'ont want output, send NullOutput object
     */
    public function run(OutputInterface $output, bool $live = false, array $ids = []): void
    {
        //$timeStart = microtime(true);


        $this->load->setLiveMode($live);

        $this->load->preLoad();

        //Extract
        $adapter = $this->extract->getAdapter($ids);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($this->maxPerPage);
        $nbPages = $pagerfanta->getNbPages();

        if ($pagerfanta->getNbResults() === 0) {
            $output->writeln('no documents to index for '.$this->load::getAlias());

            return;
        }

        $output->writeln('<info>'.$pagerfanta->getNbResults().' documents will be indexed in '.$this->load::getAlias().'</info>');

        $progressBar = new ProgressBar($output, $pagerfanta->getNbPages());
        $progressBar->start();

        for ($page = 1 ; $page <= $nbPages ;$page++) {
            $pagerfanta->setCurrentPage($page);

            /**
             * @var $objects \ArrayIterator
             */
            $objects = $pagerfanta->getCurrentPageResults();

            //Transform
            $objectsTransformed = $this->transform->transformObjects($objects->getArrayCopy());

            //memory optimisation
            $objects = null;

            //Load
            $this->load->bulkLoad($objectsTransformed);

            $this->extract->purgeData();

            $progressBar->advance();
        }

        $this->load->postLoad();

        $progressBar->finish();

        $output->writeln('');
    }

    /**
     * @param $object
     * @return array
     */
    public function indexOne($object, bool $createIndexIdNotExists = true): array
    {
        $objectTransformed = $this->transform->transformObject($object);

        return $this->load->singleLoad($objectTransformed, $createIndexIdNotExists);
    }
}
