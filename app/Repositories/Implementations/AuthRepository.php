<?php

namespace App\Repositories\Implementations;

use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\Repositories\Contracts\IAuthRepository;
use App\Repositories\Core\Repository;
use App\Tools\ResponseCodes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str as Str;

class AuthRepository extends Repository implements IAuthRepository 
{
     /**
     * @var array
     */
    protected $fieldSearchable = [
        'email'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return User::class;
    }

    /**
     * Register user method
     * @param array params
     * @return User $user
     */
    public function register($params)
    {
        $params->merge([
            'password' => Hash::make($params->password),
            'role_id' => '2',
            'is_citizen' => 1,
            'active' => 1
            // 'email_verified_at' => \now()
        ]);
        
        $user = $this->create($params->all());

        $params->merge([
            'user_id' => $user->id
        ]);

        $user->citizenProfile()->create($params->all());

        return $user;

    }

    /**
     * Create token password reset
     *
     * @param  [Request] $request
     * @return [string] message
     */
    public function createPasswordResetToken($request) 
    {
        $user = $this->model->where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'message' => "No podemos encontrar un usuario con esa dirección de correo electrónico."
            ], ResponseCodes::NOT_FOUND);
        }

        if (!$user->isCitizen()) {
            return response()->json([
                'message' => "No podemos encontrar un usuario con esa dirección de correo electrónico."
            ], ResponseCodes::NOT_FOUND);
        }

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Str::random(60),
                'reset_code'=> random_int(100000, 999999)
             ]
        );

        if ($user && $passwordReset){
            $user->notify(new PasswordResetRequest($passwordReset->token, $passwordReset->reset_code));
                        
            return response()->json([
                'message' => 'Se le ha enviado un codigo de confirmación a su correo electrónico.'
            ]);
        }            
    }

     /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [object] passwordReset object
     */
    public function findPasswordResetToken($token)
    {
        $passwordReset = PasswordReset::where('token', $token) ->first();

        if (!$passwordReset)
            return response()->json([
                'message' => 'Este token es invalido o ha expirado.'
            ], ResponseCodes::NOT_FOUND);

            // Modify password reset duration
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            
            $passwordReset->delete();

            return response()->json([
                'message' => 'Este token es invalido o ha expirado.'
            ], ResponseCodes::NOT_FOUND);
        }

        return $passwordReset;
    }

    /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [object] user object
     */
    public function resetPassword($request)
    {
        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email],
            ['reset_code', $request->reset_code],
        ])->first();

        if (!$passwordReset)
            return response()->json([
                'message' => 'Este token es invalido o ha expirado.'
            ], ResponseCodes::NOT_FOUND);

        $user = $this->model->where('email', $passwordReset->email)->first();

        if (!$user)
        
            return response()->json([
                'message' => "No podemos encontrar un usuario con esa dirección de correo electrónico."
            ], ResponseCodes::NOT_FOUND);
            
        $user->password = bcrypt($request->password);
        $user->save();
        
        $passwordReset->delete();

        $user->notify(new PasswordResetSuccess($passwordReset));

        return response()->json([
            'message' => 'Contraseña restaurada satisfactoriamente'
        ]);
    }
}