<?php

declare(strict_types=1);

namespace FpDbTest\services;

use FpDbTest\dictionaries\FormatDictionary;
use FpDbTest\exceptions\BadFormatException;
use FpDbTest\exceptions\BadParamTypeException;
use FpDbTest\exceptions\NestedConditionalBlocksException;
use FpDbTest\exceptions\OpenedConditionalBlocksException;
use FpDbTest\exceptions\WrongParamsCountException;

class QueryBuilder
{
    private string $queryTemplate;
    private array $parameters;
    private ?string $skippedParamValue = null;

    /**
     * @param string $queryTemplate
     * @param array<string|int|float|bool|null|array<int|string, string|int|float|bool|null>> $parameters
     * @throws NestedConditionalBlocksException
     * @throws WrongParamsCountException
     * @throws OpenedConditionalBlocksException
     */
    public function __construct(string $queryTemplate, array $parameters = [])
    {
        $this->validate($queryTemplate, $parameters);
        $this->queryTemplate = $queryTemplate;
        $this->parameters = $parameters;
    }

    /**
     * @throws BadFormatException
     * @throws BadParamTypeException
     */
    public function getQuery(): string
    {
        return $this->cleanQueryFromSkippedConditionalBlocks($this->build());
    }

    /**
     * @param string $queryTemplate
     * @param array $parameters
     * @return void
     * @throws NestedConditionalBlocksException
     * @throws WrongParamsCountException
     * @throws OpenedConditionalBlocksException
     */
    protected function validate(string $queryTemplate, array $parameters = []): void
    {
        preg_match_all('/^[^{]*\}/', $queryTemplate, $matchesOpenedRight, PREG_SET_ORDER);
        if (count($matchesOpenedRight) > 0) {
            throw new OpenedConditionalBlocksException();
        }
        preg_match_all('/\{[^}]*$/', $queryTemplate, $matchesOpenedLeft, PREG_SET_ORDER);
        if (count($matchesOpenedLeft) > 0) {
            throw new OpenedConditionalBlocksException();
        }

        preg_match_all('/\{[^}]*\{/', $queryTemplate, $matchesLeft, PREG_SET_ORDER);
        if (count($matchesLeft) > 0) {
            throw new NestedConditionalBlocksException();
        }
        preg_match_all('/\}[^{]*\}/', $queryTemplate, $matchesRight, PREG_SET_ORDER);
        if (count($matchesRight) > 0) {
            throw new NestedConditionalBlocksException();
        }
        preg_match_all('/\?/', $queryTemplate, $matches, PREG_SET_ORDER);
        if (count($matches) !== count($parameters)) {
            throw new WrongParamsCountException();
        }
    }

    protected function cleanQueryFromSkippedConditionalBlocks(string $query): string
    {
        if ($this->skippedParamValue !== '') {
            preg_match_all(
                sprintf('/\{[^}]*%s[^}]*\}/', $this->skippedParamValue),
                $query,
                $skippedBlocks,
                PREG_SET_ORDER
            );
            if (count($skippedBlocks) > 0) {
                $skippedBlocks = array_map(function ($block) {
                    return $block[0];
                }, $skippedBlocks);
                $query = str_replace($skippedBlocks, '', $query);
            }
        }

        return str_replace(['{', '}'], '', $query);
    }

    /**
     * @return string
     * @throws BadFormatException
     * @throws BadParamTypeException
     */
    protected function build(): string
    {
        $queryArray = str_split($this->queryTemplate);
        $paramsArray = [];
        $paramsCounter = 0;
        $length = count($queryArray);
        for ($index = 0; $index < $length; $index++) {
            $char = $queryArray[$index];
            if ($char !== '?') {
                continue;
            }
            $spec = $char . ($queryArray[$index + 1] ?? '');
            $uniq = uniqid();

            $queryArray[$index] = $uniq;

            if ($this->skippedParamValue && $this->parameters[$paramsCounter] === $this->skippedParamValue) {
                $paramsArray[$uniq] = $this->parameters[$paramsCounter];
                $paramsCounter++;
                continue;
            }

            if (in_array($spec, FormatDictionary::getSpecialTypes())) {
                unset($queryArray[$index + 1]);
                $value = $this->formatValue($spec, $this->parameters[$paramsCounter]);
                $index++;
            } else {
                $value = $this->formatValue($char, $this->parameters[$paramsCounter]);
            }
            $paramsArray[$uniq] = $value;
            $paramsCounter++;
        }
        $result = implode('', $queryArray);
        return str_replace(array_keys($paramsArray), array_values($paramsArray), $result);
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return string
     * @throws BadFormatException
     * @throws BadParamTypeException
     */
    private function formatValue(string $type, mixed $value): string
    {
        $formatter = FormatterFactory::getFormatterByType($type);
        $formatter->validate($value);
        return $formatter->format($value);
    }

    public function setSkippedParamValue(string $skippedParamValue): void
    {
        $this->skippedParamValue = $skippedParamValue;
    }
}
