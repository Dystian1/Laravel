<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Book;   
use Illuminate\Support\Facades\Validator;
class BookController extends Controller
{
    public function index()
    {
        $data = Book::all();
        return response($data);
    }
    public function getAll($limit = 10, $offset = 0){
        $data["count"] = Book::count();
        $book = array();
        foreach (Book::take($limit)->skip($offset)->get() as $p) {
            $item = [
                "id"          => $p->id,
                "title"       => $p->title,
                "description" => $p->description,
                "warna"       => $p->warna,
                "stok"        => $p->stok,
                "dipinjam"    => $p->dipinjam,
                "created_at"  => $p->created_at,
                "updated_at"  => $p->updated_at,
            ];
            
            array_push($book, $item);
        }
        $data['sum'] = Book::sum('stok');
        $data['sumdipinjam'] = Book::sum('dipinjam');
        $data["book"] = $book;
        $data["status"] = 1;
        return response($data);
    }
    public function find(Request $request, $limit = 10, $offset = 0)
    {
        $find = $request->find;
        $book = Book::where("id","like","%$find%")
        ->orWhere("title","like","%$find%")
        ->orWhere("warna","like","%$find%")
        ->orWhere("description","like","%$find%");
        $data["count"] = $book->count();
        $books = array();
        foreach ($book->skip($offset)->take($limit)->get() as $p) {
          $item = [
            "id" => $p->id,
            "title" => $p->title,
            "description" => $p->description,
            "warna" => $p->warna,
            "stok" => $p->stok,
            "created_at" => $p->created_at,
            "updated_at" => $p->updated_at
          ];
          array_push($books,$item);
        }
        $data["book"] = $books;
        $data["status"] = 1;
        return response($data);
    }
    public function register(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'title' => 'required|string|max:255',
			'description' => 'required|max:255',
			'warna' => 'required|max:255',
			'stok' => 'required|integer',
		]);
		if($validator->fails()){
			return response()->json([
				'status'	=> 0,
				'message'	=> $validator->errors()->toJson()
			]);
		}
		$book = new Book();
		$book->title 	        = $request->title;
		$book->warna 	= $request->warna;
		$book->description 	= $request->description;
        $book->stok            = $request->stok;
		$book->dipinjam         = 0;        
		$book->save();
		return response()->json([
			'status'	=> '1',
			'message'	=> 'Buku berhasil terregistrasi'
			//'user'		=> $user,
		], 201);
	}
    public function store(Request $request)
    {
        try {
            $data = new Book();
            $data->title = $request->input('title');
            $data->warna = $request->input('warna');
            $data->description = $request->input('description');
            $data->stok = $request->input('stok');
            $data->dipinjam = $request->input('dipinjam');
            $data->save();
            return response()->json([
                'status' => '1',
                'message' => 'Tambah data buku berhasil!',
            ]);
        } catch(\Exception $e){
            return response()->json([
                'status' => '0',
                'message' => $e->getMessage()
            ]);
        }
    }
    public function ubah(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
			'warna' => 'required|max:255',
			'description' => 'required|max:255',
			'warna' => 'required|max:255',
            'stok' => 'required|integer',
		]);
		if($validator->fails()){
			return response()->json([
				'status'	=> '0',
				'message'	=> $validator->errors()
			]);
		}
		//proses update data
		$book = Book::where('id', $request->id)->first();
		$book->title 	        = $request->title;
		$book->warna 	    = $request->warna;
		$book->description 	    = $request->description;
        $book->stok            = $request->stok; 
		$book->save();
		return response()->json([
			'status'	=> '1',
			'message'	=> 'Buku berhasil diubah'
		]);
	}
    public function update(Request $request, $id)
    {
        try {
            $data = Book::where('id', $id)->first();
            $data->title = $request->input('title');
            $data->warna = $request->input('warna');
            $data->description = $request->input('description');
            $data->stok = $request->input('stok');
            $data->save();
            return response()->json([
                'status' => '1',
                'message' => 'Ubah data buku berhasil!',
            ]);
        } catch(\Exception $e){
            return response()->json([
                'status' => '0',
                'message' => $e->getMessage()
            ]);
        }
    }
    public function destroy($id)
    {
        try {
            $data = Book::where('id', $id)->first();
            $data->delete();
            return response()->json([
                'status' => '1',
                'message' => 'Hapus data buku berhasil!',
            ]);
        } catch(\Exception $e){
            return response()->json([
                'status' => '0',
                'message' => $e->getMessage()
            ]);
        }
    }
}