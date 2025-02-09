<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\DBAL\Exception;

#[AsCommand(
    name: 'app:database:clear',
    description: 'Clears all data from the database. Use with caution!',
    aliases: ['app:clear-db', 'app:db-clear'],
    hidden: false
)]
class DatabaseClearCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private KernelInterface $kernel;

    public function __construct(EntityManagerInterface $entityManager, KernelInterface $kernel)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->kernel = $kernel;
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to clear all data from the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->kernel->getEnvironment() === 'prod') {
            $output->writeln('<error>This command cannot be run in the production environment.</error>');
            return Command::FAILURE;
        }

        $output->writeln('<comment>WARNING: This will delete all data from the database!</comment>');

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you sure you want to proceed? (yes/no): ', false);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<info>Operation cancelled.</info>');
            return Command::SUCCESS;
        }

        $classes = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();

        try {
            $connection->executeStatement('SET session_replication_role = replica;');

            foreach ($classes as $classMetadata) {
                $tableName = $classMetadata->getTableName();
                $connection->executeStatement($platform->getTruncateTableSQL($tableName, true));
            }

            $connection->executeStatement('SET session_replication_role = DEFAULT;');

            $output->writeln('<info>Database cleared successfully.</info>');
            return Command::SUCCESS;

        } catch (Exception $e) {
            $connection->executeStatement('SET session_replication_role = DEFAULT;');
            $output->writeln('<error>An error occurred while clearing the database: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
