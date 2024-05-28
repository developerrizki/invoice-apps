<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\PostRequest;
use App\Http\Requests\Customer\PutRequest;
use App\Repositories\Customer\CustomerRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->customerRepository->getDataAll($request->limit ?? 25);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PostRequest $request
     * @return JsonResponse
     */
    public function store(PostRequest $request): JsonResponse
    {
        return $this->customerRepository->storeData($request);
    }

    /**
     * Display the specified resource.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        return $this->customerRepository->getDataByUuid($uuid);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PutRequest $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function update(PutRequest $request, string $uuid): JsonResponse
    {
        return $this->customerRepository->updateData($request, $uuid);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        return $this->customerRepository->destroyData($uuid);
    }
}
