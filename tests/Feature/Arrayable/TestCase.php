<?php


namespace Tests\Feature\Arrayable;

use DevelMe\RestfulList\Engines\Arrayable;
use DevelMe\RestfulList\Arrayable\Orchestration\Orchestrator;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Tests\Traits\WithEloquent;
use Tests\Traits\WithEngineParts;
use Tests\Traits\WithFaker;

class TestCase extends \Tests\TestCase
{
    use WithEngineParts, WithFaker, WithEloquent;

    protected array $engine = [
        'engine' => Arrayable::class,
        'orchestrator' => Orchestrator::class,
    ];

    protected function migrateDatabase(Manager $schema): void
    {
        $schema->schema()->create('examples', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->string('description')->nullable();
            $table->string('password', 500)->nullable();
            $table->string('remember_token')->nullable();
            $table->string('type')->nullable();

            $table->timestamps();
        });
    }
}