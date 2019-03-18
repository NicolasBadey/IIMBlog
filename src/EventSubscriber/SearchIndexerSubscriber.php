<?php
namespace App\EventSubscriber;

use App\Entity\Article;
use App\Model\ClientElasticSearch;
use App\Model\ETL\Article\ETLBuilder;
use App\Model\ETL\Article\Load;
use App\Model\ETL\ETL;
use App\Model\ETL\LoadArticle;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;

class SearchIndexerSubscriber implements EventSubscriber
{
    /**
     * @var ETLBuilder
     */
    protected $ETLBuilder;

    /**
     * @var ClientElasticSearch
     */
    protected $clientElasticSearch;


    public function __construct(ETLBuilder $articleETLBuilder, ClientElasticSearch $clientElasticSearch)
    {
        $this->ETLBuilder = $articleETLBuilder;
        $this->clientElasticSearch = $clientElasticSearch;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Article) {
            $this->clientElasticSearch->delete([
                'index' => Load::getAlias(),
                'id' => $entity->getId()
            ]);
        }
    }

    public function index(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Article) {
            $this->ETLBuilder->build()->indexOne($entity);
        }
    }
}
