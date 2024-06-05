<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use App\Models\CategoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function items()
    {
        // Itemテーブルから画像と価格だけ取得
        $items = Item::select('id', 'image_url', 'price')->get();

        //　items.itemビューを表示し、$items変数を渡す
        return view('items.item', ['items' => $items]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $items = Item::where('name', 'like', '%' . $query . '%')->get();

        return view('items.search_results', ['items' => $items]);
    }

    public function show($item_id)
    {
        // IDでアイテムを取得
        $item = Item::findOrFail($item_id);
        // IDでアイテムを出品したユーザーのnameを取得する
        $user = User::findOrFail($item->user_id)->name;

        // items.detailビューを表示し、$item変数を渡す
        return view('items.detail', ['item' => $item, 'user' => $user]);
    }

    public function showCreateForm()
    {
        $conditions = Condition::all();
        $categories = Category::all();

        return view('items.create', compact('conditions', 'categories'));
    }

    public function create(Request $request)
    {
        //　フォームからの入力を取得
        $input = $request->all();

        //　ユーザーIDを取得
        $userId = Auth::id();

        //　コンディション名からコンディションレコードを取得
        $condition = Condition::where('condition', $input['condition'])->first();
        $category = Category::where('category', $input['category'])->first();

        //　画像ファイルの処理
        if ($request->hasFile('image_url')) {
            $imagePath = $request->file('image_url')->store('images', 'public');
        } else {
            $imagePath = null;
        }

        //　アイテムを作成
        $item = new Item([
            'user_id' => $userId,
            'name' => $input['name'],
            'price' => $input['price'],
            'comment' => $input['comment'],
            'image_url' => $imagePath,
            'brand' => $input['brand'],
            'condition_id' => $condition->id,
        ]);

        //　アイテムを保存
        $item->save();

        // 中間テーブルの　category_items　テーブルに新しいデータを追加
        CategoryItem::create([
            'item_id' => $item->id,
            'category_id' => $category->id,
        ]);

        //　成功した場合はリダイレクトなどを行う
        return redirect()->route('items.create')->with('success', 'アイテムが追加されました');
    }

    //　出品商品一覧ページを処理するメソッド
    public function selling()
    {
        $user = Auth::user();
        $items = Item::where('user_id', $user->id)->get();

        return view('items.selling', ['items' => $items]);
    }
    //　購入商品一覧ページを処理するメソッド
    public function purchased()
    {
        $user = Auth::user();
        $items = Item::where('sold_user_id', $user->id)->get();

        return view('items.purchased', ['items' => $items]);
    }
}
