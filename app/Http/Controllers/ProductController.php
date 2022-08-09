<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions\SomethingWentWrong;
use App\Models\Product;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public const MODULO = 'products';
        /**
     * @OA\Get(
     *     tags={"Products"},
     *     path="/api/v1/products",
     *     description="Get all products",
     *     security={{"token": {}}},
     *     operationId="products_index",
     * @OA\Response(
     *    response=200,
     *    description="Successful Response",
     *    @OA\JsonContent(@OA\Property(property="data", type="Json", example="[...]"),
     *        )
     * ),
     * * @OA\Response(
     *    response=401,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated")
     *        )
     *     ),
     * )
     */

     public function index(){
        try{
            $product = Product::all();
            return new ProductResource($product);
        } catch (\Throwable $th) {
            throw new SomethingWentWrong($th);
        }
     }


   /**
     * @OA\Post(
     *     tags={"Products"},
     *     path="/api/v1/products",
     *     description="Create product",
     *     security={{"token": {}}},
     *     operationId="store_product",
     * @OA\RequestBody(
     *    required=true,
     *     @OA\MediaType(mediaType="multipart/form-data",
     *       @OA\Schema( required={"name", "price", "unit_id"},
     *                  @OA\Property(property="name", type="string", description="product name", example="Fresa"),
     *                  @OA\Property(property="price", type="integer", description="product price", example="90"),
     *                  @OA\Property(property="unit_id", type="integer", description="product Unit", example="2"),
     *       ),
     *      ),
     *   ),
     * @OA\Response(
     *    response=201,
     *    description="Successful Stored",
     *    @OA\JsonContent(@OA\Property(property="data", type="Json", example="[...]"),
     *        )
     * ),
     * * @OA\Response(
     *    response=401,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated")
     *        )
     *     ),
     * )
     */

     public function store(Request $request){


            $this->validate($request, [
                'name' => 'required',
                'price' => 'required',
                'unit_id' => 'required',
            ]);

        try {
            $product = new Product;
            $product->name = $request->name;
            $product->price = $request->price;
            $product->unit_id = $request->unit_id;
            $product->save();
            return new ProductResource($product);
        } catch (\Throwable $th) {
            throw new SomethingWentWrong($th);
        }
     }

     /**
     * @OA\Get(
     *     tags={"Products"},
     *     path="/api/v1/products/{product}/show",
     *     description="Get a product",
     *     security={{"token": {}}},
     *     operationId="products_show",
     *      *      @OA\Parameter(
     *          name="product",
     *          in="path",
     *          description="Id del producto",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="integer",
     *              example="1",
     *          )
     *      ),
     * @OA\Response(
     *    response=200,
     *    description="Successful Response",
     *    @OA\JsonContent(@OA\Property(property="data", type="Json", example="[...]"),
     *        )
     * ),
     * * @OA\Response(
     *    response=401,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated")
     *        )
     *     ),
     * )
     */

     public function show(Request $request, Product $product){
        try {
            return new ProductResource($product);
        } catch (\Throwable $th) {
            throw new SomethingWentWrong($th);
        }
     }

      /**
     * @OA\Post(
     *     tags={"Products"},
     *     path="/api/v1/products/{product}/update",
     *     description="Create product",
     *     security={{"token": {}}},
     *     operationId="update_product",
     *     @OA\Parameter(
     *          name="product",
     *          in="path",
     *          description="Id del producto",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="integer",
     *              example="1",
     *          )
     *      ),
     * @OA\RequestBody(
     *    required=true,
     *     @OA\MediaType(mediaType="multipart/form-data",
     *       @OA\Schema( required={"name", "price", "unit_id"},
     *                  @OA\Property(property="name", type="string", description="product name", example="Fresa"),
     *                  @OA\Property(property="price", type="integer", description="product price", example="90"),
     *                  @OA\Property(property="unit_id", type="integer", description="product Unit", example="2"),
     *       ),
     *      ),
     *   ),
     * @OA\Response(
     *    response=201,
     *    description="Successful Stored",
     *    @OA\JsonContent(@OA\Property(property="data", type="Json", example="[...]"),
     *        )
     * ),
     * * @OA\Response(
     *    response=401,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated")
     *        )
     *     ),
     * )
     */

     public function update(Request $request, Product $product){

        $this->validate($request, [
            'name' => 'required',
            'price' => 'required',
            'unit_id' => 'required',
        ]);

        try{
            $product->name = $request->name;
            $product->price = $request->price;
            $product->unit_id = $request->unit_id;
            $product->save();
            return new ProductResource($product);
        } catch (\Throwable $th) {
            throw new SomethingWentWrong($th);
        }
     }


     /**
     * @OA\Delete(
     *     tags={"Products"},
     *     path="/api/v1/products/{product}/delete",
     *     description="Delete a Product",
     *     security={{"token": {}}},
     *     operationId="delete_product",
     * @OA\Parameter(
     *          name="product",
     *          in="path",
     *          description="Id del producto",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="integer",
     *              example="1",
     *          )
     *      ),
     * @OA\Response(
     *    response=200,
     *    description="Successful Deleted",
     *    @OA\JsonContent(@OA\Property(property="status", type="string", example="successful"),
     *                     @OA\Property(property="message", type="string", example="Recurso borrado"),
     *        )
     * ),
     * @OA\Response(
     *    response=404,
     *    description="Error recurso no encontrado",
     *    @OA\JsonContent(@OA\Property(property="status", type="string", example="error"),
     *                     @OA\Property(property="message", type="string", example="Recurso no encontrado"),
     *        )
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated")
     *        )
     *     ),
     * )
     */

     public function destroy(Product $product){

        try {
            $product->delete();
        } catch (\Throwable $th) {
            throw new SomethingWentWrong($th);
        }
     }
}
