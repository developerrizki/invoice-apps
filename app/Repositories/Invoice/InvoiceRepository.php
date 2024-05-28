<?php

namespace App\Repositories\Invoice;

use App\Http\Requests\InvoiceRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    protected $model;
    protected $itemModel;
    protected $customerModel;
    public $apiInstance = null;

    public function __construct(
        Invoice $model,
        InvoiceItem $itemModel,
        Customer $customerModel
    )
    {
        $this->model = $model;
        $this->itemModel = $itemModel;
        $this->customerModel = $customerModel;

        Configuration::setXenditKey(config('xendit.secret_key'));
        $this->apiInstance = new InvoiceApi();
    }

    /**
     * @param int $limit
     * @return JsonResponse
     */
    public function getDataAll(int $limit): JsonResponse
    {
        try {
            $invoices = $this->model->paginate($limit);

            return response()->json([
                'message' => 'Success',
                'data' => $invoices
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
            $invoice = $this->model->whereUuid($uuid)->first();

            if (!$invoice) {
                return response()->json([
                    'message' => 'Not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Success',
                'data' => $invoice->load(['user', 'customer', 'items'])
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * @param InvoiceRequest $request
     * @return JsonResponse
     */
    public function storeData(InvoiceRequest $request): JsonResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $customer = $this->customerModel
                    ->whereUuid($request->customer_uuid)
                    ->first();

                $invoice = $this->model->create([
                    'uuid' => Str::uuid(),
                    'due_date' => Carbon::parse($request->due_date)->format('Y-m-d'),
                    'number' => $this->generateNumber(),
                    'user_id' => Auth::id(),
                    'total' => $this->calculateTotal($request->items),
                    'customer_id' => $customer->id ?? null
                ]);

                foreach ($request->items as $item) {
                    $this->itemModel->firstOrCreate([
                        'uuid' => Str::uuid(),
                        'item_name' => $item['name'],
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                        'invoice_id' => $invoice->id
                    ]);
                }
            });

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
     * @param InvoiceRequest $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function updateData(InvoiceRequest $request, string $uuid): JsonResponse
    {
        try {
            $invoice = $this->model->whereUuid($uuid)->first();

            if (!$invoice) {
                return response()->json([
                    'message' => 'Not found',
                    'data' => null
                ], 404);
            }

            DB::transaction(function () use ($request, $invoice) {
                $invoice->update([
                    'due_date' => Carbon::parse($request->due_date)->format('Y-m-d'),
                    'total' => $this->calculateTotal($request->items)
                ]);

                // Delete previous items
                $invoice->items()->delete();

                // Create new for invoice items
                foreach ($request->items as $item) {
                    $this->itemModel->firstOrCreate([
                        'uuid' => Str::uuid(),
                        'item_name' => $item['name'],
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                        'invoice_id' => $invoice->id
                    ]);
                }
            });

            return response()->json([
                'message' => 'Success',
                'data' => $invoice
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
            $invoice = $this->model->whereUuid($uuid)->first();

            if (!$invoice) {
                return response()->json([
                    'message' => 'Not found',
                    'data' => null
                ], 404);
            }

            $invoice->delete();

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
     * @param Request $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function paymentInvoice(Request $request, string $uuid): JsonResponse
    {
        try {
            $invoice = $this->model->whereUuid($uuid)->first();

            if (!$invoice) {
                return response()->json([
                    'message' => 'Not found',
                    'data' => null
                ], 404);
            }

            if ($invoice->status_payment !== Invoice::STATUS_PAYMENT_PENDING) {
                return response()->json([
                    'message' => 'Success',
                    'data' => $invoice
                ]);
            }

            $createInvoiceRequest = new CreateInvoiceRequest([
                'external_id' => (string) Str::uuid(),
                'description' => $request->description
                    ?? 'Transaction for invoice number : ' . $invoice->number,
                'amount' => $invoice->total,
                'payer_email' => $request->payer_email,
            ]);

            $result = $this->apiInstance->createInvoice($createInvoiceRequest);

            $invoice->status_payment = Invoice::STATUS_PAYMENT_PENDING;
            $invoice->checkout_link = $result['invoice_url'];
            $invoice->external_id = $createInvoiceRequest['external_id'];
            $invoice->save();

            return response()->json([
                'message' => "Success",
                'data' => [
                    'invoice_link' => $invoice->checkout_link
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Xendit Create Invoice Link Failed : ');
            Log::error($e);

            return response()->json([
                'message' => 'Failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function notificationInvoice(Request $request): JsonResponse
    {
        try {
            Log::info('Request from Xendit webhook : ');
            Log::info(json_encode($request->all(), JSON_PRETTY_PRINT));

            $result = $this->apiInstance->getInvoices(null, $request->external_id);

            Log::info('Result from Xendit get invoices : ');
            Log::info(json_encode($result));

            $invoice = $this->model->where('external_id', $request->external_id)->first();

            if (!$invoice) {
                return response()->json([
                    'message' => 'Not found',
                    'data' => null
                ], 404);
            }

            if ($invoice->status_payment === Invoice::STATUS_PAYMENT_SETTLED) {
                return response()->json('Payment successfully');
            }

            // Update status payment
            $invoice->status_payment = Str::lower($result[0]['status']);
            $invoice->save();

            return response()->json('Payment successfully');

        } catch (Exception $e) {
            Log::error('Xendit Webhook Failed : ');
            Log::error($e);

            return response()->json([
                'message' => 'Failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Process generate number invoice
     *
     * @return string
     */
    private function generateNumber(): string
    {
        return 'INV-' . date('Ymd') . '-' . date('His');
    }

    /**
     * Process calculate total qty from data items
     *
     * @param array $items
     * @return float
     */
    private function calculateTotal(array $items): float
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['qty'] * $item['price'];
        }

        return $total;
    }
}
