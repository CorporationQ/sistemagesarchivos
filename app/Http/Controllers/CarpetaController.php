<?php

namespace App\Http\Controllers;

use App\Models\Carpeta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class CarpetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $id_user= Auth::user()->id;
        $carpetas = Carpeta::whereNull('carpeta_padre_id')
        
                              ->where('usuario_id',$id_user)
                              ->get(); 
        return view('admin.mi_almacenamiento.index',['carpetas'=>$carpetas]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:191',
        ]);

        $carpeta = new Carpeta();
        $carpeta->nombre = $request->nombre;
        $carpeta->usuario_id = $request->usuario_id;
        $carpeta->save();

        return redirect()->route('mi_almacenamiento.index')
            ->with('mensaje','Se registro la carpeta de la manera correcta')
            ->with('icono','success');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $carpeta=Carpeta::findOrFail($id);
        $subcarpetas=$carpeta->carpetasHijas;
        $archivos=$carpeta->archivos;
        return view('admin.mi_almacenamiento.show',compact('carpeta','subcarpetas','archivos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Debug: Verificar datos recibidos
        $datos = $request->all();
        \Log::info('Datos recibidos:', $datos);
        
        $request->validate([
            'nombre' => 'required|max:191',
        ]);
    
        // Debug: Verificar ID recibido
        \Log::info('ID recibido:', ['id' => $id]);
    
        $carpeta = Carpeta::find($id);
    
        // Debug: Verificar si la carpeta fue encontrada
        if (!$carpeta) {
            \Log::error('Carpeta no encontrada:', ['id' => $id]);
            return redirect()->route('mi_almacenamiento.index')
                ->with('mensaje', 'Carpeta no encontrada')
                ->with('icono', 'error');
        }
    
        $carpeta->nombre = $request->nombre;
        $carpeta->save();
    
        return redirect()->route('mi_almacenamiento.index')
            ->with('mensaje', 'Se cambió el nombre de la carpeta de manera correcta')
            ->with('icono', 'success');
    }

    public function update_color(Request $request)
    {
        
        $id = $request->id; 
        $carpeta = Carpeta::find($id);
        $carpeta->color = $request->color;
        $carpeta->save();

        return redirect()->back();

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Carpeta::destroy($id);     

        Storage::deleteDirectory($id);
        Storage::deleteDirectory('public/'.$id);
        return redirect()->back()
        ->with('mensaje','Se elimino la carpeta de la manera correcta')
        ->with('icono','success');
    }

    public function crear_subcarpeta(Request $request){
        $request->validate([
            'nombre' => 'required|max:191',
            'carpeta_padre_id' => 'required',
        ]);

        $carpeta = new Carpeta();
        $carpeta->nombre = $request->nombre;
        $carpeta->usuario_id = $request->user_id;
        $carpeta->carpeta_padre_id = $request->carpeta_padre_id;
        $carpeta->save();

        return redirect()->back()
            ->with('mensaje','Se registro la carpeta de la manera correcta')
            ->with('icono','success');

    }

    public function update_subcarpeta(Request $request){

        $request->validate([
            'nombre' => 'required|max:191',
        ]);

        $id = $request->id;
        $carpeta = Carpeta::find($id);
        $carpeta->nombre = $request->nombre;
        $carpeta->save();

        return redirect()->back()
            ->with('mensaje','Se actualizo la carpeta de la manera correcta')
            ->with('icono','success');

    }

    public function update_subcarpeta_color(Request $request){

        $id = $request->id; 
        $carpeta = Carpeta::find($id);
        $carpeta->color = $request->color;
        $carpeta->save();

        return redirect()->back();
    }
}
