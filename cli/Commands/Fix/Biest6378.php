<?php

namespace Studip\Cli\Commands\Fix;

use DBManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Biest6378 extends Command
{
    protected static $defaultName = 'fix:biest-6378';

    protected function configure(): void
    {
        $this->setDescription('Fix Biest #6378 after migration has run (requires vips tables)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $db = DBManager::get();

        $data = $db->query("SHOW VARIABLES LIKE 'auto_increment_increment'");
        $incr = (int) $data->fetchColumn(1) ?: 1;

        $data = $db->query("SELECT id, mkdate FROM etask_assignments WHERE type IN ('exam', 'practice', 'selftest') LIMIT 1");
        $row = $data->fetch(\PDO::FETCH_ASSOC);
        $id = (int) $row['id'] ?: 1;
        $mkdate = (int) $row['mkdate'] + 1800;

        $assignment_id = [];
        $data = $db->query('SELECT id FROM vips_assignment WHERE test_id IN (SELECT id FROM vips_test)');

        while ($row = $data->fetch(\PDO::FETCH_ASSOC)) {
            $assignment_id[$row['id']] = $id;
            $id += $incr;
        }

        $stmt = $db->prepare('UPDATE cw_blocks SET payload = :payload, chdate = :chdate WHERE id = :id');
        $data = $db->prepare("SELECT id, payload FROM cw_blocks WHERE block_type = 'test' AND chdate < ?");
        $data->execute([$mkdate]);

        while ($row = $data->fetch(\PDO::FETCH_ASSOC)) {
            $payload = json_decode($row['payload'], true);

            if ($payload && isset($assignment_id[$payload['assignment']])) {
                $payload['assignment'] = (string) $assignment_id[$payload['assignment']];

                $values = [
                    'id'          => $row['id'],
                    'payload'     => json_encode($payload),
                    'chdate'      => time()
                ];
                $stmt->execute($values);
            }
        }

        return Command::SUCCESS;
    }
}
