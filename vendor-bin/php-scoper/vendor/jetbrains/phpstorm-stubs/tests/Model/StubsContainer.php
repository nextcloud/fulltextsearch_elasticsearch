<?php
declare(strict_types=1);

namespace StubTests\Model;

use RuntimeException;
use function array_key_exists;
use function count;

class StubsContainer
{
    /**
     * @var PHPConst[]
     */
    private $constants = [];
    /**
     * @var PHPFunction[]
     */
    private $functions = [];
    /**
     * @var PHPClass[]
     */
    private $classes = [];
    /**
     * @var PHPInterface[]
     */
    private $interfaces = [];

    /**
     * @return PHPConst[]
     */
    public function getConstants(): array
    {
        return $this->constants;
    }

    public function getConstant(string $constantName, ?string $sourceFilePath = null): ?PHPConst
    {
        $constants = array_filter($this->constants, function (PHPConst $const) use ($constantName): bool {
            return $const->name === $constantName && $const->duplicateOtherElement === false
                && BasePHPElement::entitySuitsCurrentPhpVersion($const);
        });
        if (count($constants) === 1) {
            return array_pop($constants);
        }

        if ($sourceFilePath !== null) {
            $constants = array_filter($constants, function (PHPConst $constant) use ($sourceFilePath) {
                return $constant->sourceFilePath === $sourceFilePath
                    && BasePHPElement::entitySuitsCurrentPhpVersion($constant);
            });
        }
        if (count($constants) > 1) {
            throw new RuntimeException("Multiple constants with name $constantName found");
        }
        if (!empty($constants)) {
            return array_pop($constants);
        }
        return null;
    }

    public function addConstant(PHPConst $constant): void
    {
        if (isset($constant->name)) {
            if (array_key_exists($constant->name, $this->constants)) {
                $amount = count(array_filter(
                    $this->constants,
                    function (PHPConst $nextConstant) use ($constant) {
                        return $nextConstant->name === $constant->name;
                    }
                ));
                $this->constants[$constant->name . '_duplicated_' . $amount] = $constant;
            } else {
                $this->constants[$constant->name] = $constant;
            }
        }
    }

    /**
     * @return PHPFunction[]
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }

    /**
     * @param string $name
     * @param string|null $sourceFilePath
     * @param bool $shouldSuitCurrentPhpVersion
     * @return PHPFunction|null
     * @throws RuntimeException
     */
    public function getFunction(string $name, ?string $sourceFilePath = null, bool $shouldSuitCurrentPhpVersion = true): ?PHPFunction
    {
        $functions = array_filter($this->functions, function (PHPFunction $function) use ($shouldSuitCurrentPhpVersion, $name): bool {
            return $function->name === $name && (!$shouldSuitCurrentPhpVersion || BasePHPElement::entitySuitsCurrentPhpVersion($function));
        });
        if (count($functions) > 1) {
            $functions = array_filter($functions, function (PHPFunction $function): bool {
                return $function->duplicateOtherElement === false;
            });
        }
        if (count($functions) === 1) {
            return array_pop($functions);
        }

        if ($sourceFilePath !== null) {
            $functions = array_filter($functions, function (PHPFunction $function) use ($shouldSuitCurrentPhpVersion, $sourceFilePath) {
                return $function->sourceFilePath === $sourceFilePath
                    && (!$shouldSuitCurrentPhpVersion || BasePHPElement::entitySuitsCurrentPhpVersion($function));
            });
        }
        if (count($functions) > 1) {
            throw new RuntimeException("Multiple functions with name $name found");
        }
        if (!empty($functions)) {
            return array_pop($functions);
        }
        return null;
    }

    public function addFunction(PHPFunction $function): void
    {
        if (isset($function->name)) {
            if (array_key_exists($function->name, $this->functions)) {
                $amount = count(array_filter(
                    $this->functions,
                    function (PHPFunction $nextFunction) use ($function) {
                        return $nextFunction->name === $function->name;
                    }
                ));
                $function->duplicateOtherElement = true;
                $this->functions[$function->name . '_duplicated_' . $amount] = $function;
            } else {
                $this->functions[$function->name] = $function;
            }
        }
    }

