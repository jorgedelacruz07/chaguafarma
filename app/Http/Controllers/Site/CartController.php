<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Product;
use \Cart as Cart;

class CartController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index()
  {
    $articles = collect();
    $articles['items'] = Cart::content();
    $articles['subtotal'] = Cart::subtotal();
    $articles['total'] = Cart::total();
    return view('site.cart.index', compact('articles'));
  }

  /**
  * Show the form for creating a new resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function create()
  {
    //
  }

  /**
  * Store a newly created resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function store(Request $request)
  {
    $duplicates = Cart::search(function ($cartItem, $rowId) use ($request) {
      return $cartItem->id === $request['id'];
    });

    if (!$duplicates->isEmpty()) {
      return response()->json([
        'success' => false,
        'message' => 'Producto en el carrito'
      ]);
    }

    try {
      Cart::add([
        'id' => $request->id,
        'qty' => 1,
        'name' => $request->name,
        'price' => $request->price,
        'options' => [
          'slug' => $request->slug,
          'image' => $request->image,
        ]
      ])->associate('App\Product');
      return response()->json([
        'success' => true,
        'message' => 'Agregado correctamente'
      ]);
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'No se pudo agregar'
      ]);
    }


  }

  /**
  * Display the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function show($id)
  {
    //
  }

  /**
  * Show the form for editing the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function edit($id)
  {
    //
  }

  /**
  * Update the specified resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function update(Request $request, $id)
  {
    //
  }

  /**
  * Remove the specified resource from storage.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function destroy($id)
  {
    //
  }

  public function updateRowId(Request $request, $rowId)
  {
    try {
      Cart::update($rowId, $request['qty']);
      $articles = collect();
      $articles['items'] = Cart::content();
      $articles['subtotal'] = Cart::subtotal();
      $articles['total'] = Cart::total();
      return response()->json([
        'success' => true,
        'message' => 'Actualizado exitosamente',
        'articles' => $articles
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Fallo al actualizar'
      ]);
    }
  }

  public function deleteRowId(Request $request, $rowId)
  {
    try {
      Cart::remove($rowId);
      $articles = collect();
      $articles['items'] = Cart::content();
      $articles['subtotal'] = Cart::subtotal();
      $articles['total'] = Cart::total();
      return response()->json([
        'success' => true,
        'message' => 'Eliminado exitosamente',
        'articles' => $articles
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Fallo al eliminar'
      ]);
    }

  }
}
