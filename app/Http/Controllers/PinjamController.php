<?php
namespace App\Http\Controllers;
use App\Pinjam;   
use App\Book;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
class PinjamController extends Controller
{
    public function getAll($limit = 10, $offset = 0){
        $data["count"] = Pinjam::count();
        $pinjam = array();
        foreach (Pinjam::take($limit)->skip($offset)->get() as $p) {
            $item = [
                "id"           => $p->id,
                "idpeminjam"   => $p->idpeminjam,
                "idbuku"       => $p->idbuku,
                "name"         => $p->name,
                "jumlah_pinjam"=> $p->jumlah_pinjam,
                "tanggal_pinjam"=> $p->tanggal_pinjam,
                "tanggal_kembali"=> $p->tanggal_kembali,
                "denda"        =>$p->denda,
                "status"       =>$p->status,
                "created_at"   => $p->created_at,
                "updated_at"   => $p->updated_at,
            ];
            
            array_push($pinjam, $item);
        }
        $data["pinjam"] = $pinjam;
        $data["status"] = 1;
        return response($data);
    }
    public function index(Request $request, $id)
    {
        $userid                 = $request->peminjam; 
        $user                   = User::where('id', $userid)->first();
        $nameuser               = $user->name;
        $book                   = Book::where('id', $id)->first();
        $terpinjam              = $book->stok - $request->jumlah_pinjam;
        $book->dipinjam         = $book->dipinjam + $request->jumlah_pinjam;
            if($book->stok > $request->jumlah_pinjam ){ 
                $book->stok = $book->stok - $request->jumlah_pinjam;
                $book->save();
                $pinjam = new Pinjam();
                $pinjam->idpeminjam 	    = $userid;
                $pinjam->idbuku      	    = $request->id;
                $pinjam->name 	            = $nameuser;
                $pinjam->jumlah_pinjam      = $request->jumlah_pinjam;
                $pinjam->tanggal_pinjam     = date("y/m/d");
                $pinjam->tanggal_kembali    = null;
                $pinjam->denda              = 0; 
                $pinjam->status             = "Dipinjam";
                $pinjam->save();
                
                return response()->json([
                    'status'	=> '1',
                    'message'	=> 'Buku berhasil dipinjam'
                ], 201);
            }
            else{
                return response()->json([
                    'status'	=> '0',
                    'message'	=> 'Stok tidak cukup'
                ], 201);
            }
        }
    public function kembali(Request $request)
    {
            $pinjam = Pinjam::where('id', $request->id)->first();
            if($pinjam->status == "Dipinjam"){
                $book   = Book::where('id', $pinjam->idbuku)->first();
                $pinjam->status = "Kembali";
                $pinjam->tanggal_kembali = date("y/m/d");
                $book->stok = $book->stok + $pinjam->jumlah_pinjam;
                $book->dipinjam = $book->dipinjam - $pinjam->jumlah_pinjam;
                $book->save();
                $pinjam->save();
                return response()->json([
                    'status'	=> '1',
                    'message'	=> 'Buku berhasil dikembalikan'
                ], 201);
                } else {
                    return response()->json([
                        'status'	=> '0',
                        'message'	=> 'Buku Sudah Dikembalikan'
                    ], 201);
                }
            }
        }