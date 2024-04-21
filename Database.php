<?php

namespace FpDbTest;

use FpDbTest\exceptions\BadFormatException;
use FpDbTest\exceptions\BadParamTypeException;
use FpDbTest\exceptions\NestedConditionalBlocksException;
use FpDbTest\exceptions\OpenedConditionalBlocksException;
use FpDbTest\exceptions\WrongParamsCountException;
use FpDbTest\services\QueryBuilder;
use mysqli;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;
    private string $skipParam = '';

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * @param string $query
     * @param array $args
     * @return string
     * @throws BadFormatException
     * @throws BadParamTypeException
     * @throws NestedConditionalBlocksException
     * @throws WrongParamsCountException
     * @throws OpenedConditionalBlocksException
     */
    public function buildQuery(string $query, array $args = []): string
    {
        $args = $this->prepareParameters($args);
        $builder = new QueryBuilder($query, $args);
        if ($this->skipParam) {
            $builder->setSkippedParamValue($this->skipParam);
        }
        return $builder->getQuery();
    }

    public function skip(): string
    {
        if ($this->skipParam === '') {
            $this->skipParam = uniqid('skip');
        }
        return $this->skipParam;
    }

    private function prepareParameters(array $parameters): array
    {
        $result = [];
        foreach ($parameters as $key => $parameter) {
            if (is_array($parameter)) {
                $result[] = $this->prepareParameters($parameter);
            } else {
                if (is_string($parameter)) {
                    $parameter = mysqli_escape_string($this->mysqli, $parameter);
                }
                $result[$key] = $parameter;
            }
        }

        return $result;
    }
}
