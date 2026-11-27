<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalvarTimeRequest;
use App\Http\Resources\TimeResource;
use App\Models\Time;
use Illuminate\Http\Request;

class TimeController extends Controller
{
    public function index(Request $r)
    {
        return TimeResource::collection(Time::query()->when($r->busca, fn ($q, $s) => $q->where('nome', 'ilike', '%'.$s.'%')->orWhere('telefone', 'ilike', '%'.$s.'%'))->withCount('agendamentos')->orderBy('nome')->paginate(10));
    }

    public function store(SalvarTimeRequest $r)
    {
        return (new TimeResource(Time::create($r->validated())))->response()->setStatusCode(201);
    }

    public function show(Time $time)
    {
        return new TimeResource($time->load('agendamentos'));
    }

    public function update(SalvarTimeRequest $r, Time $time)
    {
        $time->update($r->validated());

        return new TimeResource($time->refresh());
    }

    public function destroy(Time $time)
    {
        $time->delete();

        return response()->noContent();
    }
}
