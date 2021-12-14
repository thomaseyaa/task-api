<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiTokenController extends Controller
{
    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => "required"
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'The provided credentials are incorrect.'], 401);
        }

        $user->tokens()->where('tokenable_id', $user->id)->delete();

        $token = $user->createToken($request->device_name, ['posts:read'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'email' => $user->email,
            'name' => $user->name,
            'created_at' => $user->created_at
        ]);

        /**
         * @OA\Post(path="/api/auth/login",
         *   tags={"auth"},
         *   summary="Login user",
         *   description="Login form",
         *   operationId="loginUser",
         * @OA\RequestBody(
         *    required=true,
         *    description="User email and password for login",
         *    @OA\JsonContent(
         *       required={"email","password"},
         *       @OA\Property(property="email", type="string", format="email", example="thomas@example.com"),
         *       @OA\Property(property="password", type="string", format="password", example="1234"),
         *       @OA\Property(property="device_name", type="string", example="true"),
         *    ),
         * ),
         *  @OA\Response(
         *    response=200,
         *    description="Success",
         *    @OA\JsonContent(
         *       @OA\Property(property="token", type="string"),
         *       @OA\Property(property="name", type="string", example="Thomas"),
         *       @OA\Property(property="email", type="string", example="thomas@example.com")
         *        )
         *     ),
         *   @OA\Response(
         *    response=400,
         *    description="Error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="Invalid input"),
         *        )
         *     ),
         *   @OA\Response(
         *    response=401,
         *    description="Error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="Invalid creditentials"),
         *        )
         *     ),
         * )
         */
    }

    // Register
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => "required",
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $exists = User::where('email', $request->email)->exists();

        if ($exists) {
            return response()->json([
                'error' => "You are already registered. Please login instead"
            ], 409);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name
        ]);

        $token = $user->createToken($request->device_name, ['posts:read'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'email' => $user->email,
            'name' => $user->name,
            'created_at' => $user->created_at
        ]);

        /**
         * @OA\Post(path="/api/auth/register",
         *   tags={"auth"},
         *   summary="Register",
         *   description="Register form",
         *   operationId="registerUser",
         * @OA\RequestBody(
         *    required=true,
         *    description="User email, name, password, device_name for register",
         *    @OA\JsonContent(
         *       required={"email", "name", "password"},
         *       @OA\Property(property="email", type="string", format="email", example="thomas@example.com"),
         *       @OA\Property(property="name", type="string", example="Thomas"),
         *       @OA\Property(property="password", type="string", format="password", example="1234"),
         *       @OA\Property(property="device_name", type="string", example="Mac"),
         *    ),
         * ),
         *  @OA\Response(
         *    response=200,
         *    description="Success",
         *    @OA\JsonContent(
         *       @OA\Property(property="token", type="string"),
         *       @OA\Property(property="name", type="string", example="Thomas"),
         *       @OA\Property(property="email", type="string", example="thomas@example.com"),
         *       @OA\Property(property="created_at", type="date-time", example="2021-12-03 12:00:00"),
         *        )
         *     ),
         *   @OA\Response(
         *    response=400,
         *    description="Error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="All fields are required"),
         *        )
         *     ),
         *   @OA\Response(
         *    response=409,
         *    description="Error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="You are already registered. Please login instead"),
         *        )
         *     ),
         * )
         */
    }

    // Logout
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response(null, 204);

        /**
         * @OA\Post(path="/api/auth/logout",
         *   tags={"auth"},
         *   summary="Logout user",
         *   description="Logout",
         *   operationId="logOut",
         *  @OA\Response(
         *    response=200,
         *    description="Success",
         *     ),
         * )
         */
    }
}
