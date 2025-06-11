<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use App\Models\Company; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;

class ProductController extends Controller 
{
    
    public function index(Request $request)
{
    $query = Product::query();

    if ($product_name = $request->product_name) {
        $query->where('product_name', 'LIKE', '%' . $product_name . '%');
    }

    if ($company_id = $request->company_id) {
        $query->where('company_id', $company_id);
    }

    if ($request->filled('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }

    if ($request->filled('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    if ($request->filled('min_stock')) {
        $query->where('stock', '>=', $request->min_stock);
    }

    if ($request->filled('max_stock')) {
        $query->where('stock', '<=', $request->max_stock);
    }

    $product = new Product();
    $products = $product->conditionSearch($query);

    $company = new Company();
    $companies = $company->searchAll();

    return view('products.index', compact('products','companies'));
}

    public function create()
    {
        $company = new Company();
        $companies = $company->searchAll();

        return view('products.create', compact('companies'));
    }

    public function store(Request $request) 
    {
        $request->validate([
            'product_name' => 'required', 
            'company_id' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'comment' => 'nullable', 
            'img_path' => 'nullable|image|max:2048',
        ]);
        


        $product = new Product([
            'product_name' => $request->get('product_name'),
            'company_id' => $request->get('company_id'),
            'price' => $request->get('price'),
            'stock' => $request->get('stock'),
            'comment' => $request->get('comment'),
        ]);



        if($request->hasFile('img_path')){ 
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }

        $products = new Product();
        try {
            DB::beginTransaction();
            $products->newCreate($product);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

        }

        return redirect('products');
    }

    public function show(Product $product)
    {

        $query = Company::query();

        $query->where('id', $product->company_id);

        $company = new Company();

        $companies = $company->conditionSearch($query);

        $product->company = $companies;

        return view('products.show', ['product' => $product]);
    }

    public function edit(Product $product)
    {
        $company = new Company();
        $companies = $company->searchAll();

        return view('products.edit', compact('product', 'companies'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_name' => 'required',
            'company_id' => 'required',
            'price' => 'required',
            'stock' => 'required',
        ]);

        $product->product_name = $request->product_name;
        $product->company_id = $request->company_id;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->comment = $request->comment;
        if ($request->hasFile('img_path')) {
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }

        $products = new Product();
        try {
            DB::beginTransaction();
            $products->targetUpdate($product);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

        }

        return redirect()->route('products.index')
            ->with('success',config('message.Update'));
    }

  public function destroy($id)
    {
    // 指定されたIDに対応する商品を取得
    $product = Product::findOrFail($id);

    // 商品を削除
    $product->delete();

    // JSONレスポンスを返す
    return response()->json(['success' => '商品を削除しました。']);
    }

    public function search(Request $request)
    {
        $query = Product::select('products.*', 'company_name')
            ->join('companies', 'companies.id', '=', 'products.company_id');
    
        if (isset($request->keyword)) {
            $query->where('product_name', 'like', '%' . $request->keyword . '%');
        }
    
        if (isset($request->company_id)) {
            $query->where('company_id', $request->company_id);
        }
    
        if (isset($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
    
        if (isset($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }
    
        if (isset($request->min_stock)) {
            $query->where('stock', '>=', $request->min_stock);
        }
    
        if (isset($request->max_stock)) {
            $query->where('stock', '<=', $request->max_stock);
        }
    
        $products = $query->orderBy('id', 'asc')->get();

        // ★ img_path を asset() で URL に変換する処理を追加
        foreach ($products as $product) {
            if (!empty($product->img_path)) {
                $product->img_path = asset($product->img_path);
            }
        }
    
        return response()->json(['products' => $products]);
    }
}    