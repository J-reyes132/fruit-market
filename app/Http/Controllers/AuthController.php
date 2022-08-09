<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Tools\ResponseCodes;
use App\Http\Resources\UserResourceLogin;
use App\Exceptions\SomethingWentWrong;
use App\Http\Requests\RegisterRequest;
use App\Repositories\Implementations\AuthRepository;

class AuthController extends Controller
{
    private $authRepository;

    public function __construct(AuthRepository $authRepo) {
        $this->authRepository = $authRepo;
    }

    /**
     * @OA\Post(
     * tags={"Login"},
     * path="/api/v1/login",
     * description="Autenticarse en el sistema",
     * operationId="login",
     * @OA\RequestBody(
     *    required=true,
     *     @OA\MediaType(mediaType="multipart/form-data",
     *       @OA\Schema( required={"email","password"},
     *                  @OA\Property(property="email", type="string", description="Email Usuario", example="admin@pruebas.com"),
     *                  @OA\Property(property="password", type="string", description="Password", example="admin"),
     *       ),
     *      ),
     *   ),
     * @OA\Response(
     *    response=401,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     ),
     * @OA\Response(
     *    response=200,
     *    description="Successful Response",
     *    @OA\JsonContent(
     *       @OA\Property(property="user", type="json", example="User information"),
     *       @OA\Property(property="token", type="string", example="bearer token for user"),
     *        )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['status' => 'error', 'message' => 'Credenciales Invalidas'], ResponseCodes::UNAUTHORIZED);
        }

        $user = auth()->user();

        $token = auth()->user()->createToken(env('TOKEN_SECRET'))->accessToken;

        return response(['user' => new UserResourceLogin($user), 'token' => $token], ResponseCodes::OK);
    }

    /**
     * @OA\Post(
     *     tags={"Login"},
     *     path="/api/v1/logout",
     *     description="Revocar autorizacion en el sistema",
     *     security={{"token": {}}},
     *
     * @OA\Response(
     *    response=200,
     *    description="Successful Response",
     *    @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="successful"),
     *       @OA\Property(property="message", type="string", example="User has been logged out"),
     *        )
     *     ),
     * * @OA\Response(
     *    response=401,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated")
     *        )
     *     ),
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['status' => 'successful','message' => 'Usuario ha salido del sistema'], ResponseCodes::OK);
    }


    /**
     * @OA\Post(
     * tags={"Register"},
     * path="/api/v1/register",
     * description="Registrarse en el sistema",
     * operationId="register",
     * @OA\RequestBody(
     *    required=true,
     *     @OA\MediaType(mediaType="multipart/form-data",
     *       @OA\Schema( required={"email","password","name"},
     *                  @OA\Property(property="email", type="string", description="Email Usuario", example="email@gmail.com"),
     *                  @OA\Property(property="password", type="string", description="Password", example="123456"),
     *                  @OA\Property(property="name", type="string", description="Nombre", example="Nombre 1"),
     *       ),
     *      ),
     *   ),
     * @OA\Response(
     *    response=401,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     ),
     * @OA\Response(
     *    response=200,
     *    description="Successful Response",
     *    @OA\JsonContent(
     *       @OA\Property(property="user", type="json", example="User information"),
     *       @OA\Property(property="token", type="string", example="bearer token for user"),
     *        )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        try {
            $loginData = $request->only(['email', 'password']);

            $this->authRepository->register($request);

            $request->merge([
                'password' => $loginData['password']
            ]);

            // dd($request->all(), $loginData);

            return $this->login($request);

            // return response(['status' => 'error', 'message' => 'Usuario desactivado'], ResponseCodes::UNAUTHORIZED);
        } catch (\Throwable $th) {
            throw new SomethingWentWrong($th);
        }
    }
}
