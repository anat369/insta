<?php

namespace App\Models;

use PDO;
USE Aura\SqlQuery\QueryFactory;

class Database
{
    protected $pdo;
    protected $queryFactory;

    /**
     * Database constructor.
     * @param PDO $PDO
     * @param QueryFactory $queryFactory
     */
    public function __construct(PDO $PDO, QueryFactory $queryFactory)
    {
        $this->pdo = $PDO;
        $this->queryFactory = $queryFactory;
    }


    /**
     * Получаем записи из конкретной таблицы, количество регулируем
     *
     * @param $table
     * @param null $limit
     * @return array
     */
    public function all($table, $limit = null)
    {
        $select = $this->queryFactory->newSelect();

        $select->cols(['*'])->from($table)->limit($limit);

        $dth = $this->pdo->prepare($select->getStatement());

        $dth->execute($select->getBindValues());

        return $dth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Находим запись в таблице
     *
     * @param $table
     * @param $id
     * @return mixed
     */
    public function find($table, $id)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from($table)->where('id = :id')->bindValue('id', $id);

        $sth = $this->pdo->prepare($select->getStatement());

        $sth->execute($select->getBindValues());

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Создаем запись
     *
     * @param $table
     * @param $data
     * @return string
     */
    public function create($table, $data)
    {
        $insert = $this->queryFactory->newInsert();
        $insert->into($table)->cols($data);


        $sth = $this->pdo->prepare($insert->getStatement());
        $sth->execute($insert->getBindValues());

        $name = $insert->getLastInsertIdName('id');
        return $this->pdo->lastInsertId($name);
    }

    /**
     * Обновляем запись в таблице
     *
     * @param $table
     * @param $id
     * @param $data
     */
    public function update($table, $id, $data)
    {
        $update = $this->queryFactory->newUpdate();

        $update->table($table)->cols($data)->where('id = :id')->bindValue('id', $id);

        $sth = $this->pdo->prepare($update->getStatement());

        $sth->execute($update->getBindValues());
    }

    /**
     * Удаляем запись
     *
     * @param $table
     * @param $id
     */
    public function delete($table, $id)
    {
        $delete = $this->queryFactory->newDelete();

        $delete->from($table)->where('id = :id')->bindValue('id', $id);

        $sth = $this->pdo->prepare($delete->getStatement());

        $sth->execute($delete->getBindValues());
    }

    /**
     * Получаем количество записей по условию
     *
     * @param $table
     * @param $row
     * @param $value
     * @return int
     */
    public function getCount($table, $row, $value)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from($table)->where("$row = :$row")->bindValue($row, $value);

        $sth = $this->pdo->prepare($select->getStatement());

        $sth->execute($select->getBindValues());

        return count($sth->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Получаем записи для использования пагинации
     *
     * @param $table
     * @param $row
     * @param $id
     * @param int $page
     * @param int $rows
     * @return array
     */
    public function getPaginatedFrom($table, $row, $id, $page = 1, $rows = 1)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from($table)->where("$row = :row")->bindValue(':row', $id)->page($page)->setPaging($rows);
        $sth = $this->pdo->prepare($select->getStatement());

        $sth->execute($select->getBindValues());

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Получаем необходимое количество записей, по умолчанию 4
     *
     * @param $table
     * @param $row
     * @param $id
     * @param int $limit
     * @return array
     */
    public function whereAll($table, $row, $id, $limit = 4)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from($table)->limit($limit)->where("$row = :id")->bindValue(":id", $id);

        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

}