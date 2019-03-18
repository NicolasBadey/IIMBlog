<?php
namespace App\EventSubscriber;

use App\Entity\Article;
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
     * @var LoadArticle
     */
    protected $loadArticle;


    public function __construct(ETLArticle $etlArticle, LoadArticle $loadArticle)
    {
        $this->etlArticle = $etlArticle;
        $this->loadArticle = $loadArticle;
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
            $this->loadArticle->deleteDocument($entity->getId());
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
