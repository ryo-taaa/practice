@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">商品一覧画面</h1>

    <div class="products mt-5">
        <form method="GET" action="{{ route('products.index') }}" enctype="multipart/form-data">
            @csrf

                <label for="product_name">商品名:</label>
                <input id="product_name" type="text" name="product_name" value="{{ request('product_name') }}" >

                <label for="company_id" >メーカー</label>
                <select id="company_id" name="company_id">
                    <option value=""></option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                    @endforeach
                </select>

            <button type="submit" class="btn btn-info btn-sm mx-1">検索</button>
        </form>
    </div>

    <div class="products mt-5">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>在庫数</th>
                    <th>メーカー名</th>
                    <th><a href="{{ route('products.create') }}" class="btn btn-primary mb-3">新規登録</a></th>
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
                    </td>
                    <td>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm mx-1">詳細</a>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm mx-1">編集</a>
                        <form method="POST" id="delete_{{ $product->id }}" action="{{ route('products.destroy', $product) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <a href="#" data-id="{{ $product->id }}" onclick="deletePost(this)" class="btn btn-danger btn-sm mx-1">削除</a>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    
</div>
@endsection


