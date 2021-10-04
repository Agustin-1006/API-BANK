<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Cuenta;
use App\Models\Evento;

class EventoController extends Controller
{
    public function show($id)
    {
        $Cuenta = Cuenta::find($id);
        if ($Cuenta) {
            return response()->json($Cuenta, 200);
        } else {
            return response()->json("La cuenta no existe", 404);
        }
    }

    public function checkEvent(Request $request)
    {
        switch ($request->input('tipo')) {
            case 'crear':
                try {
                    $Cuenta = Cuenta::create(
                        ["balance" => $request->input('balance'), "email" => $request->input('email')]
                    );
                    Evento::create(
                        ["origen" => $Cuenta->id, "destino" => $Cuenta->id, "tipo" => $request->input('tipo'), "balance" => $request->input('balance')]
                    );
                    return response()->json($Cuenta, 201);
                } catch (Exception $e) {
                    return response()->json($e, 404);
                }

                break;
            case 'deposito':
                $Cuenta = Cuenta::find($request->input('origen'));
                if ($Cuenta) {
                    Cuenta::where('id', $Cuenta->id)
                        ->update(['balance' => $Cuenta->balance + $request->input('balance')]);
                    Evento::create(
                        ["origen" => $Cuenta->id, "destino" => $Cuenta->id, "tipo" => $request->input('tipo'), "balance" => $request->input('balance')]
                    );
                    return response()->json(Cuenta::find($request->input('origen')), 200);
                } else {
                    return response()->json("La cuenta no existe", 404);
                }
                break;
            case 'retiro':
                $Cuenta = Cuenta::find($request->input('origen'));
                if ($Cuenta && $Cuenta->balance >= $request->input('balance')) {
                    Cuenta::where('id', $Cuenta->id)
                        ->update(['balance' => $Cuenta->balance - $request->input('balance')]);
                    Evento::create(
                        ["origen" => $Cuenta->id, "destino" => $Cuenta->id, "tipo" => $request->input('tipo'), "balance" => $request->input('balance')]
                    );
                    return response()->json(Cuenta::find($request->input('origen')), 200);
                } else {
                    return response()->json("La cuenta no existe o monto insuficiente", 404);
                }
                break;
            case 'transferencia':
                $Cuenta = Cuenta::find($request->input('origen'));
                $Cuenta2 = Cuenta::find($request->input('destino'));
                if ($Cuenta && $Cuenta->balance >= $request->input('balance') && $Cuenta2) {
                    Cuenta::where('id', $Cuenta->id)
                        ->update(['balance' => $Cuenta->balance - $request->input('balance')]);
                    Cuenta::where('id', $Cuenta2->id)
                        ->update(['balance' => $Cuenta2->balance + $request->input('balance')]);
                    Evento::create(
                        ["origen" => $Cuenta->id, "destino" => $Cuenta2->id, "tipo" => $request->input('tipo'), "balance" => $request->input('balance')]
                    );
                    return response()->json([Cuenta::find($request->input('origen')), Cuenta::find($request->input('destino'))], 200);
                } else {
                    return response()->json("Algunas de las cuentas no existe o el monto es insuficiente", 404);
                }
                break;
            case 'multiplica':
                $Cuenta = Cuenta::find($request->input('origen'));
                if ($Cuenta) {
                    Cuenta::where('id', $Cuenta->id)
                        ->update(['balance' => $Cuenta->balance * $request->input('balance')]);
                    Evento::create(
                        ["origen" => $Cuenta->id, "destino" => $Cuenta->id, "tipo" => $request->input('tipo'), "balance" => $request->input('balance')]
                    );
                    return response()->json(Cuenta::find($request->input('origen')), 200);
                } else {
                    return response()->json("La cuenta no existe", 404);
                }
                break;
        }
    }
}
