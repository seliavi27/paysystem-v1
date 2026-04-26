<?php
namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Payment;
use App\Entity\User;
use App\Enum\CurrencyType;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'payments:import', description: 'Импорт платежей из JSON файла')]
final class PaymentsImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Только проверить, без записи');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io   = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');
        $dry  = (bool)$input->getOption('dry-run');

        if (!is_file($file))
        {
            $io->error("File not found: {$file}");
            return Command::FAILURE;
        }

        $rows = json_decode((string)file_get_contents($file), true, flags: JSON_THROW_ON_ERROR);

        if (!is_array($rows))
        {
            $io->error('JSON должен быть массивом платежей');
            return Command::FAILURE;
        }

        $progress = new ProgressBar($output, count($rows));
        $progress->start();
        $imported = 0;
        $skipped  = 0;

        foreach ($rows as $row)
        {
            if (!isset($row['id'], $row['userId']))
            {
                $skipped++;
                $progress->advance();
                continue;
            }

            $user = $this->em->find(User::class, $row['userId']);

            if ($user === null)
            {
                $skipped++;
                $progress->advance();
                continue;
            }

            if ($this->em->find(Payment::class, $row['id']) !== null)
            {
                $skipped++;
                $progress->advance();
                continue;
            }

            $payment = new Payment(
                user: $user,
                amount: (float)$row['amount'],
                description: $row['description'] ?? '',
                currency: CurrencyType::from($row['currency']),
                method: PaymentMethod::from($row['method']),
            );

            match (PaymentStatus::from($row['status']))
            {
                PaymentStatus::COMPLETED  => $payment->markCompleted(),
                PaymentStatus::FAILED     => $payment->markFailed(),
                PaymentStatus::REFUNDED   => $payment->markRefunded(),
                PaymentStatus::PROCESSING => $payment->markProcessing(),
                PaymentStatus::PENDING    => null,
            };

            if (!$dry)
            {
                $this->em->persist($payment);
            }

            $imported++;
            $progress->advance();

            if ($imported % 50 === 0 && !$dry)
            {
                $this->em->flush();
                $this->em->clear();
            }
        }

        if (!$dry)
        {
            $this->em->flush();
        }

        $progress->finish();
        $io->newLine(2);

        $io->success(sprintf(
            '%s: imported=%d, skipped=%d',
            $dry ? 'Dry run' : 'Import done',
            $imported,
            $skipped,
        ));

        return Command::SUCCESS;
    }
}