<?php


namespace Tests\Contracts;

interface ChecksOrderSupport
{
    /**
     * @test
     */
    public function it_has_simplified_ordering(): void;
    /**
     * @test
     */
    public function it_has_complex_ordering(): void;
}