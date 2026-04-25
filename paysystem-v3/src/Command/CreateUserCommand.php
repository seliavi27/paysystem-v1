<?php
declare(strict_types=1);

namespace PaySystem\Command;

use PaySystem\DTO\CreateUserRequest;
use PaySystem\Service\UserServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'user:create', description: 'Создать пользователя в интерактивном режиме')]
final class CreateUserCommand extends Command
{
    protected static $defaultName = 'user:create';

    public function __construct(private UserServiceInterface $userService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email',    null, InputOption::VALUE_REQUIRED, 'Email пользователя')
            ->addOption('full_name', null, InputOption::VALUE_REQUIRED, 'Полное имя')
            ->addOption('phone',    null, InputOption::VALUE_OPTIONAL, 'Телефон', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email    = $input->getOption('email')    ?? $io->ask('Email',  validator: $this->emailValidator());
        $fullName = $input->getOption('full_name') ?? $io->ask('Full name', validator: fn($v) => $this->requireNonEmpty($v, 'Full name'));
        $phone    = $input->getOption('phone');
        $password = $io->askHidden('Password',  validator: fn($v) => $this->requireMinLength($v, 6));
        $confirm  = $io->askHidden('Confirm password');

        if ($password !== $confirm) {
            $io->error('Пароли не совпадают');
            return Command::FAILURE;
        }

        if (!$io->confirm("Создать пользователя {$email} ({$fullName})?", default: true)) {
            $io->warning('Отменено.');
            return Command::SUCCESS;
        }

        try {
            $user = $this->userService->create(new CreateUserRequest(
                email: $email,
                password: $password,
                passwordConfirm: $confirm,
                fullName: $fullName,
                phone: $phone ?? '',
            ));
        } catch (\Throwable $e) {
            $io->error("Не удалось создать: {$e->getMessage()}");
            return Command::FAILURE;
        }

        $io->success("Пользователь создан: id={$user->id}, email={$user->email}");
        return Command::SUCCESS;
    }

    private function emailValidator(): \Closure
    {
        return fn(?string $v) => filter_var($v, FILTER_VALIDATE_EMAIL)
            ? $v
            : throw new \RuntimeException('Некорректный email');
    }

    private function requireNonEmpty(?string $v, string $field): string
    {
        return trim((string)$v) === '' ? throw new \RuntimeException("{$field} обязательно") : $v;
    }

    private function requireMinLength(?string $v, int $min): string
    {
        return strlen((string)$v) < $min ? throw new \RuntimeException("Минимум {$min} символов") : $v;
    }
}