<?php

namespace App\Http\Controllers;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Response;


/**
 * @OA\Info(
 *    title="FoodicAPI",
 *    version="1.0.0",
 * )
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      in="header",
 *      name="bearerAuth",
 *      type="http",
 *      scheme="bearer"
 * ),

 * @OA\Post(
 *     path="/api/v1/login",
 *     tags={"token"},
 *     summary="get token ",
 *     @OA\Response(response=201, description="get Token", @OA\JsonContent()),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="password", type="string"),
 *         )
 *     )
 * )
 * @param OrderRequest $request
 * @return Response

 * @OA\Post(
 *     path="/api/v1/order/create",
 *     tags={"create order"},
 *     summary="create order ",
 *     @OA\Response(response=201, description="create order", @OA\JsonContent()),
 *  @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"products"},
 *             @OA\Property(
 *                 property="products",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"product_id", "quantity"},
 *                     @OA\Property(property="product_id", type="integer", example="1", description="ID of the product"),
 *                     @OA\Property(property="quantity", type="integer", example="2", description="Quantity of the product")
 *                 )
 *             )
 *         )
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
abstract class Controller
{
    //
}
