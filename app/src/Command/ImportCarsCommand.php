<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\DinggoApiClient;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use RuntimeException;

class ImportCarsCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $cars = $this->fetchTable('Cars');
        $added = 0;
        $updated = 0;

        try {
            $data = (new DinggoApiClient())->fetchCars();

            // Roll back the import if any car cannot be saved.
            $cars->getConnection()->transactional(function () use ($cars, $data, &$added, &$updated): void {
                foreach ($data as $row) {
                    if (!is_array($row)) {
                        throw new RuntimeException('The API returned an invalid car record.');
                    }

                    $car = $cars->newEntity($row);
                    if ($car->hasErrors()) {
                        throw new RuntimeException(
                            'Invalid car fields: ' . implode(', ', array_keys($car->getErrors())),
                        );
                    }

                    $existing = $cars->find()->where([
                        'license_plate' => $car->license_plate,
                        'license_state' => $car->license_state,
                    ])->first();

                    if ($existing) {
                        $car = $cars->patchEntity($existing, $row);
                    }

                    $cars->saveOrFail($car);

                    if ($existing) {
                        $updated++;
                    } else {
                        $added++;
                    }
                }
            });
        } catch (\Exception $error) {
            $io->err('Car import failed. No changes saved. ' . $error->getMessage());

            return static::CODE_ERROR;
        }

        $io->success("Cars imported: {$added} added, {$updated} updated.");

        return static::CODE_SUCCESS;
    }
}
