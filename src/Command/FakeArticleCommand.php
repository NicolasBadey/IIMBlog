<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FakeArticleCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * UserRoleCommand constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->em = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:article:fake')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->em->getConnection();
        $sql = <<<SQL
        INSERT INTO article (title, content, latitude, longitude)
 VALUES
 ('Rébecca', 'Armand', 42, 42),
 ('Aimée', 'Hebert', 42, 42),
 ('Marielle', 'Ribeiro', 42, 42),
 ('Hilaire', 'Savary', 42, 42);
 ('Hilaire', 'Savary', 42, 42);
 ('Hilaire', 'Savary', 42, 42);
 ('Hilaire', 'Savary', 42, 42);
 ('Hilaire', 'Savary', 42, 42);
 ('Hilaire', 'Savary', 42, 42);
 ('Hilaire', 'Savary', 42, 42);
SQL;

        for ($i = 0; $i < 100000; ++$i) {
            $connection->exec($sql);
        }
    }
}
