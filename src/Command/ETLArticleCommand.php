<?php
namespace App\Command;

use App\Model\ETL\ETLArticle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ETLArticleCommand extends Command
{

    /**
     * @var ETLArticle
     */
    protected $etlArticle;

    /**
     * ETLCommand constructor.
     * @param ETLArticle $etl_article
     */
    public function __construct(ETLArticle $etlArticle)
    {
        parent::__construct();

        $this->etlArticle = $etlArticle;
    }


    protected function configure()
    {
        $this
            ->setName('app:etl:article')
            ->setDescription('ETL for populate Elasticsearch from SQL');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->etlArticle->indexAll();

        $output->writeln('<info>end of ETL</info>');
    }
}
