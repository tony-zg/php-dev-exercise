<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Quote extends Entity
{
    protected array $_accessible = [
        'car_id' => true,
        'price' => true,
        'repairer' => true,
        'overview_of_work' => true,
    ];
}
