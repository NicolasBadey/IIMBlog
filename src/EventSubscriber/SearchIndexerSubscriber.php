<?php
namespace App\EventSubscriber;

use App\Entity\Article;
use App\Model\ETL\ETLArticle;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchIndexerSubscriber implements EventSubscriberInterface
{
    /**
     * @var ETLArticle
     */
    protected $etl_article;

    /**
     * @var string
     */
    protected $type = 'article';


    public function __construct(ETLArticle $etl_article)
    {
        $this->etl_article = $etl_article;
    }

    public static function getSubscribedEvents()
    {
        return [
            'postPersist',
            'postUpdate',
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
