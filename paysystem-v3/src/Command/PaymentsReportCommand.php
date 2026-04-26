<?php
declare(strict_types=1);

namespace App\Command;

use DateTime;
use App\Enum\PaymentStatus;
use App\Repository\PaymentRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'payments:report', description: 'Сводка по платежам за период')]
final class PaymentsReportCommand extends Command
{
    public function __construct(
        private PaymentRepositoryInterface $repository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('since', null, InputOption::VALUE_REQUIRED, 'Начало периода (YYYY-MM-DD)', date('Y-m-d', strtotime('-7 days')))
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Сколько последних показать в таблице', '10');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io       = new SymfonyStyle($input, $output);
        $since    = new DateTime($input->getOption('since'));
        $limit    = (int)$input->getOption('limit');
        $payments = $this->repository->findSince($since); // добавь этот метод в репозиторий, DQL: WHERE p.createdAt >= :since

        if (empty($payments)) {
            $io->warning("Нет платежей с {$since->format('Y-m-d')}.");
            return Command::SUCCESS;
        }

        // Сводка по статусам
        $totals = array_fill_keys(array_map(fn($s) => $s->value, PaymentStatus::cases()), ['count' => 0, 'sum' => 0.0]);
        $progress = new ProgressBar($output, count($payments));
        $progress->start();
        foreach ($payments as $p) {
            $s = $p->getStatus()->value;
            $totals[$s]['count']++;
            $totals[$s]['sum'] += $p->getAmount();
            $progress->advance();
        }
        $progress->finish();
        $io->newLine(2);

        // Таблица свёрнутая
        $io->section("Сводка с {$since->format('Y-m-d')}");
        $io->table(
            ['Статус', 'Кол-во', 'Сумма'],
            array_map(
                fn($s, $row) => [$s, $row['count'], number_format($row['sum'], 2)],
                array_keys($totals), $totals
            ),
        );

        // Последние N
        $recent = array_slice($payments, 0, $limit);
        $io->section("Последние {$limit} платежей");
        $io->table(
            ['ID', 'User', 'Amount', 'Currency', 'Status', 'Created'],
            array_map(fn($p) => [
                substr($p->getId(), 0, 8),
                $p->getUser()->getEmail(),
                number_format($p->getAmount(), 2),
                $p->getCurrency()->value,
                $p->getStatus()->value,
                $p->getCreatedAt()->format('Y-m-d H:i'),
            ], $recent),
        );

        return Command::SUCCESS;
    }
}