<?php

namespace App\Command;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates an admin in the database using doctrine',
)]
class CreateUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure()
    {
        $this->setName('app:create-user')
            ->setDescription('Creates a new user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $output->writeln([
            'Admin Creator',
            'Note: save the credentials before entering them!',
        ]);

        $usernameQuestion = new Question('enter admin username: ');
        $username = $helper->ask($input, $output, $usernameQuestion);
        if (empty($username)) {
            $output->writeln('<error>User is required!</error>');
            return Command::FAILURE;
        }

        $emailQuestion = new Question('enter admin email: ');
        $email = $helper->ask($input, $output, $emailQuestion);
        if (empty($username)) {
            $output->writeln('<error>Email is required!</error>');
            return Command::FAILURE;
        }


        $passwordQuestion = new Question('enter admin password: ');
        $passwordQuestion->setHidden(true);
        $password = $helper->ask($input, $output, $passwordQuestion);
        if (empty($password)) {
            $output->writeln('<error>Password is required</error>');
            return Command::FAILURE;
        }

        $passwordConfirmQuestion = new Question('confirm password: ');
        $passwordConfirmQuestion->setHidden(true);
        $passwordConfirm = $helper->ask($input, $output, $passwordConfirmQuestion);
        while($password !== $passwordConfirm) {
            $output->writeln(messages: 'paswwords did not match!');

            $passwordQuestion = new Question('enter admin password:');
            $passwordQuestion->setHidden(true);
            $password = $helper->ask($input, $output, $passwordQuestion);
            if (empty($password)) {
                $output->writeln('<error>Password is required</error>');
                return Command::FAILURE;
            }

            $passwordConfirmQuestion = new Question('confirm password: ');
            $passwordConfirmQuestion->setHidden(true);
            $passwordConfirm = $helper->ask($input, $output, $passwordConfirmQuestion);
        }
        #setting properties
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setRoles(['admin']);
        #hashing password
        $output->writeln('Hashing password...');
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $output->writeln('Password hashed successfully.');

        #saving user
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $output->writeln('User created successfully');

        return Command::SUCCESS;
    }
}
