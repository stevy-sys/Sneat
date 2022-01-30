<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $validation = $this->validation($request,'login');
        if($validation['warning']) {
            return response()->json($validation['warning'],401);
        }

        $admin = Admin::where("email", $request->email)->first();
        if(!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'warning' => 'invalid credentials'
            ]);
           
        }
    
        $accessToken = $admin->createToken('authToken')->accessToken ;

        return response()->json([
            'user' => $admin,
            'accessToken' => $accessToken
        ]);
    }

    public function register(Request $request)
    {
        $validation = $this->validation($request,'login');
        if($validation['warning']) {
            return response()->json($validation['warning'],401);
        }

        $datanew = [
            'email' => $request->email,
            'name' => $request->name,
            'role' => 'admin',
            'password' => Hash::make($request->password)
        ];


        try {
            Admin::create($datanew);
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
                return ['message' => 'ok'];
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
                return ['statu' => 'ok'];
            }
        }
    }
}
