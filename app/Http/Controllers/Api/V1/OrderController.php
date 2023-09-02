<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Paginator;
use App\Http\Controllers\Controller;
use App\Http\Filters\OrderFilter;
use App\Http\Requests\Api\V1\Order\CreateOrderRequest;
use App\Http\Requests\Api\V1\Order\ShipmentLocatorRequest;
use App\Http\Requests\Api\V1\Order\UpdateOrderRequest;
use App\Http\Resources\OrderDashboardResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderShipmentLocatorResource;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use OpenApi\Annotations as OA;
use Throwable;

class OrderController extends Controller
{

    /**
     * @OA\Get(
     *     path="api/v1/order",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function index(Request $request, Paginator $paginator): LengthAwarePaginator
    {
        $data = $paginator->paginateData($request, Order::query());
        $data->getCollection()->transform(function ($value) {
            return new OrderResource($value);
        });

        return $data;
    }

    /**
     * @OA\Post(
     *     path="api/v1/order/create",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="order_status_uuid",
     *                     type="string",
     *                     description="order status uuid"
     *                 ),
     *                 @OA\Property(
     *                     property="payment_uuid",
     *                     type="string",
     *                     description="payment uuid"
     *                 ),
     *                 @OA\Property(
     *                     property="products",
     *                     type="array",
     *                     description="array of products",
     *                     @OA\Items(
     *                        type="object",
     *                        @OA\Property(
     *                             property="product",
     *                             type="string"
     *                         ),
     *                        @OA\Property(
     *                             property="quantity",
     *                             type="integer"
     *                         ),
     *                     ),
     *                   example={
     *                   {
     *                      "product": "string",
     *                      "quantity": 1
     *                   }
     *                  }
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="object",
     *                     description="shipping and billing address",
     *                         @OA\Property(
     *                             property="shipping",
     *                             type="string"
     *                         ),
     *                        @OA\Property(
     *                             property="billing",
     *                             type="string"
     *                         ),
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="integer",
     *                     description="amount"
     *                 ),
     *                 required={"order_status_uuid","payment_uuid","products","address","amount"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     *
     * @throws Throwable
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        $attributes = $request->safe()->all();

        $status = OrderStatus::whereUuid($attributes['order_status_uuid'])->firstOrFail();
        $payment = Payment::whereUuid($attributes['payment_uuid'])->firstOrFail();
        $user = User::where('uuid', Auth::id())->firstOrFail();

        $order = $user->orders()->create(
            [
                'products' => json_encode($attributes['products']),
                'address' => json_encode($attributes['address']),
                'amount' => $attributes['amount'],
                'order_status_id' => $status->uuid,
                'payment_id' => $payment->uuid,
            ]
        );

        return response()->json(new OrderResource($order));
    }

    /**
     * @OA\Get(
     *     path="api/v1/order/{uuid}",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID parameter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function show(Order $order): JsonResponse
    {
        if (!Auth::user()->is_admin && ($order->user_id !== Auth::id())) {
            abort(404, "Order not found");
        }

        return response()->json(new OrderResource($order));
    }

    /**
     * @OA\Put(
     *     path="api/v1/order/{uuid}",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID parameter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="order_status_uuid",
     *                     type="string",
     *                     description="order status uuid"
     *                 ),
     *                 @OA\Property(
     *                     property="payment_uuid",
     *                     type="string",
     *                     description="payment uuid"
     *                 ),
     *                 @OA\Property(
     *                     property="products",
     *                     type="array",
     *                     description="array of products",
     *                     @OA\Items(
     *                        type="object",
     *                        @OA\Property(
     *                             property="product",
     *                             type="string"
     *                         ),
     *                        @OA\Property(
     *                             property="quantity",
     *                             type="integer"
     *                         ),
     *                     ),
     *                   example={
     *                   {
     *                      "product": "string",
     *                      "quantity": 1
     *                   }
     *                  }
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="object",
     *                     description="shipping and billing address",
     *                         @OA\Property(
     *                             property="shipping",
     *                             type="string"
     *                         ),
     *                        @OA\Property(
     *                             property="billing",
     *                             type="string"
     *                         ),
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="integer",
     *                     description="amount"
     *                 ),
     *                 required={"order_status_uuid","payment_uuid","products","address","amount"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     *
     * @throws Throwable
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $attributes = $request->safe()->all();

        $status = OrderStatus::whereUuid($attributes['order_status_uuid'])->firstOrFail();
        $payment = Payment::whereUuid($attributes['payment_uuid'])->firstOrFail();

        $order->update(
            [
                'products' => json_encode($attributes['products']),
                'address' => json_encode($attributes['address']),
                'amount' => $attributes['amount'],
                'order_status_id' => $status->uuid,
                'payment_id' => $payment->uuid,
            ]
        );

        return response()->json(new OrderResource($order));
    }

    /**
     * @OA\Delete(
     *     path="api/v1/order/{uuid}",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID parameter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function destroy(Order $order): JsonResponse
    {
        $order->delete();

        return response()->json(['message' => 'Order deleted!']);
    }

    /**
     * @OA\Get(
     *     path="api/v1/orders/shipment-locator",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Sort field",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         description="Sort direction (true for descending, false for ascending)",
     *         required=false,
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="orderUuid",
     *         in="query",
     *         description="Order UUID",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="customerUuid",
     *         in="query",
     *         description="Customer UUID",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="dateRange",
     *         in="query",
     *         description="Date range",
     *         required=false,
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                 property="from",
     *                 description="Start date",
     *                 type="string",
     *                 format="date"
     *             ),
     *             @OA\Property(
     *                 property="to",
     *                 description="End date",
     *                 type="string",
     *                 format="date"
     *             )
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="fixRange",
     *         in="query",
     *         description="Available values : today, monthly, yearly",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function shipmentLocator(
        ShipmentLocatorRequest $request,
        Paginator $paginator,
        OrderFilter $filter
    ): JsonResponse {
        $query = Order::query()->filter($filter)->whereNotNull('shipped_at');

        $data = $paginator->paginateData($request, $query);

        return response()->json(OrderShipmentLocatorResource::collection($data)->response()->getData());
    }

    /**
     * @OA\Get(
     *     path="api/v1/orders/dashboard",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function dashboard(Request $request, Paginator $paginator, OrderFilter $filter): JsonResponse
    {
        $query = Order::query()->filter($filter)->with('orderStatus');

        // Calculate statistics
        $totalOrders = $query->whereHas('orderStatus', function ($query) {
            $query->where('title', '!=', 'cancelled');
        })->count();
        $potentialEarnings = $query->whereHas('orderStatus', function ($query) {
            $query->whereNotIn('title', ['cancelled', 'paid']);
        })->sum('amount');
        $totalEarnings = $query->whereHas('orderStatus', function ($query) {
            $query->where('title', 'paid');
        })->sum('amount');

        $data = $paginator->paginateData($request, $query);

        $statistics = [
            'total_orders' => $totalOrders,
            'total_earnings' => $totalEarnings,
            'potential_earnings' => $potentialEarnings,
        ];

        $responseData = array_merge(OrderDashboardResource::collection($data)->response()->getData(true), $statistics);

        return response()->json($responseData);
    }

    /**
     * @OA\Get(
     *     path="api/v1/orders/{uuid}/download",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID parameter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function download(Order $order): Response
    {
        $products = json_decode($order->products, true);

        $total = 0;
        $subTotal = 0;

        $productsAndQuantity = array_map(function ($product) use (&$total, &$subTotal) {
            $productModel = Product::where('uuid', $product['product'])->first();
            $subtotalProduct = $productModel->price * $product['quantity'];

            $total += $subtotalProduct;
            $subTotal += $subtotalProduct;

            return [
                'product' => $productModel,
                'quantity' => $product['quantity'],
                'subtotal' => $subtotalProduct,
            ];
        }, $products);

        $data = [
            'order' => $order,
            'paymentType' => strtoupper(str_replace('_', ' ', $order->payment->type)),
            'productsAndQuantity' => $productsAndQuantity,
            'address' => json_decode($order->address),
            'subtotal' => $subTotal,
            'total' => $total + $order->delivery_fee,
            'created' => Carbon::parse($order->created_at)->format('d F, Y'),
        ];

        $pdf = Pdf::loadView('pdf.order', $data);

        return $pdf->download("{$order->uuid}.pdf");
    }

}
