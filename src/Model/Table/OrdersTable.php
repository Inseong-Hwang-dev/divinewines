<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Orders Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\OrderProductsTable&\Cake\ORM\Association\HasMany $OrderProducts
 *
 * @method \App\Model\Entity\Order newEmptyEntity()
 * @method \App\Model\Entity\Order newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Order[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Order get($primaryKey, $options = [])
 * @method \App\Model\Entity\Order findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Order patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Order[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Order|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Order saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 */
class OrdersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('orders');
        $this->setDisplayField('order_id');
        $this->setPrimaryKey('order_id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('OrderProducts', [
            'foreignKey' => 'order_id',
        ]);

        $this->addBehavior('Timestamp');
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
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->decimal('total_amount')
            ->notEmptyString('total_amount');

        $validator
            ->scalar('shipping_name')
            ->maxLength('shipping_name', 255)
            ->notEmptyString('shipping_name', "Please enter the recipient's name");

        $validator
            ->scalar('shipping_address1')
            ->maxLength('shipping_address1', 255)
            ->notEmptyString('shipping_address1', "Please enter the recipient's address");

        $validator
            ->scalar('shipping_address2')
            ->maxLength('shipping_address2', 255)
            ->allowEmptyString('shipping_address2');

        $validator
            ->scalar('shipping_city')
            ->maxLength('shipping_city', 255)
            ->notEmptyString('shipping_city', "Please enter the city");

        $validator
            ->scalar('shipping_state')
            ->maxLength('shipping_state', 255)
            ->notEmptyString('shipping_state', "Please enter the state");

        $validator
            ->scalar('shipping_postcode')
            ->maxLength('shipping_postcode', 10, 'Postcode is too long')
            ->notEmptyString('shipping_postcode', "Please enter the postcode");

        $validator
            ->scalar('shipping_country')
            ->maxLength('shipping_country', 255)
            ->notEmptyString('shipping_country', "Please enter the country");

        $validator
            ->decimal('shipping_cost')
            ->notEmptyString('shipping_cost');
        $validator
            ->scalar('status')
            ->notEmptyString('status', "Please select the status");

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
        $rules->add($rules->existsIn('user_id', 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
