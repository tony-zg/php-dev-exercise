<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateCars extends BaseMigration
{
    public function change(): void
    {
        $this->table('cars', ['signed' => true])
            ->addColumn('license_plate', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('license_state', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('vin', 'string', ['limit' => 17, 'null' => false])
            ->addColumn('year', 'integer', ['null' => false])
            ->addColumn('colour', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('make', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('model', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => false])
            ->addColumn('modified', 'datetime', ['null' => false])
            // The API identifies a car by both its plate and state.
            ->addIndex(['license_plate', 'license_state'], ['unique' => true])
            ->create();
    }
}
