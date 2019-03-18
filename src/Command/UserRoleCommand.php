<?php
namespace App\Command;

use App\Entity\User;
use App\Model\ETL\ETL;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserRoleCommand extends Command
{

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * UserRoleCommand constructor.
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->em = $entityManager;
    }


    protected function configure()
    {
        $this
            ->setName('app:user:add-role')
            ->setDescription('add role for user')
            ->addArgument('username', InputArgument::REQUIRED, 'username')
            ->addArgument('role', InputArgument::REQUIRED, 'role name');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $role = $input->getArgument('role');

        /**
         * @var $user User
         */
        $user = $this->userRepository->findOneBy([
            'username' => $username
        ]);

        if (null === $user) {
            $output->writeln('<error>user '.$username.' d\'ont exists</error>');
        } else {
            $user->addRole($input->getArgument('role'));
            $this->em->flush();
            $output->writeln('<info>user '.$username.' have been granted to '.$role.'</info>');
        }
    }
}
