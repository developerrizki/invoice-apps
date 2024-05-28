<?php

namespace App\Repositories\Customer;

use App\Http\Requests\Customer\PostRequest;
use App\Http\Requests\Customer\PutRequest;
use Illuminate\Http\JsonResponse;

interface CustomerRepositoryInterface
{
    /**
     * Get list data customer with pagination
     *
     * @param int $limit
     * @return JsonResponse
     */
    public function getDataAll(int $limit): JsonResponse;

    /**
     * Get specific data customer with uuid
     *
     * @param sting $uuid
     * @return JsonResponse
     */
    public function getDataByUuid(string $uuid): JsonResponse;

    /**
     * Store data customer
     *
     * @param PostRequest $request
     * @return JsonResponse
     */
    public function storeData(PostRequest $request): JsonResponse;

    /**
     * Update specific data customer by uuid
     *
     * @param PutRequest $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function updateData(PutRequest $request, string $uuid): JsonResponse;

    /**
     * Destroy specific data customer by uuid
     *
     * @param sting $uuid
     * @return JsonResponse
     */
    public function destroyData(string $uuid): JsonResponse;
}
