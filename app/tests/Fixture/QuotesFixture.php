<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class QuotesFixture extends TestFixture
{
    public array $records = [
        [
            'id' => 1, 'car_id' => 1, 'price' => '100.00',
            'repairer' => 'Original repairer', 'overview_of_work' => 'Original work',
            'created' => '2026-01-01 00:00:00', 'modified' => '2026-01-01 00:00:00',
        ],
    ];
}
