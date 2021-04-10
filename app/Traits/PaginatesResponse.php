<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait PaginatesResponse
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param int|null     $itemsPerPage
     *
     * @return array
     */
    public function paginate($queryBuilder, $itemsPerPage = null)
    {
        $perPage = $itemsPerPage ?? config('settings.ItemsPerPage');
        $perPage = (int) $perPage;
        $paginator = call_user_func_array([$queryBuilder, 'paginate'], ['perPage' => $perPage, 'column' => ['*']]);
        return [
            'data' => $paginator->items(),
            'numberOfPages' => $paginator->lastPage(),
            'hasNextPage' => $paginator->currentPage() < $paginator->lastPage(),
            'currentPage' => $paginator->currentPage(),
            'nextPage' => $paginator->currentPage() < $paginator->lastPage() ? $paginator->currentPage() + 1 : 0,
        ];
    }

    /**
     * @param Collection $items
     *
     * @return array|Collection
     */
    private function getDict($items)
    {
        return $items->map(function ($value, $key) {
            return $value->getDict();
        });
    }
}
