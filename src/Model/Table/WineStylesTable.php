<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * WineStyles Model
 *
 * @method \App\Model\Entity\WineStyle newEmptyEntity()
 * @method \App\Model\Entity\WineStyle newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\WineStyle> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\WineStyle get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\WineStyle findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\WineStyle patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\WineStyle> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\WineStyle|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\WineStyle saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\WineStyle>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\WineStyle>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\WineStyle>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\WineStyle> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\WineStyle>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\WineStyle>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\WineStyle>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\WineStyle> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class WineStylesTable extends Table
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

        $this->setTable('wine_styles');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Products', [
            'foreignKey' => 'wine_style_id',
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
            ->scalar('name')
            ->maxLength('name', 100,'Please enter less than 100 characters')
            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->add('name', 'unique',
                ['rule' => 'validateUnique',
                'provider' => 'table',
                'message'=>'Wine style already exists. Please enter a different style.']);

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
        $rules->add($rules->isUnique(['name']), ['errorField' => 'name']);

        return $rules;
    }
}
