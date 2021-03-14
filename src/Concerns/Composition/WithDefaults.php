<?php


namespace DevelMe\RestfulList\Concerns\Composition;

use Closure;
use DevelMe\RestfulList\Contracts\Defaults;
use DevelMe\RestfulList\Exceptions\Registration\DefaultsException;
use ReflectionClass;
use ReflectionException;

trait WithDefaults
{
    protected Closure $compositions;

    /**
     * @param Closure|null $compositions
     * @throws ReflectionException
     */
    public function register(?Closure $compositions = null): void
    {
        $this->compositions = $compositions ?? $this->defaultProvider()->defaults();
    }

    /**
     * @return Defaults
     * @throws DefaultsException
     * @throws ReflectionException
     */
    private function defaultProvider(): Defaults
    {
        $this->validateConfiguredDefaults();
        /** @var Defaults $instance */
        $instance = ((new ReflectionClass(static::$defaults))->newInstance());

        return $instance;
    }

    /**
     * @throws DefaultsException
     * @throws ReflectionException
     */
    private function validateConfiguredDefaults()
    {
        if (empty(static::$defaults)) {
            throw new DefaultsException(static::class . " does not have defaults configured");
        }

        $class = new ReflectionClass(static::$defaults);

        if (!$class->implementsInterface(Defaults::class)) {
            throw new DefaultsException(static::$defaults . " does not implement " . Defaults::class);
        }
    }
}