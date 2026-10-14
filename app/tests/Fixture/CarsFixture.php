<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class CarsFixture extends TestFixture
{
    public array $records = [
        [
            'id' => 1, 'license_plate' => 'TEST01', 'license_state' => 'NSW',
            'vin' => '5TDBW5G16BS451467', 'year' => 2002, 'colour' => 'Yellow',
            'make' => 'Mitsubishi', 'model' => 'Eclipse',
            'created' => '2026-01-01 00:00:00', 'modified' => '2026-01-01 00:00:00',
        ],
    ];
}
