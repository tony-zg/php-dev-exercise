<?php
declare(strict_types=1);

namespace App\Controller;

class CarsController extends AppController
{
    public function index(): void
    {
        $cars = $this->fetchTable('Cars')
            ->find()
            ->orderBy(['license_plate' => 'ASC', 'license_state' => 'ASC'])
            ->all();

        $this->set(compact('cars'));
    }
}
