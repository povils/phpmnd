<?php

namespace Povils\PHPMND;

use Povils\PHPMND\Extension\ArgumentExtension;
use Povils\PHPMND\Extension\ArrayExtension;
use Povils\PHPMND\Extension\AssignExtension;
use Povils\PHPMND\Extension\ConditionExtension;
use Povils\PHPMND\Extension\DefaultParameterExtension;
use Povils\PHPMND\Extension\Extension;
use Povils\PHPMND\Extension\OperationExtension;
use Povils\PHPMND\Extension\PropertyExtension;
use Povils\PHPMND\Extension\ReturnExtension;
use Povils\PHPMND\Extension\SwitchCaseExtension;

/**
 * Class ExtensionResolver
 *
 * @package Povils\PHPMND
 */
class ExtensionResolver
{
    const ALL_EXTENSIONS = 'all';

    /**
     * @var Extension[]
     */
    private $allExtensions;

    /**
     * @var Extension[]
     */
    private $defaultExtensions;

    /**
     * @var Extension[]
     */
    private $resolvedExtensions = [];

    public function resolve(array $extensionNames)
    {
        $this->resolvedExtensions = $this->defaults();
        if (($allKey = array_search(self::ALL_EXTENSIONS, $extensionNames)) !== false) {
            $this->resolvedExtensions = $this->all();
            unset($extensionNames[$allKey]);
        }

        foreach ($extensionNames as $extensionName) {
            if ($this->startsWithMinus($extensionName)) {
                $this->removeExtension($extensionName);
                continue;
            }

            $this->addExtension($extensionName);
        }

        return $this->resolvedExtensions;
    }

    /**
     * @return Extension[]
     */
    public function defaults()
    {
        if (null === $this->defaultExtensions) {
            $this->defaultExtensions = [
                new ConditionExtension,
                new ReturnExtension,
                new SwitchCaseExtension
            ];
        }

        return $this->defaultExtensions;
    }

    /**
     * @return Extension[]
     */
    public function all()
    {
        if (null === $this->allExtensions) {
            $this->allExtensions = array_merge(
                [
                    new ArgumentExtension,
                    new ArrayExtension,
                    new AssignExtension,
                    new DefaultParameterExtension,
                    new OperationExtension,
                    new PropertyExtension
                ],
                $this->defaults()
            );
        }

        return $this->allExtensions;
    }

    /**
     * @param string $extensionName
     */
    private function addExtension($extensionName)
    {
        if ($this->exists($extensionName)) {
            foreach ($this->all() as $extension) {
                if ($extension->getName() === $extensionName) {
                    $this->resolvedExtensions[] = $extension;

                    return;
                }
            };
        }
    }

    /**
     * @param string $extensionName
     *
     * @throws \Exception
     */
    private function removeExtension($extensionName)
    {
        $extensionNameWithoutMinus = substr($extensionName, 1);
        if ($this->exists($extensionNameWithoutMinus)) {
            foreach ($this->resolvedExtensions as $key => $resolvedExtension) {
                if ($extensionNameWithoutMinus === $resolvedExtension->getName()) {
                    unset($this->resolvedExtensions[$key]);

                    return;
                }
            }
        }
    }

    /**
     * @param string $extensionName
     * @return bool
     *
     * @throws \Exception
     */
    private function exists($extensionName)
    {
        foreach ($this->all() as $extension) {
            if ($extension->getName() === $extensionName) {
                return true;
            }
        }

        throw new \InvalidArgumentException(sprintf('Extension "%s" does not exist', $extensionName));
    }

    /**
     * @param string $extensionName
     *
     * @return bool
     */
    private function startsWithMinus($extensionName)
    {
        return 0 === strpos($extensionName, '-');
    }
}
