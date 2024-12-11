<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'regex:/^[\w.%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'ativo' => ['required', 'boolean'],
        ], [
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'As senhas precisam ser iguais.',
            'email.unique' => 'O email informado já está em uso.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email precisa ser um email válido.',
            'email.regex' => 'O email precisa ser um email válido no formato exemplo@dominio.com.',
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser uma string.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'ativo' => $input['ativo'],
        ]);

        return response()->json(['success' => 'Usuário criado com sucesso!']);
    }

    public function update(Request $request, User $user)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'regex:/^[\w.%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}$/'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'ativo' => ['required', 'boolean'],
        ], [
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'As senhas precisam ser iguais.',
            'email.email' => 'O email precisa ser um email válido.',
            'email.regex' => 'O email precisa ser um email válido no formato exemplo@dominio.com.',
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser uma string.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $user->update($input);

        return response()->json(['success' => 'Usuário atualizado com sucesso!']);
    }
}
