<?php
declare(strict_types=1);

namespace StubTests\Model;

interface StubProblemType
{
    public const STUB_IS_MISSED = 0;
    public const FUNCTION_PARAMETER_MISMATCH = 1;
    public const WRONG_PARENT = 2;
    public const WRONG_CONSTANT_VALUE = 3;
    public const FUNCTION_IS_DEPRECATED = 4;
    public const WRONG_FINAL_MODIFIER = 5;
    public const WRONG_STATIC_MODIFIER = 6;
    public const FUNCTION_ACCESS = 7;
    public const WRONG_INTERFACE = 8;
    public const PARAMETER_TYPE_MISMATCH = 9;
    public const PARAMETER_REFERENCE = 10;
    public const PARAMETER_VARARG = 11;
    public const ABSENT_IN_META = 12;
    public const PROPERTY_IS_STATIC = 13;
    public const PROPERTY_ACCESS = 14;
    public const PROPERTY_TYPE = 15;
    public const PARAMETER_HAS_SCALAR_TYPEHINT = 16;
    public const FUNCTION_HAS_RETURN_TYPEHINT = 17;
    public const PARAMETER_NAME_MISMATCH = 18;
    public const HAS_NULLABLE_TYPEHINT = 19;
    public const HAS_UNION_TYPEHINT = 20;
    public const HAS_DUPLICATION = 21;
    public const TYPE_IN_PHPDOC_DIFFERS_FROM_SIGNATURE = 22;
    public const WRONG_PARAMETER_DEFAULT_VALUE = 23;
    public const WRONG_RETURN_TYPEHINT = 24;
    public const WRONG_OPTIONALLITY = 27;
    public const PROPERTY_READONLY = 28;
}
