<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data;


class Repository
{
    private $methods = array();
    private $pdo;
    private $tableName;
    private $entityClass;
    private $queryBuilder;
    private $methodParser;

    public function __construct($class, $tableName, $pdo)
    {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
        $this->entityClass = new \ReflectionClass($class);
        $this->queryBuilder = new DefaultQueryBuilder();
        $this->methodParser = new DefaultMethodNameParser();
    }

    public function __call($method, $args)
    {
        if (!array_key_exists($method, $this->methods)) {
            $this->addMethod($method);
        }

        if (is_callable($this->methods[$method])) {
            return call_user_func_array($this->methods[$method], $args);
        }

        throw new \ErrorException('Something went wrong. The created method is not callable');
    }

    private function addMethod($method)
    {
        $tokens = $this->methodParser->parse($method);
        print_r($tokens);
        $query = $this->queryBuilder->build($tokens, $this->tableName);

        $findMethod = function () use ($query) {

            $sth = $this->pdo->prepare($query->sql);
            $sth->setFetchMode(\PDO::FETCH_CLASS, $this->entityClass->getName());
            $sth->execute(func_get_args());
            if ($query->resultMode === QueryBuilderResult::RESULT_SINGLE) {
                return $sth->fetch();
            } else if ($query->resultMode === QueryBuilderResult::RESULT_MULTIPLE) {
                return $sth->fetchAll();
            }

            throw new \InvalidArgumentException('Result Mode ' . $query->resultMode . ' is not implemented');

        };

        $this->methods[$method] = \Closure::bind($findMethod, $this, get_class());
    }
}