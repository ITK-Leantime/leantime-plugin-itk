<?php

namespace Leantime\Plugins\Itk\Command;

use Leantime\Domain\Auth\Repositories\Auth as AuthRepository;
use Leantime\Domain\Users\Repositories\Users;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        private readonly Users $users,
        private readonly AuthRepository $authRepo
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
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'The user (id or email)')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'The password.')
            ->addOption('reset-url', null, InputOption::VALUE_NONE, 'If set, a password reset URL will reported');
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

        if ($input->getOption('reset-url')) {
            $question = sprintf('Generate password reset URL for user %s?', $username);
            if ($io->confirm($question, !$input->isInteractive())) {
                // Cf. \Leantime\Domain\Auth\Services\Auth::generateLinkAndSendEmail().
                $permittedChars = '0123456789abcdefghijklmnopqrstuvwxyz';
                $resetToken = substr(str_shuffle($permittedChars), 0, 32);

                if ($this->authRepo->setPWResetLink($username, $resetToken)) {
                    $baseUrl = BASE_URL;
                    if (!parse_url($baseUrl)) {
                        $io->warning('Base URL not defined (`LEAN_APP_URL` not set in config)');
                        $baseUrl = '';
                    }
                    $url = $baseUrl . '/auth/resetPw/' . $resetToken;
                    $io->success(sprintf('Password reset URL for user %s: %s', $username, $url));

                    return Command::SUCCESS;
                } else {
                    $io->error(sprintf('Error setting password reset URL on user %s', $username));

                    return Command::FAILURE;
                }
            }
        } else {
            $question = sprintf('Reset password for user %s?', $username);
            if ($io->confirm($question, !$input->isInteractive())) {
                $password = $this->generatePassword($input);
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
        }

        return static::SUCCESS;
    }

    /**
     * Generate password
     *
     * @return string
     */
    private function generatePassword(InputInterface $input): string
    {
        $password = $input->getOption('password');
        if (null !== $password) {
            return $password;
        }

        // Cf. \Leantime\Domain\Users\Services\Users::createUserInvite().
        return Uuid::uuid4()->toString();
    }
}
