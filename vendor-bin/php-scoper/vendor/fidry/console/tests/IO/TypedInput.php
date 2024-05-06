<?php

/*
 * This file is part of the Fidry\Console package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fidry\Console\Tests\IO;

final class TypedInput
{
    /**
     * @var bool|TypeException
     */
    public $boolean;

    /**
     * @var null|bool|TypeException
     */
    public $nullableBoolean;

    /**
     * @var string|TypeException
     */
    public $string;

    /**
     * @var null|string|TypeException
     */
    public $nullableString;

    /**
     * @var string[]|TypeException
     */
    public $stringArray;

    /**
     * @var int|TypeException
     */
    public $integer;

    /**
     * @var null|int|TypeException
     */
    public $nullableInteger;

    /**
     * @var int[]|TypeException
     */
    public $integerArray;

    /**
     * @var float|TypeException
     */
    public $float;

    /**
     * @var null|float|TypeException
     */
    public $nullableFloat;

    /**
     * @var float[]|TypeException
     */
    public $floatArray;

    /**
     * @param bool|TypeException        $boolean
     * @param null|bool|TypeException   $nullableBoolean
     * @param string|TypeException      $string
     * @param null|string|TypeException $nullableString
     * @param string[]|TypeException    $stringArray
     * @param int|TypeException         $integer
     * @param null|int|TypeException    $nullableInteger
     * @param int[]|TypeException       $integerArray
     * @param float|TypeException       $float
     * @param null|float|TypeException  $nullableFloat
     * @param float[]|TypeException     $floatArray
     */
    public function __construct(
        $boolean,
        $nullableBoolean,
        $string,
        $nullableString,
        $stringArray,
        $integer,
        $nullableInteger,
        $integerArray,
        $float,
        $nullableFloat,
        $floatArray
    ) {
        $this->boolean = $boolean;
        $this->nullableBoolean = $nullableBoolean;
        $this->string = $string;
        $this->nullableString = $nullableString;
        $this->stringArray = $stringArray;
        $this->integer = $integer;
        $this->nullableInteger = $nullableInteger;
        $this->integerArray = $integerArray;
        $this->float = $float;
        $this->nullableFloat = $nullableFloat;
        $this->floatArray = $floatArray;
    }

    /**
     * @param bool|TypeException        $boolean
     * @param null|bool|TypeException   $nullableBoolean
     * @param string|TypeException      $string
     * @param null|string|TypeException $nullableString
     * @param int|TypeException         $integer
     * @param null|int|TypeException    $nullableInteger
     * @param float|TypeException       $float
     * @param null|float|TypeException  $nullableFloat
     */
    public static function createForScalar(
        TypeException $arrayToScalarTypeException,
        $boolean,
        $nullableBoolean,
        $string,
        $nullableString,
        $integer,
        $nullableInteger,
        $float,
        $nullableFloat
    ): self {
        return new self(
            $boolean,
            $nullableBoolean,
            $string,
            $nullableString,
            $arrayToScalarTypeException,
            $integer,
            $nullableInteger,
            $arrayToScalarTypeException,
            $float,
            $nullableFloat,
            $arrayToScalarTypeException,
        );
    }

    /**
     * @param string[]|TypeException $stringArray
     * @param int[]|TypeException    $integerArray
     * @param float[]|TypeException  $floatArray
     */
    public static function createForArray(
        TypeException $scalarToArrayTypeException,
        $stringArray,
        $integerArray,
        $floatArray
    ): self {
        return new self(
            $scalarToArrayTypeException,
            $scalarToArrayTypeException,
            $scalarToArrayTypeException,
            $scalarToArrayTypeException,
            $stringArray,
            $scalarToArrayTypeException,
            $scalarToArrayTypeException,
            $integerArray,
            $scalarToArrayTypeException,
            $scalarToArrayTypeException,
            $floatArray,
        );
    }
}
