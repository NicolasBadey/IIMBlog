<?php
namespace App\EventSubscriber;

use App\Entity\Article;
use App\Model\ETL\ETLArticle;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;

class SearchIndexerSubscriber implements EventSubscriber
{
    /**
     * @var ETLArticle
     */
    protected $etl_article;


    public function __construct(ETLArticle $etl_article)
    {
        $this->etl_article = $etl_article;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
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

    public function index(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Article) {
            $this->etl_article->indexOne($entity);
        }
    }
}
