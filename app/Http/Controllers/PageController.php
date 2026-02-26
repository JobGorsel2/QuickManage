<?php

namespace App\Http\Controllers;
use App\Models\WorkspaceParameter;
use Illuminate\Support\Str;
use App\Models\Page;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
 
use Hashids\Hashids;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::get();
        // $var = 'TEST'; 
        // dd($pages);
        
        return view('pages.pages.index',[
            'pages' => $pages,
            
        ]);
    }

    public function create()
    {
        // $obj = "1";
        // $id = Hashids::encode($obj);
        // $hashids = new Hashids();       
        // dd($numb2);
        // $code = $date+" "+$text; 
        // $time = date("m-d-Y H:i:s:");
        // $id = $hashids->encode(23); // o2fXhV
        // $numbers = $hashids->decode($id); // [1, 2, 3]
        // $pages = Page::get();
        // $var = 'TEST'; 

         $templates = Template::get();

        return view('pages.pages.create',[
            'templates' => $templates,
        ]);
    }

    public function store(Request $request) 
    {

        $allData = $request->all();
         
    // Separate parameter_* fields
        $unique = base64_encode(date("m-d-Y H:i:s:"));
        $unique .= base64_encode("DKkda#dkvmd)lda_d-e0mwmas01#9dmxMWdiaPWOQ0n3");
        $numb = rand(1000000000,1000000000000);
        $numb2 = base64_encode($numb);
        $unique .=$numb2;
        
        $page = new Page();
        $page->name = $request['name'];
        $page->description = $request['description'];
        $page->template_id = $request['template'];
        $page->repository = $request['repo'];
        $page->workspace = $request['workspace'];
        $page->service = $request['service'];
        $page->folder_id = session('folder_id');
        $page->hash_id = $unique;
        $page->save();


        $parameterData = [];
        $pageData = [];
                    
        foreach ($allData as $key => $value) {
            if (str_starts_with($key, 'parameter_')) {
                $parameterData[$key] = $value;
                  
            } elseif (!in_array($key, ['_token', 'submit'])) {
                $pageData[$key] = $value;
            }
        }
        $parameterDataCleaned = [];
        foreach ($parameterData as $key => $value) {
            $cleanKey = Str::after($key, 'parameter_'); // or use substr($key, 10)
            $parameterDataCleaned[$cleanKey] = $value;
            [$parameterName, $fieldType] = explode(',', $cleanKey);
            // $parameter->field_type = $fieldType;

            // dd($parameterName);

            $parameter = new WorkspaceParameter();
            $parameter->parameter_name = $parameterName;
            $parameter->field_type = $fieldType;
            $parameter->page_id = $page->id;
            $parameter->save();
        }
        // foreach ($parameterDataCleaned as $key => $value) {
        //     $parameter = new WorkspaceParameter();
        //     $parameter->parameter_name = $key;
        //     $parameter->page_id = $page->id;
        //     $parameter->save();
        // }
        // $author = Auth::user()->name;

        // $page = new Page;
        // $page->name = $request->input('name');
        
        // $page->hash_id = $unique;
        
        // $page->save();
        // //redirect
        return back()->with('success','Pagina aangemaakt!');
    }

    public function show($unique) 
    {
        $data = Page::with('template')
        ->where('hash_id', $unique)
        ->first();

        // $DBparameters = WorkspaceParameter::where('page_id', $data->id)->get();

        // dd($DBparameters);
         
        // dd($data);   

        //redirect
        return view('html_templates.GKB_Realisatie_Style', [
                 'data'=> $data,
                 
                //  'DBparameters'=> $DBparameters
        ]);
    }

    public function edit($id) 
    {
        $page = Page::with('template','folder')
        ->where('id', $id)
        ->first();

        // dd($page);

        //redirect
        return view('pages.pages.edit', [
            'page' => $page,       
        ]);
    }

    public function update(Request $req, $id) 
    {
        // dd($req->input());
        $page = Page::find($id);
        $page->name = $req->input('name');
        $page->description = $req->input('description');
        $page->save();
        //redirect
        return back()->with('success','Pagina bijgewerkt!');
    }

    public function destroy($id)
    {
        // $id = Hash::make('1');
         
        $page = Page::where('id',$id)->delete();
       
        // $pages = Page::get();
        // $var = 'TEST'; 
        // dd($pages);
        return back()->with('success','Pagina verwijderd!');
    }
}
