<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CarsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('cars');
        $this->setDisplayField('license_plate');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('license_plate')
            ->maxLength('license_plate', 20)
            ->requirePresence('license_plate', 'create')
            ->notEmptyString('license_plate');

        $validator
            ->scalar('license_state')
            ->maxLength('license_state', 10)
            ->requirePresence('license_state', 'create')
            ->notEmptyString('license_state');

        $validator
            ->scalar('vin')
            ->maxLength('vin', 17)
            ->requirePresence('vin', 'create')
            ->notEmptyString('vin');

        $validator
            ->integer('year')
            ->range('year', [1000, 9999])
            ->requirePresence('year', 'create')
            ->notEmptyString('year');

        $validator
            ->scalar('colour')
            ->maxLength('colour', 100)
            ->requirePresence('colour', 'create')
            ->notEmptyString('colour');

        $validator
            ->scalar('make')
            ->maxLength('make', 100)
            ->requirePresence('make', 'create')
            ->notEmptyString('make');

        $validator
            ->scalar('model')
            ->maxLength('model', 100)
            ->requirePresence('model', 'create')
            ->notEmptyString('model');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['license_plate', 'license_state']), [
            'errorField' => 'license_plate',
            'message' => 'This car is already saved for this state.',
        ]);

        return $rules;
    }
}
