<?php
declare(strict_types=1);

namespace StubTests\Model;

use RuntimeException;
use function array_key_exists;
use function count;

abstract class BasePHPClass extends BasePHPElement
{
    /**
     * @var PHPMethod[]
     */
    public $methods = [];

    /**
     * @var PHPConst[]
     */
    public $constants = [];

    public $isFinal = false;

    public function addConstant(PHPConst $parsedConstant): void
    {
        if (isset($parsedConstant->name)) {
            if (array_key_exists($parsedConstant->name, $this->constants)) {
                $amount = count(array_filter(
                    $this->constants,
                    function (PHPConst $nextConstant) use ($parsedConstant) {
                        return $nextConstant->name === $parsedConstant->name;
                    }
                ));
                $this->constants[$parsedConstant->name . '_duplicated_' . $amount] = $parsedConstant;
            } else {
                $this->constants[$parsedConstant->name] = $parsedConstant;
            }
        }
    }

    /**
     * @throws RuntimeException
     */
    public function getConstant($constantName): ?PHPConst
    {
        $constants = array_filter($this->constants, function (PHPConst $constant) use ($constantName): bool {
            return $constant->name === $constantName && $constant->duplicateOtherElement === false
                && BasePHPElement::entitySuitsCurrentPhpVersion($constant);
        });
        if (empty($constants)) {
            throw new RuntimeException("Constant $constantName not found in stubs for set language version");
        }
        return array_pop($constants);
    }

    public function addMethod(PHPMethod $parsedMethod): void
    {
        if (isset($parsedMethod->name)) {
            if (array_key_exists($parsedMethod->name, $this->methods)) {
                $amount = count(array_filter(
                    $this->methods,
                    function (PHPMethod $nextMethod) use ($parsedMethod) {
                        return $nextMethod->name === $parsedMethod->name;
                    }
                ));
                $this->methods[$parsedMethod->name . '_duplicated_' . $amount] = $parsedMethod;
            } else {
                $this->methods[$parsedMethod->name] = $parsedMethod;
            }
        }
    }

    /**
     * @throws RuntimeException
     */
    public function getMethod(string $methodName): ?PHPMethod
    {
        $methods = array_filter($this->methods, function (PHPMethod $method) use ($methodName): bool {
            return $method->name === $methodName && $method->duplicateOtherElement === false
                && BasePHPElement::entitySuitsCurrentPhpVersion($method);
        });
        if (empty($methods)) {
            throw new RuntimeException("Method $methodName not found in stubs for set language version");
        }
        return array_pop($methods);
    }
}
