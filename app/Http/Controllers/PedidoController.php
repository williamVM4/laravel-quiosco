<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Resources\PedidoCollection;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new PedidoCollection(Pedido::with('user')->with('productos')->where('estado', 0)->get());
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
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pedido $pedido)
    {
        //
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
        //
    }
}
