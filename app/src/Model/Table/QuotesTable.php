<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class QuotesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('quotes');
        $this->setDisplayField('repairer');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Cars', [
            'foreignKey' => 'car_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('car_id')
            ->greaterThanOrEqual('car_id', 1)
            ->requirePresence('car_id', 'create')
            ->notEmptyString('car_id');

        $validator
            ->decimal('price')
            ->greaterThanOrEqual('price', 0)
            ->lessThanOrEqual('price', 99999999.99)
            ->requirePresence('price', 'create')
            ->notEmptyString('price');

        $validator
            ->scalar('repairer')
            ->maxLength('repairer', 255)
            ->requirePresence('repairer', 'create')
            ->notEmptyString('repairer');

        $validator
            ->scalar('overview_of_work')
            ->maxLengthBytes('overview_of_work', 65535)
            ->requirePresence('overview_of_work', 'create')
            ->notEmptyString('overview_of_work');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['car_id'], 'Cars'), [
            'errorField' => 'car_id',
            'message' => 'Choose a car that has already been saved.',
        ]);

        return $rules;
    }
}
