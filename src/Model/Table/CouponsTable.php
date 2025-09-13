<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Coupons Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Coupon newEmptyEntity()
 * @method \App\Model\Entity\Coupon newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Coupon> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Coupon get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Coupon findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Coupon patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Coupon> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Coupon|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Coupon saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Coupon>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Coupon>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Coupon>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Coupon> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Coupon>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Coupon>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Coupon>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Coupon> deleteManyOrFail(iterable $entities, array $options = [])
 */
class CouponsTable extends Table
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

        $this->setTable('coupons');
        $this->setDisplayField('coupon_code');
        $this->setPrimaryKey('coupon_id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
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
            ->integer('coupon_id')
            ->allowEmptyString('coupon_id', null, 'create')
            ->add('coupon_id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('coupon_code')
            ->maxLength('coupon_code', 50)
            ->requirePresence('coupon_code', 'create')
            ->notEmptyString('coupon_code')
            ->add('coupon_code', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->numeric('discount_amount')
            ->requirePresence('discount_amount', 'create')
            ->notEmptyString('discount_amount')
            ->greaterThanOrEqual('discount_amount', 0)
            ->lessThanOrEqual('discount_amount', 100);

        $validator
            ->dateTime('expiry_date')
            ->allowEmptyDateTime('expiry_date');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        $validator
            ->numeric('min_purchase_amount')
            ->allowEmptyString('min_purchase_amount')
            ->greaterThanOrEqual('min_purchase_amount', 0);

        $validator
            ->numeric('max_discount_amount')
            ->allowEmptyString('max_discount_amount')
            ->greaterThanOrEqual('max_discount_amount', 0);

        $validator
            ->integer('usage_limit')
            ->allowEmptyString('usage_limit')
            ->greaterThanOrEqual('usage_limit', 0);

        $validator
            ->integer('times_used')
            ->notEmptyString('times_used')
            ->greaterThanOrEqual('times_used', 0);

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
        $rules->add($rules->isUnique(['coupon_code']), ['errorField' => 'coupon_code']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
