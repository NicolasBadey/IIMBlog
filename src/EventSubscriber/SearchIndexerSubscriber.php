<?php
namespace App\EventSubscriber;

use App\Entity\Article;
use App\Model\ClientElasticSearch;
use App\Model\ETL\ETLArticle;
use App\Model\ETL\LoadArticle;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;

class SearchIndexerSubscriber implements EventSubscriber
{
    /**
     * @var ETLArticle
     */
    protected $etlArticle;

    /**
     * @var ClientElasticSearch
     */
    protected $clientElasticSearch;


    public function __construct(ETLArticle $etlArticle, ClientElasticSearch $clientElasticSearch)
    {
        $this->etlArticle = $etlArticle;
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
                'index' => LoadArticle::getAlias(),
                'id' => $entity->getId()
            ]);
        }
    }

    public function index(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Article) {
            $this->etlArticle->indexOne($entity);
        }
    }
}
