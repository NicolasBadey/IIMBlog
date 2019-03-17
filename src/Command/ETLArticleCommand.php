<?php
namespace App\Command;

use App\Model\ETL\ETLArticle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription('ETL for populate Elasticsearch from SQL')
            ->addOption(
                'alias',
                'a',
                InputOption::VALUE_OPTIONAL,
                'alias or not',
                1
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $alias = $input->getOption('alias') === null ? true : (bool) $input->getOption('alias');

        $this->etlArticle->indexAll($alias,$output);
    }
}
