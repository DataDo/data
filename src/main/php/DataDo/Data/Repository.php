<?php

namespace DataDo\Data;
use PDO;
use Closure;
use ReflectionClass;
use DataDo\Data\Exceptions\DslSyntaxException;
use ErrorException;

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

    /**
     * Create a new Repository.
     * @param $class string The full class name of the entity this repository should use
     * @param $tableName string The name of the table that should be used
     * @param $pdo PDO the connection to the database
     */
    public function __construct($class, $tableName, PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
        $this->entityClass = new ReflectionClass($class);
        $this->queryBuilder = new DefaultQueryBuilder();
        $this->methodParser = new DefaultMethodNameParser();
    }

    /**
     * Call a dsl method and create it if it does not exist.
     * @param $method
     * @param $args
     * @return mixed
     * @throws ErrorException
     * @throws DslSyntaxException if parsing the dsl method failed
     */
    public function __call($method, $args)
    {
        if (!array_key_exists($method, $this->methods)) {
            $this->addMethod($method);
        }

        if (is_callable($this->methods[$method])) {
            return call_user_func_array($this->methods[$method], $args);
        }

        throw new ErrorException('Something went wrong. The created method is not callable');
    }

    public function insert(\stdClass $entity)
    {
        $class = new \ReflectionClass($entity);
        $elements = array();
        foreach ($class->getProperties() as $prop) {
            $prop->setAccessible(true);
            $elements[$prop->getName()] = $prop->getValue($entity);
        }
        if (count($elements) === 0) {
            throw new \InvalidArgumentException('Cannot insert elements with no properties');
        }
        $values = '?' . str_repeat(', ?', count($elements) - 1);

        $query = 'INSERT INTO ' . $this->tableName . ' (' . implode(array_keys($elements), ', ') . ') VALUES (' . $values . ')';
        $sth = $this->pdo->prepare($query);
        return $sth->execute(array_values($elements));
    }

    /**
     * Parse a dsl method and add it to this repository.
     * @param $method string the method name
     * @throws DslSyntaxException
     */
    private function addMethod($method)
    {
        $tokens = $this->methodParser->parse($method);
        $query = $this->queryBuilder->build($tokens, $this->tableName);

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
}