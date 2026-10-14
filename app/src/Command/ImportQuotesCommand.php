<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\DinggoApiClient;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use RuntimeException;

class ImportQuotesCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $cars = $this->fetchTable('Cars')->find()->all();
        $quotes = $this->fetchTable('Quotes');
        $api = new DinggoApiClient();
        $imported = 0;
        $failed = 0;

        if ($cars->isEmpty()) {
            $io->err('No saved cars found. Run import_cars first.');

            return static::CODE_ERROR;
        }

        foreach ($cars as $car) {
            try {
                $data = $api->fetchQuotes($car->license_plate, $car->license_state);
                $newQuotes = [];

                foreach ($data as $row) {
                    if (!is_array($row)) {
                        throw new RuntimeException('The API returned an invalid quote record.');
                    }

                    $row['car_id'] = $car->id;
                    $quote = $quotes->newEntity($row);
                    if ($quote->hasErrors()) {
                        throw new RuntimeException(
                            'Invalid quote fields: ' . implode(', ', array_keys($quote->getErrors())),
                        );
                    }
                    $newQuotes[] = $quote;
                }

                // Keep the old quotes if saving the replacement fails.
                $quotes->getConnection()->transactional(function () use ($quotes, $car, $newQuotes): void {
                    $quotes->deleteAll(['car_id' => $car->id]);
                    foreach ($newQuotes as $quote) {
                        $quotes->saveOrFail($quote);
                    }
                });

                $imported += count($newQuotes);
                $io->out($car->license_plate . ' (' . $car->license_state . '): ' . count($newQuotes) . ' quotes saved.');
            } catch (\Exception $error) {
                $failed++;
                $io->err(
                    $car->license_plate . ' (' . $car->license_state . '): import failed; previous quotes kept. '
                    . $error->getMessage(),
                );
            }
        }

        $io->out("Quotes imported: {$imported}. Cars with errors: {$failed}.");

        return $failed > 0 ? static::CODE_ERROR : static::CODE_SUCCESS;
    }
}
