<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Repositories\Invoice\InvoiceRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    private $invoiceRepository;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->invoiceRepository->getDataAll($request->limit ?? 25);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param InvoiceRequest $request
     * @return JsonResponse
     */
    public function store(InvoiceRequest $request): JsonResponse
    {
        return $this->invoiceRepository->storeData($request);
    }

    /**
     * Display the specified resource.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        return $this->invoiceRepository->getDataByUuid($uuid);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param InvoiceRequest $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function update(InvoiceRequest $request, string $uuid): JsonResponse
    {
        return $this->invoiceRepository->updateData($request, $uuid);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        return $this->invoiceRepository->destroyData($uuid);
    }

    /**
     * Process payment integration with Xendit
     *
     * @param Request $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function payment(Request $request, string $uuid): JsonResponse
    {
        return $this->invoiceRepository->paymentInvoice($request, $uuid);
    }

    /**
     * Webhook notification for receive data from xendit
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function notification(Request $request): JsonResponse
    {
        return $this->invoiceRepository->notificationInvoice($request);
    }
}
