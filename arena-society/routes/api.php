<?php

use App\Http\Controllers\Api\AgendamentoController;
use App\Http\Controllers\Api\ProdutoController;
use App\Http\Controllers\Api\TimeController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::post('/login', function (Request $request) {
    $data = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
    $user = User::where('email', $data['email'])->first();
    if (! $user || ! Hash::check($data['password'], $user->password)) {
        return response()->json(['message' => 'Credenciais inválidas.'], 401);
    }

    return response()->json(['token' => $user->createToken('arena-api')->plainTextToken, 'token_type' => 'Bearer']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('times', TimeController::class);
    Route::apiResource('produtos', ProdutoController::class);
    Route::apiResource('agendamentos', AgendamentoController::class)->only(['index', 'store', 'show', 'destroy']);
    Route::patch('/agendamentos/{agendamento}/status', [AgendamentoController::class, 'updateStatus']);
    Route::post('/logout', function (Request $r) {
        $r->user()->currentAccessToken()->delete();

        return response()->noContent();
    });
});
