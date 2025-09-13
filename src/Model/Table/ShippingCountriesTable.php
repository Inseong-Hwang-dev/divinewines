<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ShippingCountries Model
 *
 * @method \App\Model\Entity\ShippingCountry newEmptyEntity()
 * @method \App\Model\Entity\ShippingCountry newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ShippingCountry> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ShippingCountry get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ShippingCountry findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ShippingCountry patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ShippingCountry> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ShippingCountry|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ShippingCountry saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ShippingCountry>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ShippingCountry>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ShippingCountry>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ShippingCountry> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ShippingCountry>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ShippingCountry>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ShippingCountry>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ShippingCountry> deleteManyOrFail(iterable $entities, array $options = [])
 */
class ShippingCountriesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('shipping_countries');
        $this->setDisplayField('country_code');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('country_code')
            ->maxLength('country_code', 5)
            ->requirePresence('country_code', 'create')
            ->notEmptyString('country_code')
            ->add('country_code', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('country_name')
            ->maxLength('country_name', 255)
            ->requirePresence('country_name', 'create')
            ->notEmptyString('country_name')
            ->add('country_name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['country_name']), ['errorField' => 'country_name']);
        $rules->add($rules->isUnique(['country_code']), ['errorField' => 'country_code']);

        return $rules;
    }
}
