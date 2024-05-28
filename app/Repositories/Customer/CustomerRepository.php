<?php

namespace App\Repositories\Customer;

use App\Http\Requests\Customer\PostRequest;
use App\Http\Requests\Customer\PutRequest;
use App\Models\Customer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CustomerRepository implements CustomerRepositoryInterface
{
    protected $model;

    public function __construct(Customer $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $limit
     * @return JsonResponse
     */
    public function getDataAll(int $limit): JsonResponse
    {
        try {
            return response()->json([
                'message' => 'Success',
                'data' => $this->model->paginate($limit)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * @param sting $uuid
     * @return JsonResponse
     */
    public function getDataByUuid(string $uuid): JsonResponse
    {
        try {
            $customer = $this->model->whereUuid($uuid)->first();

            if (!$customer) {
                return response()->json([
                    'message' => 'Not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Success',
                'data' => $customer
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * @param PostRequest $request
     * @return JsonResponse
     */
    public function storeData(PostRequest $request): JsonResponse
    {
        try {
            $payload = $request->validated();
            $payload['uuid'] = Str::uuid();

            $this->model->create($payload);

            return response()->json([
                'message' => 'Success',
                'data' => null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * @param PutRequest $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function updateData(PutRequest $request, string $uuid): JsonResponse
    {
        try {
            $customer = $this->model->whereUuid($uuid)->first();

            if (!$customer) {
                return response()->json([
                    'message' => 'Not found',
                    'data' => null
                ], 404);
            }

            $customer->update($request->validated());

            return response()->json([
                'message' => 'Success',
                'data' => $customer
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * @param sting $uuid
     * @return JsonResponse
     */
    public function destroyData(string $uuid): JsonResponse
    {
        try {
            $customer = $this->model->whereUuid($uuid)->first();

            if (!$customer) {
                return response()->json([
                    'message' => 'Not found',
                    'data' => null
                ], 404);
            }

            $customer->delete();

            return response()->json([
                'message' => 'Success',
                'data' => null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
