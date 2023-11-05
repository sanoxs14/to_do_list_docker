<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
     // Registro de usuario
     public function register(Request $request)
     {
        
         $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
             'email' => 'required|email|max:255|unique:users',
             'password' => 'required|string|min:8',
         ]);

         if($validator->fails()){
            return response()->json($validator->errors(), 422);
         }
        
         $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
         ]);
         $token = $user->createToken('auth_token')->plainTextToken;
 
         return response()->json(['data' =>$user,'acces_token' =>$token], 201);
     }
     // Login de user
     public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        $user = User::where('email','=',$request->email)->firstOrFail();
      
        // Generar un token de acceso y obtener su valor
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => $user->name,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    // Logout del usuario
    public function logout(Request $request){
      $user = auth()->user();
      $user->tokens()->delete();//@intelephense-ignore-line
      
      return response()->json([
         'message' =>'Tokens eliminados correctamente'
     ]);
      
    }

}
