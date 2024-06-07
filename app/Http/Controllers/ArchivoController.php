<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchivoController extends Controller
{
    
    public function upload(Request $request){
        $id= $request->id;
        $file = $request->file("file");
        $filename = time().'-'.$file->getClientOriginalName();
        //$request->file('file')->storeAs($id, $filename,'public');  //me permite cargar de forma publica los archivos y mostrar el enlace
       $request->file('file')->storeAs($id, $filename);    // cargar de forma PRIVADA
       // $file->move(public_path($id),$filename);



        $archivo = new Archivo();
        $archivo->carpeta_id = $request->id;
        $archivo->nombre = $filename;
        $archivo->estado_archivo = 'PRIVADO';
        $archivo->save();

      

        return redirect()->back()
           ->with('mensaje','Se cargo el archivo de la manera correcta')
           ->with('icono','success');

    
    }


        public function eliminar_archivo(Request $request){
           
            $id = $request->id;
            $archivo = Archivo::find($id);
            $estado_archivo = $archivo->estado_archivo;
            if($estado_archivo=='PRIVADO'){
                Storage::delete($archivo->carpeta_id.'/'.$archivo->nombre);
            }
            else{
                Storage::delete('public/'.$archivo->carpeta_id.'/'.$archivo->nombre);
            }
            Archivo::destroy($id);          
            return redirect()->back()
                ->with('mensaje', 'Se eliminó el archivo de la manera correcta')
                ->with('icono', 'success');
        }

        public function cambiar_de_privado_a_publico(Request $request){
            $id = $request->id;
            $estado_archivo = "PÚBLICO";

            $archivo = Archivo::find($id);
            $carpeta_id= $archivo->carpeta_id;
            $nombre= $archivo->nombre;


            $archivo->estado_archivo = $estado_archivo;
            $archivo->save();

            $ruta_archivo_privado = $carpeta_id.'/'.$nombre;

            $ruta_archivo_publico = 'public/'.$carpeta_id.'/'.$nombre;

            Storage::move($ruta_archivo_privado,$ruta_archivo_publico);

            return redirect()->back()
            ->with('mensaje', 'Se cambió el estado del Archivo')
            ->with('icono', 'success');

        }


         public function cambiar_de_publico_a_privado(Request $request){
            $id = $request->id;
            $estado_archivo ="PRIVADO";

            $archivo = Archivo::find($id);
            $carpeta_id= $archivo->carpeta_id;
            $nombre = $archivo->nombre;

            $archivo->estado_archivo = $estado_archivo;
            $archivo->save();   


            $ruta_archivo_privado = $carpeta_id.'/'.$nombre;

            $ruta_archivo_publico = 'public/'.$carpeta_id.'/'.$nombre;

            Storage::move($ruta_archivo_publico,$ruta_archivo_privado);

            return redirect()->back()
            ->with('mensaje', 'Se cambió el estado del Archivo')
            ->with('icono', 'success');


         }

   


}

