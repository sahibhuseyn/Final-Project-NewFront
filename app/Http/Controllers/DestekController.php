<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Auth;
use App\Elan;
use App\Photo;
use Session;
class DestekController extends Controller
{
  public function show()
  {
    return view('pages.destek_add');
  }

   //<================= METHHOD FOR SAVING IMG WITH AJAX ================>

   public function only_pic(Request $req)
        {

          if ($req->ajax()) {
            $file_type = $req->file->getClientOriginalExtension();
            $lowered = strtolower($file_type);

            if($lowered=='jpg' || $lowered=='jpeg' || $lowered=='png'){
                $fileName = $req->file->getClientOriginalName();
                $file = $_FILES['file'];
                $istek_id = $_POST['istek_id'];
                $file['istek_id'] = $istek_id;
                $file_name =date('ygmis').'.'.$fileName;
                $req->file->move(public_path('image'), $file_name);
                $sekil = Elan::find($istek_id);
                $hamsi = $sekil->shekiller();
                $data = new Photo;
                $data->imageName = $file_name;
                $hamsi->save($data);
                return json_encode($file_name);
              }else{
                $file_name="error";
                return json_encode($file_name);
              }
          }
      }


    //<============ METHHOD FOR DELETING X PRESSED IMGS FROM EDITING=======>

        public function delete_edited_pics($pics) {
          if(!$pics) return false;
            foreach ($pics as $pic=>$status) {
              if(file_exists('image/'.$pic)){
                if($status == 0) {
                  unlink('image/'.$pic);
                  Photo::where('imageName', $pic)->delete();
                  echo "he";
                }
              }
            }
        }


  public function destek_add(Request $req)
  {

        $this->validate($req, [
          'title' => 'required',
          'about' => 'required',
          'location' => 'required',
          'lat' => 'required',
          'lng' => 'required',
          'image'=> 'required',
          'name' => 'required',
          'phone' => 'required',
          'email' => 'required',
          'nov' => 'required',
          'date' => 'required'
      ]);

     if($req->file('image')[0]==null){
       Session::flash('imageerror' , "Xahiş olunur şəkil seçin.");
          return back();
      }
  
     $files = $req->file('image');
     $pic_name = array();
     foreach ($files as $file) {
       $filetype=$file->getClientOriginalExtension();
       $lowered = strtolower($filetype); 

       if($lowered=='jpg' || $lowered=='jpeg' || $lowered=='png'){
          array_push($pic_name, $filetype);
       }
       else{
         Session::flash('imageerror' , "Xahiş olunur şəkili düzgun yükləyəsiniz.");
          return back();
       }
     }

    $data = [
          'type_id'=>'1',
          'title'=>$req->title,
          'about'=>$req->about,
          'location'=>$req->location,
          'lat'=>$req->lat,
          'lng'=>$req->lng,
          'name'=>$req->name,
          'phone'=>'+994'.$req->operator.$req->phone,
          'email'=>$req->email,
          'org'=>$req->org,
          'nov'=>$req->nov,
          'deadline'=>$req->date
        ];

        $insert_pic_id = Auth::user()->elanlar()->create($data)->shekiller();
          $files = $req->file('image');

          foreach ($files as $file) {
            $file_name =  date('ygmis').'.'.$file->getClientOriginalName();
            $file->move(public_path('image'),$file_name);
            $data = new Photo;
            $data->imageName = $file_name;
            $insert_pic_id->save($data);
          }
      Session::flash('destek_add' , "Dəstəyiniz uğurla  əlavə olundu və yoxlamadan keçəndən sonra dərc olunacaq.");
       return redirect('/destek-add');
  }

  public function destek_edit($id)
  {
    $destek_edit = Elan::find($id);
    return view('pages.destek_edit',compact('destek_edit'));
  }

  public function destek_update(Request $req,$id)
  {
    $this->validate($req, [
       'title' => 'required',
        'about' => 'required',
        'location' => 'required',
        'lat' => 'required',
        'lng' => 'required',
        'name' => 'required',
        'phone' => 'required',
        'email' => 'required',
        'nov' => 'required'
    ]);

    // $this->delete_edited_pics($req->input('picsArray'));

   Session::flash('destek_edited' , "Dəstəyiniz uğurla dəyişdirildi və yoxlamadan keçəndən sonra dərc olunacaq.");
   $destek_update = Elan::find($id);
   $destek_update->title = $req->title;
   $destek_update->location = $req->location;
   $destek_update->lat = $req->lat;
   $destek_update->lng = $req->lng;
   $destek_update->about = $req->about;
   $destek_update->name = $req->name;
   $destek_update->email = $req->email;
   $destek_update->org = $req->org;
   $destek_update->nov = $req->nov;
   $destek_update->deadline = $req->date;
   $destek_update->phone = $req->phone;
   $destek_update->status = 0;
   $destek_update->update();
   return redirect("/destek-edit/$destek_update->id");
  }


  //<================= METHHOD FOR ISTEK_EDIT ================>
   public function destek_delete($id)//updated
   {
     $destek_delete=Elan::find($id);
     $destek_delete->shekiller();
     foreach ($destek_delete->shekiller as $val) {
         unlink('image/'.$val->imageName);
     }
     $destek_delete->delete();
     return back();
   }
}
