<?php namespace VaahCms\Modules\ordersystem\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class OrderProductsController extends Controller {

	/**
	 * Display a listing of the resource.
	 * @return Response
	 */
	public function index()
	{
        return view('index');
	}

	/**
	 * Show the form for creating a new resource.
	 * @return Response
	 */
	public function create(Request $request)
	{
        return view('create');
	}

	/**
	 * Store a newly created resource in storage.
	 * @return Response
	 */
	public function store(Request $request)
	{
		$order_id = $request->order_id;
		$products = $request->products;

		if(!$order_id || !is_array($products)){
			return response()->json([
				'success' => false,
				'message' => 'Order ID or products data missing.'
			], 400);
		}

		foreach($products as $prod){
			DB::table('order_product')->insert([
				'order_id' => $order_id,
				'product_id' => $prod['id'],
				'quantity' => $prod['quantity'],
				'created_at' => now(),
				'updated_at' => now(),
			]);
		}

		return response()->json([
			'success' => true,
			'message' => 'Order products saved successfully.'
		]);
	}

	/**
	 * Display the specified resource.
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Request $request, $id)
	{
		return view('show');
	}

	/**
	 * Show the form for editing the specified resource.
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Request $request, $id)
	{
        return view('edit');
	}

	/**
	 * Update the specified resource in storage.
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request)
	{
		$order_id = $request->order_id;
		$products = $request->products;

		if(!$order_id || !is_array($products)){
			return response()->json([
				'success' => false,
				'message' => 'Order ID or products data missing.'
			], 400);
		}

		// Delete old products for this order
		DB::table('order_product')->where('order_id', $order_id)->delete();

		// Insert new products
		foreach($products as $prod){
			DB::table('order_product')->insert([
				'order_id' => $order_id,
				'product_id' => $prod['id'],
				'quantity' => $prod['quantity'],
				'created_at' => now(),
				'updated_at' => now(),
			]);
		}

		return response()->json([
			'success' => true,
			'message' => 'Order products updated successfully.'
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request, $id)
	{
        return response()->json([]);
	}

}
