<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use App\Models\Company; 
use Illuminate\Http\Request; 

class ProductController extends Controller 
{
    
    public function index(Request $request)
    {
        $query = Product::query();
        if($product_name = $request->product_name){
            $query->where('product_name', 'LIKE', '%' . $request->product_name . '%');
        }
        if($company_id = $request->company_id){ 
            $query->where('company_id', $request->company_id);
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
        $product->img_path = $request->img_path;

        $products = new Product();
        try {
            DB::beginTransaction();
            $products->targetUpdate($product);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

        }

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $products = new Product();
        try {
            DB::beginTransaction();
            $products->targetDelet($product);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        
        }

        return redirect('/products');
    }

}

