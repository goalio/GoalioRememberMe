<?php

namespace GoalioRememberMe\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;
use Zend\Db\Sql\Where;

class RememberMe extends AbstractDbMapper
{
    protected $tableName  = 'user_remember_me';

    public function findById($userId)
    {
        $select = $this->getSelect()
            ->where(array('user_id' => $userId));

        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function findByIdSerie($userId, $serieId)
    {
        $select = $this->getSelect()
            ->where(array('user_id' => $userId, 'sid' => $serieId));

        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function updateSerie($entity)
    {
        $where = new Where();
        $where->equalTo('user_id', $entity->getUserId());
        $where->equalTo('sid', $entity->getSid());
        $hydrator = new RememberMeHydrator;
        return parent::update($entity, $where, $this->tableName, $hydrator);
    }

    public function createSerie($entity)
    {
        $hydrator = new RememberMeHydrator;
        return parent::insert($entity, $this->tableName, $hydrator);
    }

    public function removeAll($userId)
    {
        $where = new Where();
        $where->equalTo('user_id', $userId);
        return parent::delete($where, $this->tableName);
    }

    public function remove($entity)
    {
        $where = new Where();
        $where->equalTo('user_id', $entity->getUserId());
        $where->equalTo('sid', $entity->getSid());
        $where->equalTo('token', $entity->getToken());
        return parent::delete($where, $this->tableName);
    }

    public function removeSerie($userId, $serieId)
    {
        $where = new Where();
        $where->equalTo('user_id', $userId);
        $where->equalTo('sid', $serieId);
        return parent::delete($where, $this->tableName);
    }
}
