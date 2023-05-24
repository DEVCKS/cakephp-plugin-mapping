<?php

namespace Mapping\Model\Table;

use Cake\ORM\Table;

class NafsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config):void
    {
        parent::initialize($config);

        $this->setTable('nafs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }

    /**
     * @param array $ids = []
     * @return int[]
     */
    public function getByIds(array $ids = []): array
    {
        $query = $this->find();

        if (count($ids) > 0) {
            $query->where(['id IN' => $ids]);
        }
            
        return $query->toArray();
    }
}
