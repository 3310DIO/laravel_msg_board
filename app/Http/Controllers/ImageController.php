<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * 儲存圖片資料
     */
    public function store(Request $request)
    {
        $img_data = $request->file('my_img');
        $user_account = session()->get('account');
        $img_name = $img_data->getClientOriginalName();
        $img_size = $img_data->getSize();
        // $img_tmp_name = $img_data->getPathname();
        $img_error = $img_data->getError();
        if(!($request->hasFile('my_img'))){
            $message = '非法操作';
            return redirect()->route('member.show', $user_account)->with('error', $message);
        }else{
            if(!($img_error === 0)){ // 判斷是否有錯誤訊息
                $message = '未知的錯誤';
                return redirect()->route('member.show', $user_account)->with('error', $message);
            }else{
                if($img_size > 10485760){ // 判斷大小是否超過10MB
                    $message = '上傳影像不能超過10MB';
                    return redirect()->route('member.show', $user_account)->with('error', $message);
                }else{
                    $img_ex = pathinfo($img_name, PATHINFO_EXTENSION); // 只保留副檔名
                    $img_ex_lc = strtolower($img_ex); // 轉成小寫

                    $allow_img = array("jpg", "jpeg", "png"); // 允許的圖片格式
                    if(!(in_array($img_ex_lc, $allow_img))){ // 判斷格式是否符合預設條件
                        $message = '僅支援jpg、jpeg、png格式';
                        return redirect()->route('member.show', $user_account)->with('error', $message);
                    }else{
                        $new_img_name = uniqid("IMG-", true).'.'.$img_ex_lc; // 產生一個隨機名稱加上原本的副檔名
                        $img_upload_path = $request->getSchemeAndHttpHost() . '/upload/img/'.$new_img_name; 
                        $img_data->move(public_path('/storage/upload/img/'), $img_upload_path); // 將上傳檔案複製進指定資料夾

                        $new_img = new Image();
                        $new_img->user_account = $user_account;
                        $new_img->img_url = $new_img_name;
                        // $new_img->width_height = $w_h;
                        // dd($msg);
                        $new_img->save();
                        $message = '上傳成功';
                        return redirect()->route('member.show', $user_account)->with('message', $message);
                    }
                }
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * 更新圖片資料
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'img_content' => 'required|max:200',
        ],[
            'img_content.required' => '請輸入圖片內容',
            'img_content.max' => '圖片內容須在200字以內',
        ]);
        $id_img = $request->input('img_id');
        $user_account = session()->get('account');
        $content = $request->input('img_content');
        if($content==''){ // 判斷是否有輸入值輸入，沒有則返回首頁
            $message = "非法操作";
            return redirect()->route('msg.index')->with('error', $message);
        }else{
            $img_data = Image::find($id_img);
            // dd($id_img);
            if($img_data == null || $user_account != $img_data->user_account){ // 判斷是否是發佈者進行的修改
                $message = "非法操作";
                return redirect()->route('msg.index')->with('error', $message);
            }else{
                $img_data->img_content = $content;
                $img_data->save();
                $message = "修改成功";
                return redirect()->route('member.show', $user_account)->with('message', $message);
            }
        }
    }

    /**
     * 移除圖片
     */
    public function destroy(Request $request, string $id)
    {
        // dd("測試");
        $user_account = session()->get('account');
        $img_data = Image::find($id);
        if($img_data == null || $user_account != $img_data->user_account){ // 判斷是否是發佈者進行的修改
            $message = "非法操作";
            return redirect()->route('msg.index')->with('error', $message);
        }else{
            $new_img_name = $img_data->img_url;
            $img_upload_path = 'public/upload/img/'.$new_img_name;
            $img_del_path = 'public/upload/del_img/'.$new_img_name;
            // dd(URL($img_upload_path),URL($img_del_path));
            Storage::move($img_upload_path, $img_del_path); // 將上傳檔案複製進指定資料夾
            $img_data->is_del = 1;
            $img_data->save();
            $message = "刪除成功";

            return redirect()->route('member.show', $user_account)->with('message', $message);
        }
    }
}
