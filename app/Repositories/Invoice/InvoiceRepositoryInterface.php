<?php

namespace App\Repositories\Invoice;

use App\Http\Requests\InvoiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface InvoiceRepositoryInterface
{
    /**
     * Get list data invoice with pagination
     *
     * @param int $limit
     * @return JsonResponse
     */
    public function getDataAll(int $limit): JsonResponse;

    /**
     * Get specific data invoice with uuid
     *
     * @param sting $uuid
     * @return JsonResponse
     */
    public function getDataByUuid(string $uuid): JsonResponse;

    /**
     * Store data invoice
     *
     * @param InvoiceRequest $request
     * @return JsonResponse
     */
    public function storeData(InvoiceRequest $request): JsonResponse;

    /**
     * Update specific data invoice by uuid
     *
     * @param PutRequest $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function updateData(InvoiceRequest $request, string $uuid): JsonResponse;

    /**
     * Destroy specific data invoice by uuid
     *
     * @param sting $uuid
     * @return JsonResponse
     */
    public function destroyData(string $uuid): JsonResponse;

    /**
     * Process create payment link with Xendit
     *
     * @param Request $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function paymentInvoice(Request $request, string $uuid): JsonResponse;

    /**
     * Webhook for receipt data from Xendit
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function notificationInvoice(Request $request): JsonResponse;
}