    /**
     * @return PHPClass[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @param string $name
     * @param string|null $sourceFilePath
     * @param bool $shouldSuitCurrentPhpVersion
     * @return PHPClass|null
     * @throws RuntimeException
     */
    public function getClass(string $name, ?string $sourceFilePath = null, bool $shouldSuitCurrentPhpVersion = true): ?PHPClass
    {
        $classes = array_filter($this->classes, function (PHPClass $class) use ($shouldSuitCurrentPhpVersion, $name): bool {
            return $class->name === $name &&
                (!$shouldSuitCurrentPhpVersion || BasePHPElement::entitySuitsCurrentPhpVersion($class));
        });
        if (count($classes) === 1) {
            return array_pop($classes);
        }

        if ($sourceFilePath !== null) {
            $classes = array_filter($classes, function (PHPClass $class) use ($shouldSuitCurrentPhpVersion, $sourceFilePath) {
                return $class->sourceFilePath === $sourceFilePath &&
                    (!$shouldSuitCurrentPhpVersion || BasePHPElement::entitySuitsCurrentPhpVersion($class));
            });
        }
        if (count($classes) > 1) {
            throw new RuntimeException("Multiple classes with name $name found");
        }
        if (!empty($classes)) {
            return array_pop($classes);
        }
        return null;
    }

    /**
     * @return PHPClass[]
     */
    public function getCoreClasses(): array
    {
        return array_filter($this->classes, function (PHPClass $class): bool {
            return $class->stubBelongsToCore === true;
        });
    }

    public function addClass(PHPClass $class): void
    {
        if (isset($class->name)) {
            if (array_key_exists($class->name, $this->classes)) {
                $amount = count(array_filter(
                    $this->classes,
                    function (PHPClass $nextClass) use ($class) {
                        return $nextClass->name === $class->name;
                    }
                ));
                $this->classes[$class->name . '_duplicated_' . $amount] = $class;
            } else {
                $this->classes[$class->name] = $class;
            }
        }
    }

    /**
     * @param string $name
     * @param string|null $sourceFilePath
     * @param bool $shouldSuitCurrentPhpVersion
     * @return PHPInterface|null
     * @throws RuntimeException
     */
    public function getInterface(string $name, ?string $sourceFilePath = null, bool $shouldSuitCurrentPhpVersion = true): ?PHPInterface
    {
        $interfaces = array_filter($this->interfaces, function (PHPInterface $interface) use ($shouldSuitCurrentPhpVersion, $name): bool {
            return $interface->name === $name &&
                (!$shouldSuitCurrentPhpVersion || BasePHPElement::entitySuitsCurrentPhpVersion($interface));
        });
        if (count($interfaces) === 1) {
            return array_pop($interfaces);
        }

        if ($sourceFilePath !== null) {
            $interfaces = array_filter($interfaces, function (PHPInterface $interface) use ($shouldSuitCurrentPhpVersion, $sourceFilePath) {
                return $interface->sourceFilePath === $sourceFilePath &&
                    (!$shouldSuitCurrentPhpVersion || BasePHPElement::entitySuitsCurrentPhpVersion($interface));
            });
        }
        if (count($interfaces) > 1) {
            throw new RuntimeException("Multiple interfaces with name $name found");
        }
        if (!empty($interfaces)) {
            return array_pop($interfaces);
        }
        return null;
    }

    /**
     * @return PHPInterface[]
     */
    public function getInterfaces(): array
    {
        return $this->interfaces;
    }

    /**
     * @return PHPInterface[]
     */
    public function getCoreInterfaces(): array
    {
        return array_filter($this->interfaces, function (PHPInterface $interface): bool {
            return $interface->stubBelongsToCore === true;
        });
    }

    public function addInterface(PHPInterface $interface): void
    {
        if (isset($interface->name)) {
            if (array_key_exists($interface->name, $this->interfaces)) {
                $amount = count(array_filter(
                    $this->interfaces,
                    function (PHPInterface $nextInterface) use ($interface) {
                        return $nextInterface->name === $interface->name;
                    }
                ));
                $this->interfaces[$interface->name . '_duplicated_' . $amount] = $interface;
            } else {
                $this->interfaces[$interface->name] = $interface;
            }
        }
    }
}
