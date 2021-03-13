<?php


namespace DevelMe\RestfulList\Model\Orchestration;


use Closure;
use DevelMe\RestfulList\Contracts\Orchestration;
use DevelMe\RestfulList\Contracts\Registration;
use ReflectionClass;

class Orchestrator implements Registration, Orchestration
{
    private Closure $compositions;

    protected static string $defaults = Composition::class;

    public function register(?Closure $compositions = null): void
    {
        $this->compositions = $compositions ?? $this->defaults();
    }

    public function __call(string $name, array $arguments)
    {
        return $this->orchestrate($name);
    }

    public function orchestrate(string $ask): mixed
    {
        $compositions = $this->compositions;

        return $compositions($ask);
    }

    /**
     * @throws \ReflectionException
     */
    private function defaults(): Closure
    {
        return ((new ReflectionClass(static::$defaults))->newInstance())->defaults();
    }
}