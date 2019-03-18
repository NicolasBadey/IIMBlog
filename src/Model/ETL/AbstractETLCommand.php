<?php
namespace App\Model\ETL;

use App\Model\ETL\Article\ArticleETLBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractETLCommand extends Command
{

    /**
     * @var ArticleETLBuilder
     */
    protected $ETLBuilder;

    protected function configure()
    {
        $this
            ->setDescription('ETL for populate Elasticsearch from SQL')
            ->addOption(
                'live',
                'a',
                InputOption::VALUE_OPTIONAL,
                'live or not',
                0
            )->addArgument(
                'ids',
                InputArgument::OPTIONAL,
                'specifics Ids to populate'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $live = $input->getOption('live') === null ? true : (bool) $input->getOption('live');
        $ids = $input->getArgument('ids') === null ? [] : explode(',', $input->getArgument('ids'));

        $this->ETLBuilder->build()->run($output, $live, $ids);
    }
}
