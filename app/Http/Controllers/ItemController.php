<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Payjp\Charge;

class ItemController extends Controller
{
    public function showItems(Request $request)
    {
        $query = Item::query();

        // カテゴリで絞り込み
        if ($request->filled('category')) {
          $category = $request->input('category');
          $query->where('category_id', $category);
        }
        
        // キーワードで絞り込み
        if ($request->filled('keyword')) {
            $keyword = '%' . $this->escape($request->input('keyword')) . '%';
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'LIKE', $keyword);
                $query->orWhere('description', 'LIKE', $keyword);
            });
        }
        $items = $query->orderBy('id', 'DESC')->paginate(20);

        return view('items.items')
             ->with('items', $items);
    }

    private function escape(string $value)
    {
        return str_replace(
            ['\\', '%', '_'],
            ['\\\\', '\\%', '\\_'],
            $value
        );
    }

    public function showItemDetail(Item $item)
    {
        return view('items.item_detail')
            ->with('item', $item);
    }
    
    public function showSellForm()
    {
        $categories = Category::all();

        return view('sell')->with('categories', $categories);
    }

    public function sellItem(ItemRequest $request)
    {
        $user = Auth::user();

        $imageName = $this->saveImage($request->file('item-image'));

        $file_name = $request->file('file')->getClientOriginalName();
        $file_name = $user->name."_".$file_name;
        $request->file('file')->storeAs('public/contents',$file_name);

        $item                        = new Item();
        $item->image_file_name       = $imageName;
        $item->file             = $file_name;
        $item->seller_id             = $user->id;
        $item->title                  = $request->input('title');
        $item->description           = $request->input('description');
        $item->category_id = $request->input('category');
        $item->price                 = $request->input('price');

        $item->save();

        return redirect()->back()
            ->with('status', 'コンテンツを出品しました。');
    }

      /**
      * 商品画像をリサイズして保存します
      *
      * @param UploadedFile $file アップロードされた商品画像
      * @return string ファイル名
      */
      private function saveImage(UploadedFile $file): string
      {
          $tempPath = $this->makeTempPath();
  
          Image::make($file)->fit(300, 300)->save($tempPath);
  
          $filePath = Storage::disk('public')
              ->putFile('item-images', new File($tempPath));
  
          return basename($filePath);
      }
  
      /**
       * 一時的なファイルを生成してパスを返します。
       *
       * @return string ファイルパス
       */
      private function makeTempPath(): string
      {
          $tmp_fp = tmpfile();
          $meta   = stream_get_meta_data($tmp_fp);
          return $meta["uri"];
      }

      public function showBuyItemForm(Item $item)
      {
          return view('items.item_buy_form')
              ->with('item', $item);
      }

      public function buyItem(Request $request, Item $item)
      {
          $user = Auth::user();
  
          $token = $request->input('card-token');
  
          try {
              $this->settlement($item->id, $item->seller->id, $user->id, $token);
          } catch (\Exception $e) {
              Log::error($e);
              return redirect()->back()
                  ->with('type', 'danger')
                  ->with('message', '購入処理が失敗しました。');
          }
  
          return view('items.item_download')
              ->with('item', $item);
      }

      private function settlement($itemID, $sellerID, $buyerID, $token)
     {
         DB::beginTransaction();
 
         try {
             $item   = Item::lockForUpdate()->find($itemID);
             
             $tax = Tax::find(1);
             $tax_rate = $tax->tax_rate;
             $sales = $item->price + $item->price * $tax_rate;

             // transactionテーブル登録処理
             $item->transactions()->attach($buyerID, ['sales' => $sales]);

             $charge = Charge::create([
                'card'     => $token,
                'amount'   => $sales,
                'currency' => 'jpy'
            ]);
            if (!$charge->captured) {
                throw new \Exception('支払い確定失敗');
            }
 
         } catch (\Exception $e) {
             DB::rollBack();
             throw $e;
         }
 
         DB::commit();
     }

     public function itemDownloadForm(Item $item)
      {
          return view('items.item_download')->with('item', $item);
      }

    public function itemDownLoad(Item $item) {
        $fileName = $item->file;
        $filePath = "public/contents/$fileName";

        $exe = substr($fileName, -5);
        
        if($exe === '.pptx') {
            $headers = [['Content-Type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation']];
        } else if($exe === '.xlsx') {
            $headers = [['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']];
        }else if($exe === '.docx') {
            $headers = [['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']];
        }else {

        }

        return Storage::download($filePath, $fileName, $headers ?? '');   
    }
}
