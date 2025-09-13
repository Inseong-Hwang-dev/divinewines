<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Newsletters Model
 *
 * @method \App\Model\Entity\Newsletter newEmptyEntity()
 * @method \App\Model\Entity\Newsletter newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Newsletter> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Newsletter get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Newsletter findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Newsletter patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Newsletter> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Newsletter|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Newsletter saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Newsletter>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Newsletter>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Newsletter>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Newsletter> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Newsletter>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Newsletter>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Newsletter>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Newsletter> deleteManyOrFail(iterable $entities, array $options = [])
 */
class NewslettersTable extends Table
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

        $this->setTable('newsletters');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

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
            ->scalar('title')
            ->maxLength('title', 50)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('subheading')
            ->maxLength('subheading', 100)
            ->requirePresence('subheading', 'create')
            ->notEmptyString('subheading');

        $validator
            ->scalar('body')
            ->requirePresence('body', 'create')
            ->notEmptyString('body');

        $validator
            ->scalar('image')
            ->maxLength('image', 255)
            ->allowEmptyFile('image');

        $validator
            ->dateTime('modified_at')
            ->allowEmptyDateTime('modified_at');

        $validator
            ->dateTime('date_published')
            ->allowEmptyDateTime('date_published');

        $validator
            ->scalar('status')
            ->allowEmptyString('status');

        $validator
            ->dateTime('created_at')
            ->allowEmptyDateTime('created_at');

        return $validator;
    }
}
