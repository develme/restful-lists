<?php


namespace Tests\Unit\Model;


use DevelMe\RestfulList\Engines\Model as ModelEngine;
use DevelMe\RestfulList\Filters\ModelComposer;
use Tests\Traits\WithEngineParts;

abstract class TestCase extends \Tests\TestCase
{
    use WithEngineParts;

    protected array $engine = [
        'engine' => ModelEngine::class,
        'composer' => ModelComposer::class
    ];
}