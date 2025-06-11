@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">商品一覧画面</h1>

    <div class="products mt-5">
        <form  id="search-form" method="GET" action="{{ route('products.index') }}" enctype="multipart/form-data">
            @csrf

                <label for="product_name">商品名:</label>
                <input id="product_name" type="text" name="keyword" value="{{ request('product_name') }}" >

                <label for="company_id" >メーカー</label>
                <select id="company_id" name="company_id">
                    <option value=""></option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                    @endforeach
                </select>
                <h3>絞り込み</h3>
                <div>
                     <label for="min_price">価格下限:</label>
                     <input type="number" class="form-input" placeholder="価格下限を入力してください" name="min_price" id="min_price" value="{{ request('min_price') }}">
                </div>
                <div>
                     <label for="max_price">価格上限:</label>
                    <input type="number" class="form-input" placeholder="価格上限を入力してください" name="max_price" id="max_price" value="{{ request('max_price') }}">
                </div>
                <div>
                    <label for="min_stock">在庫下限:</label>
                    <input type="number" class="form-input" placeholder="在庫下限を入力してください" name="min_stock" id="min_stock" value="{{ request('min_stock') }}">
                </div>
                <div>
                    <label for="max_stock">在庫上限:</label>
                    <input type="number" class="form-input" placeholder="在庫上限を入力してください" name="max_stock" id="max_stock" value="{{ request('max_stock') }}">
                </div>

            <button type="submit" class="btn btn-info btn-sm mx-1">検索</button>
        </form>
    </div>

    <div class="text-end mt-4 mb-3">
        <a href="{{ route('products.create') }}" class="btn btn-primary">新規登録</a>
    </div>

    <div id="search-results" class="mt-4" style="display: none;"></div>

    <div id="default-product-table" class="products mt-5">

        <table class="table table-striped tablesorter">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>在庫数</th>
                    <th>メーカー名</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td><img src="{{ asset($product->img_path) }}" alt="商品画像" width="100"></td>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->company->company_name }}</td>
                    
                    <td>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm mx-1">詳細</a>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm mx-1">編集</a>
                        <form method="POST" id="delete_{{ $product->id }}" action="{{ route('products.destroy', $product) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm mx-1 deletebutton" data-product-id="{{ $product->id }}">削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    
</div>
@endsection


