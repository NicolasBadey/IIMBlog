<?php
namespace App\Model\ETL;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;
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
     * Be careful,with SQL Limit statement become time consuming after 500
     */
    protected $maxPerPAge = 500;

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
     * @param int $maxPerPAge
     */
    public function setMaxPerPAge(int $maxPerPAge)
    {
        $this->maxPerPAge = $maxPerPAge;
    }




    /**
     * @param OutputInterface $output
     *
     * if you do'ont want output, send NullOutput object
     */
    public function run(OutputInterface $output, bool $live = false, array $ids = []): void
    {
        $this->load->setLiveMode($live);

        $this->load->preLoad();

        //Extract
        $adapter = $this->extract->getAdapter($ids);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($this->maxPerPAge);
        $nbPages = $pagerfanta->getNbPages();

        if ($pagerfanta->getNbResults() === 0) {
            $output->writeln('no documents to index for '.$this->load::getAlias());

            return;
        }

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

            $output->write('.');

            //echo round(memory_get_usage()/1000) . "\n";
        }

        $this->load->postLoad();

        $output->writeln("\n".$pagerfanta->getNbResults().' documents indexed in '.$this->load::getAlias());
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
