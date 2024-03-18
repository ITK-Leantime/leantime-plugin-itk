<?php

namespace Leantime\Plugins\Itk\Command;

use Leantime\Domain\Users\Repositories\Users;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * User password reset command.
 */
#[AsCommand(
    name: 'itk:user:password-reset',
    description: 'Reset user password',
)]
final class UserPasswordResetCommand extends Command
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly Users $users
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'The user (id or email)');
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $id = $input->getArgument('id');
        $user = ($this->users->getUserByEmail($id) ?: null)
            ?? ($this->users->getUser($id) ?: null)
            ?? null;
        if (null === $user || !isset($user['username'])) {
            throw new InvalidArgumentException(sprintf('Cannot find user with id %s', $id));
        }
        $username = $user['username'];

        $question = sprintf('Reset password for user %s?', $username);
        if ($io->confirm($question, !$input->isInteractive())) {
            $password = $this->generatePassword();
            $values = ['password' => $password]
                + $user
                // Users::editUser expects the user key to be set (!)
                + ['user' => $user['username']];
            if ($this->users->editUser($values, $user['id'])) {
                $io->success(sprintf('Password for user %s set to %s', $username, $password));
                return static::SUCCESS;
            } else {
                $io->error(sprintf('Error setting password for user %s', $username));
                return static::FAILURE;
            }
        }

        return static::SUCCESS;
    }

    /**
     * Generate password
     *
     * @return string
     */
    private function generatePassword(): string
    {
        // Cf. \Leantime\Domain\Users\Services\Users::createUserInvite().
        return Uuid::uuid4()->toString();
    }
}
