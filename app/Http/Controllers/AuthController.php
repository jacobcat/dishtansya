<?php


namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Registers a new user
     *
     * @param  \Illuminate\Http\Request         $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email|unique:users',
                'password' => 'required'
            ]);
        } catch (ValidationException $e) {
            $error_msg = "";
            foreach($e->errors() as $error) {
                $error_msg .= $error[0] . " ";
            }
            return response()->json(['message' => trim($error_msg)], 400);
        }

        try {
            $user = new User;
            $user->email = $request->input('email');
            $password = $request->input('password');
            $user->password = app('hash')->make($password);
            $user->save();
            return response()->json(['message' => 'User successfully registered'], 201);

        } catch(\Exception $e) {
            return response()->json(['message' => 'Error creating user'], 400);
        }
    }

    /**
     * Get the error messages for the defined validation rules
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',
            'email.email' => 'Email is not a valid e-mail',
            'email.unique' => 'Email already taken'
        ];
    }
}
