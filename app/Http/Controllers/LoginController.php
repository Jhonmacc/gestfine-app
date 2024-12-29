<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PexelsService;
use App\Models\User;

class LoginController extends Controller
{
    protected $pexelsService;

    public function __construct(PexelsService $pexelsService)
    {
        $this->pexelsService = $pexelsService;
    }

    public function welcome()
    {
        $backgroundImage = $this->pexelsService->getRandomPhoto();
        return view('welcome', compact('backgroundImage'));
    }

    public function showLoginForm()
    {
        $backgroundImage = $this->pexelsService->getRandomPhoto();
        return view('auth.login', compact('backgroundImage'));
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Verificar se o usuário existe e está ativo antes de tentar autenticar
        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            if ($user->ativo == 0) {
                return redirect()->route('login')->withErrors(['email' => 'Seu usuário foi desativado no sistema.']);
            }

            if (Auth::attempt($credentials)) {
                return redirect()->intended('dashboard');
            }
        }

        return redirect()->route('login')->withErrors(['email' => 'As credenciais fornecidas estão incorretas.']);
    }
}
