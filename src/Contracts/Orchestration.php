<?php


namespace DevelMe\RestfulList\Contracts;

use DevelMe\RestfulList\Contracts\Engine\Arrangement;
use DevelMe\RestfulList\Contracts\Engine\Filtration;
use DevelMe\RestfulList\Contracts\Engine\Paginator;
use DevelMe\RestfulList\Contracts\Engine\Result;

/**
 * Interface Orchestration
 * @package DevelMe\RestfulList\Contracts
 * @method static Counter counter()
 * @method static Filtration filter()
 * @method static Arrangement order()
 * @method static Paginator pagination()
 * @method static Result result()
 */
interface Orchestration
{
    public function orchestrate(string $ask): mixed;
}