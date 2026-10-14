<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateQuotes extends BaseMigration
{
    public function change(): void
    {
        $this->table('quotes', ['signed' => true])
            ->addColumn('car_id', 'integer', ['signed' => true, 'null' => false])
            ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => false])
            ->addColumn('repairer', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('overview_of_work', 'text', ['null' => false])
            ->addColumn('created', 'datetime', ['null' => false])
            ->addColumn('modified', 'datetime', ['null' => false])
            ->addIndex(['car_id'])
            ->addForeignKey('car_id', 'cars', 'id', [
                'delete' => 'CASCADE',
                'update' => 'RESTRICT',
            ])
            ->create();
    }
}
