<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Adds new user',
    hidden: false
)]
class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';
    protected static $defaultDescription = 'Creates a new user';

    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    )
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to create a user.')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('email', InputArgument::REQUIRED, 'Email address')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addOption('first-name', null, InputOption::VALUE_OPTIONAL, 'First name')
            ->addOption('last-name', null, InputOption::VALUE_OPTIONAL, 'Last name');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = [];

        if (!$input->getArgument('username')) {
            $questions['username'] = new Question('Please enter a username: ');
        }

        if (!$input->getArgument('email')) {
            $questions['email'] = new Question('Please enter an email: ');
        }

        if (!$input->getArgument('password')) {
            $questions['password'] = (new Question('Please enter a password: '))
                ->setHidden(true)
                ->setHiddenFallback(false);
        }

        foreach ($questions as $key => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($key, $answer);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Retrieve input arguments and options
        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $firstName = $input->getOption('first-name') ?? 'FirstName';
        $lastName = $input->getOption('last-name') ?? 'LastName';

        // Validate if user already exists
        if ($this->userRepository->findOneBy(['username' => $username])) {
            $output->writeln('<error>Username already exists.</error>');
            return Command::FAILURE;
        }
        if ($this->userRepository->findOneBy(['email' => $email])) {
            $output->writeln('<error>Email already exists.</error>');
            return Command::FAILURE;
        }

        // Create the user entity
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);


        // Hash and set the password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // Persist the user to the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();


$output->writeln('<info>User successfully created!</info>');
        $output->writeln('Username: ' . $user->getUsername());
        $output->writeln('Email: ' . $user->getEmail());

        return Command::SUCCESS;
    }
}

