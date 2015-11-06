<?php

namespace DataDo\Data;

use Closure;
use DataDo\Data\Exceptions\DslSyntaxException;
use DataDo\Data\Parser\DefaultMethodNameParser;
use DataDo\Data\Parser\MethodNameParser;
use DataDo\Data\Query\DefaultQueryBuilder;
use DataDo\Data\Query\QueryBuilder;
use DataDo\Data\Query\QueryBuilderResult;
use ErrorException;
use PDO;
use ReflectionClass;
use ReflectionProperty;
use stdClass;

/**
 * This class is responsible for the communication with the database for your simple queries.
 * @package DataDo\Data
 */
class Repository
{
    /** @var Closure[] */
    private $methods = array();
    /** @var  PDO */
    private $pdo;
    /** @var string */
    private $tableName;
    /** @var ReflectionClass */
    private $entityClass;
    /** @var QueryBuilder */
    private $queryBuilder;
    /** @var MethodNameParser */
    private $methodParser;
    /** @var NamingConvention */
    private $namingContention;
    /**
     * @var ReflectionProperty
     */
    private $idProperty;

    /**
     * Create a new Repository.
     * Optionally you can provide the name of the id property. This is the property that will be used when updating an entity.
     * @param $class string The full class name of the entity this repository should use
     * @param $pdo PDO the connection to the database
     * @param string $idProperty the name of the property that is used as the row id
     */
    public function __construct($class, PDO $pdo, $idProperty)
    {
        $this->pdo = $pdo;
        $this->entityClass = new ReflectionClass($class);
        $this->idProperty = $this->entityClass->getProperty($idProperty);
        $this->idProperty->setAccessible(true);
        $this->queryBuilder = new DefaultQueryBuilder();
        $this->methodParser = new DefaultMethodNameParser();
        $this->namingContention = new DefaultNamingConvention();
        $this->tableName = $this->namingContention->tableName($this->namingContention->classToTableName($this->entityClass));
    }

    /**
     * Insert an entity into the database.
     * You can optionally provide a property name. If you do the inserted id will be assigned to that property.
     * @param $entity
     * @param string $idProperty the name of the property that should hold the id
     * @return integer the id of the row that was inserted
     */
    public function insert($entity)
    {
        $values = $this->getInsertValues($entity, $this->namingContention);
        $keyString = implode(array_keys($values), ', ');
        $questionMarkString = count($values) === 0 ? '' : ('?' . str_repeat(', ?', count($values) - 1));

        $sql = "INSERT INTO $this->tableName ($keyString) VALUES ($questionMarkString)";
        $sth = $this->pdo->prepare($sql);
        $sth->execute(array_values($values));
        $id = $this->pdo->lastInsertId();

        if($this->idProperty !== null) {
            $this->idProperty->setValue($entity, $id);
        }

        return $id;
    }

    /**
     * Call a dsl method and create it if it does not exist.
     * @param $method
     * @param $args
     * @return mixed
     * @throws ErrorException
     * @throws DslSyntaxException if parsing the dsl method failed
     */
    public function __call($method, array $args)
    {
        if (!array_key_exists($method, $this->methods)) {
            $this->addMethod($method);
        }

        if (is_callable($this->methods[$method])) {
            return call_user_func_array($this->methods[$method], $args);
        }

        throw new ErrorException('Something went wrong. The created method is not callable');
    }

    /**
     * Parse a dsl method and add it to this repository.
     * @param $method string the method name
     * @throws DslSyntaxException
     */
    private function addMethod($method)
    {
        $tokens = $this->methodParser->parse($method);
        $query = $this->queryBuilder->build($tokens, $this->tableName, $this->namingContention, $this->entityClass);

        if ($query->getResultMode() <= QueryBuilderResult::RESULT_SELECT_MULTIPLE) {
            $this->addSelectionMethod($query, $method);
        } else {
            $this->addIntResultMethod($query, $method);
        }

    }

    /**
     * Add a method that can either result in an entity or an array of entities.
     * @param $query QueryBuilderResult a parsed query
     * @param $methodName string the name of the dsl method
     * @throws DslSyntaxException if an unsupported result mode was requested
     */
    private function addSelectionMethod(QueryBuilderResult $query, $methodName)
    {
        $findMethod = function () use ($query) {

            $sth = $this->pdo->prepare($query->getSql());
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            $sth->setFetchMode(\PDO::FETCH_CLASS, $this->entityClass->getName());
            $sth->execute(func_get_args());
            switch ($query->getResultMode()) {
                case QueryBuilderResult::RESULT_SELECT_SINGLE:
                    return $sth->fetch();
                case QueryBuilderResult::RESULT_SELECT_MULTIPLE:
                    return $sth->fetchAll();
            }

            throw new DslSyntaxException('Result Mode ' . $query->getResultMode() . ' is not implemented', DATADO_ILLEGAL_RESULT_MODE);

        };

        $this->methods[$methodName] = \Closure::bind($findMethod, $this, get_class());
    }

    /**
     * Add a new method that will result in an integer.
     * @param $query QueryBuilderResult a parsed dsl query
     * @param $methodName string the name of the method
     */
    private function addIntResultMethod(QueryBuilderResult $query, $methodName)
    {
        $intResultMethod = function () use ($query) {
            $sth = $this->pdo->prepare($query->getSql());
            $sth->execute(func_get_args());
            return $sth->rowCount();
        };

        $this->methods[$methodName] = \Closure::bind($intResultMethod, $this, get_class());
    }


    private function getInsertValues($entity, NamingConvention $namingContention)
    {
        $result = [];
        foreach($this->entityClass->getProperties() as $property) {
            $property->setAccessible(true);
            $columnName = $namingContention->columnName($namingContention->propertyToColumnName($property));

            $result[$columnName] = $property->getValue($entity);
        }
        return $result;
    }
}