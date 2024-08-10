<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PexelsService;

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

}
