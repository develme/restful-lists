<?php


namespace Tests\Contracts;

interface ChecksFilterSupport
{
    /**
     * @test
     */
    public function it_has_simplified_filtering(): void;

    /**
     * @test
     */
    public function it_has_complex_filtering(): void;

    /**
     * @test
     */
    public function it_filters_equals(): void;

    /**
     * @test
     */
    public function it_filters_not_equals(): void;

    /**
     * @test
     */
    public function it_filters_contains(): void;

    /**
     * @test
     */
    public function it_filters_starts_with(): void;

    /**
     * @test
     */
    public function it_filters_ends_with(): void;

    /**
     * @test
     */
    public function it_filters_in(): void;

    /**
     * @test
     */
    public function it_filters_between(): void;

    /**
     * @test
     */
    public function it_filters_less_than(): void;

    /**
     * @test
     */
    public function it_filters_less_than_or_equal(): void;

    /**
     * @test
     */
    public function it_filters_greater_than(): void;

    /**
     * @test
     */
    public function it_filters_greater_tha_or_equal(): void;
}