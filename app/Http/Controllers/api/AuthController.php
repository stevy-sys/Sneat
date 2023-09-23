<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/login",
     *      operationId="login",
     *      tags={"Authentication"},
     *      summary="Connexion utilisateur",
     *      description="Retourne le donnee de user avec token authentification",
     *      @OA\RequestBody(
     *          description="Données du utilisatuer à envoyer",
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *                  example="JohnDoe@gmail.com"
     *              ),
     *              @OA\Property(
     *                  property="password",
     *                  type="string",
     *                  example="votre mot de passe"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="User connecter",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     *      security={}
     * )
     */
    public function login(Request $request)
    {
        $validation = $this->validation($request,'login');
        if(!$validation['status']) {
            return response()->json($validation['warning'],401);
        }

        $user = User::where("email", $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'warning' => 'invalid credentials'
            ]);
        }
    
        $accessToken = $user->createToken('authToken')->accessToken ;

        return response()->json([
            'user' => $user,
            'accessToken' => $accessToken
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/register",
     *      operationId="register",
     *      tags={"Authentication"},
     *      summary="inscription de utilisateur",
     *      description="Inscription de nouvel utilisateur",
     *      @OA\RequestBody(
     *          description="Données du utilisatuer à envoyer",
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *                  example="JohnDoe@gmail.com"
     *              ),
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  example="JohnDoe"
     *              ),
     *              @OA\Property(
     *                  property="password",
     *                  type="string",
     *                  example="votre mot de passe"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Connexion avec success",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     *      security={}
     * )
     */
    public function register(Request $request)
    {
        $validation = $this->validation($request,'login');
        if(!$validation['status']) {
            return response()->json($validation['warning'],401);
        }

        $datanew = [
            'email' => $request->email,
            'name' => $request->name,
            'password' => Hash::make($request->password)
        ];


        try {
            User::create($datanew);
            return response()->json([
                'success' => 'Votre incription est fait avec success.
                 Veuiller verifier votre email pour confirmer votre inscription.'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ]);
        }
    }

    private function validation($request,$type)
    {
        if ($type == 'register') {
            $rules = [
                'email' => 'required|email',
                'name' => 'required',
                'password' => 'required|min:8',
            ];
            $validation = Validator::make($request->all(), $rules);
            if ($validation->fails()) {
                return $validation->errors();
            }else{
                return ['status' => 'ok'];
            }
        }else{
            $rules = [
                'email' => 'required|email',
                'password' => 'required|min:8',
            ];
            $validation = Validator::make($request->all(), $rules);
    
            if ($validation->fails()) {
                return ['warning' => $validation->errors()];
            }else{
                return ['status' => 'ok'];
            }
        }
    }
}
