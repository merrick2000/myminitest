<?php

namespace App\Command;

use App\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    private $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create a new admin user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the admin user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $randomUsername = 'admin_'.uniqid();
        $randomEmail = 'email_'.uniqid().'@admin.test';
        $user = new User();
        $user->setUsername($randomUsername);
        $user->setEmail($randomEmail);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $input->getArgument('password')));
        $user->setCredits(0.0);
        $user->addRole('ROLE_ADMIN');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('Admin created successfully. Email: '.$randomEmail . ' Username: '. $randomUsername);

        return 1;
    }
}
