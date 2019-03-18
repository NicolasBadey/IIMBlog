<?php
namespace App\Command;

use App\Model\ETL\AbstractETLCommand;
use App\Model\ETL\Article\ArticleETLBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\NullHandler;
use Psr\Log\LoggerInterface;

class ETLArticleCommand extends AbstractETLCommand
{

    /**
     * ETLArticleCommand constructor.
     * @param ArticleETLBuilder $ETLArticleBuilder
     */
    public function __construct(ArticleETLBuilder $ETLArticleBuilder, EntityManagerInterface $em, LoggerInterface $logger)
    {
        //desactivate logs
        $logger->pushHandler(new NullHandler());
        $em->getConfiguration()->setSQLLogger(null);

        parent::__construct('app:etl:article');

        $this->ETLBuilder = $ETLArticleBuilder;
    }
}
