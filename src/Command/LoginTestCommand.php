<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:test-login')]
class LoginTestCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = 'mehdimejri15@gmail.com';
        $password = 'aaaaaaaa';

        $output->writeln("Attempting to find user: $email");
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $output->writeln("ERROR: User not found!");
            return Command::FAILURE;
        }

        $output->writeln("User found. ID: " . $user->getId());
        $output->writeln("Stored Hash: " . $user->getPassword());

        $isValid = $this->passwordHasher->isPasswordValid($user, $password);

        if ($isValid) {
            $output->writeln("SUCCESS: Password is valid.");
            return Command::SUCCESS;
        } else {
            $output->writeln("ERROR: Password is INVALID.");
            return Command::FAILURE;
        }
    }
}
