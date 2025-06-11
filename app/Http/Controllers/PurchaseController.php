<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function purchase(Request $request)
    {
        $statusCode = 200;
        $result = [];

        try {
            // 商品特定
            $product = Product::find($request->id);

            if (!$product) {
                // 商品が見つからない場合のエラーハンドリング
                $statusCode = 404;
                $result['error'] = '商品が見つかりません。';
                return response()->json($result, $statusCode, ['Content-Type' => 'application/json'], JSON_UNESCAPED_SLASHES);
            }

            // 在庫判定
            if ($product->stock < 1) {
                // 在庫が不足している場合のエラーハンドリング
                $statusCode = 400;
                $result['error'] = '在庫が不足しています。';
                return response()->json($result, $statusCode, ['Content-Type' => 'application/json'], JSON_UNESCAPED_SLASHES);
            }

            // トランザクション開始
            DB::beginTransaction();

            // salesテーブルにレコードを追加する（在庫がある場合）
            Sale::create([
                'product_id' => $product->id,
            ]);

            // productsテーブルの在庫数を減算する（在庫がある場合）
            $product->stock--; 
            $product->save();

            // トランザクションをコミット
            DB::commit();

            $result['message'] = '購入が完了しました。';
        } catch (\Exception $e) {
            // トランザクションをロールバック
            DB::rollBack();
            $statusCode = 500;
            $result['error'] = '購入処理中にエラーが発生しました。';
            $result['error'] = $e->getMessage();
        }

        return response()->json($result, $statusCode, ['Content-Type' => 'application/json'], JSON_UNESCAPED_SLASHES);
    }
}