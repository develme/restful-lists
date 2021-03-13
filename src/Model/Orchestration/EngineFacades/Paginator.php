<?php


namespace DevelMe\RestfulList\Model\Orchestration\EngineFacades;


use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Pagination\Setting as PaginationSettingInterface;
use DevelMe\RestfulList\Contracts\Pagination\Paginator as PaginatorContract;
use DevelMe\RestfulList\Pagination\Setting as PaginationSetting;
use Exception;

class Paginator implements \DevelMe\RestfulList\Contracts\Engine\Paginator
{
    private PaginatorContract $paginator;

    public function __construct(PaginatorContract $paginator)
    {
        $this->paginator = $paginator;
    }

    public function paginate(Data $data, array $pagination)
    {
        $pagination = match (true) {
            !array_is_list($pagination) => PaginationSetting::createFromAssociative($pagination),
            array_is_list($pagination) => PaginationSetting::createFromOrdered($pagination),
            $pagination instanceof PaginationSettingInterface => $pagination,
            default => throw new Exception("Type not supported: " . gettype($pagination))
        };

        $this->paginator->paginate($pagination, $data);
    }
}