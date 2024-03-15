<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\PedidoProducto;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PedidoCollection;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new PedidoCollection(Pedido::with('user')->with('productos')->where('estado', 0)->get());
        //return response()->json(['data' => new PedidoCollection(Pedido::with('user')->with('productos')->where('estado', 0)->get())]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pedido = Pedido::create([
            'user_id' => Auth::user()->id,
            'total' => $request->total
        ]);

        $productos = $request->productos;

        $pedido_productos = [];

        foreach ($productos as $producto) {
            $pedido_productos[] = [
                'pedido_id' => $pedido->id,
                'producto_id' => $producto['id'],
                'cantidad' => $producto['cantidad'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            // $pedido->productos()->attach($producto['id'], ['cantidad' => $producto['cantidad']]);
        }

        PedidoProducto::insert($pedido_productos);

        return response([
            'message' => 'Pedido creado con exito, estará listo en unos minutos',
            'pedido' => $pedido
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    // public function show(Pedido $pedido)
    // {
    //     $pedido = Pedido::with('productos')->find($pedido->id);
    //     return response([
    //         'pedido' => $pedido
    //     ], 200);
    // }

    public function show($id)
    {
        $pedido = Pedido::with('productos')->find($id);

        if ($pedido) {
            return response([
                'pedido' => $pedido
            ], 200);
        } else {
            return response([
                'message' => 'Pedido no encontrado'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedido $pedido)
    {
        $pedido->update([
            'estado' => 1
        ]);

        return response([
            'message' => 'Pedido entregado con exito',
            'pedido' => $pedido
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedido $pedido)
    {
        $pedido->delete();
        return response([
            'message' => 'Pedido eliminado con exito'
        ], 200);
    }
}
