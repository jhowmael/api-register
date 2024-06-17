<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class UsersController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        if($user->save()){
            return response()->json([
                'message' => 'Usuário registrado com sucesso!',
                'user' => $user,
            ], 201);
        }

        return response()->json([
            'message' => 'Erro ao Salvar Registro',
        ], 500);  
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 200);
        }

        return response()->json(['message' => 'Credenciais inválidas'], 401);
    }

    public function edit($id, Request $request)
    {
        $user = Auth::user();
        if ($user->id != $id) {
            return response()->json(['message' => 'Não autorizado a editar este usuário'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'string|min:8',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['message' => 'Usuário atualizado com sucesso', 'user' => $user], 200);
    }

    public function view($id)
    {
        $user = User::findOrFail($id);

        $authenticatedUser = Auth::user();
        if ($authenticatedUser->id != $user->id) {
            return response()->json(['message' => 'Não autorizado a visualizar este usuário'], 403);
        }

        return response()->json(['user' => $user], 200);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);

        $authenticatedUser = Auth::user();
        if ($authenticatedUser->id != $user->id) {
            return response()->json(['message' => 'Não autorizado a deletar este usuário'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Usuário excluído com sucesso'], 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $response = Password::sendResetLink($request->only('email'));

        return $response == Password::RESET_LINK_SENT
            ? response()->json(['message' => 'E-mail de redefinição de senha enviado'], 200)
            : response()->json(['message' => 'Erro ao enviar o e-mail'], 400);
    }
}