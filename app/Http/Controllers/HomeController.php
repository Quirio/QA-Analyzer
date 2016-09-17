<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use View;

class HomeController extends Controller
{

    private $tabla;
    private $valoresX;
    private $valoresY;
    private $mediaY;
    private $maxY;
    private $mediaMaxY;   

    private $FWHM;
    private $error;
    private $rangomin;
    private $rangomax;
    private $Ycandidatas;
    private $XcandidatasPreMax;
    private $XcandidatasPostMax;
    private $XMax;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    private function readFile($content){
        $tabla = [];
        $valoresX = [];
        $valoresY = [];
        $filas = preg_split("/\n/", $content);
        array_pop($filas);

        foreach ($filas as $valores) {
            $tabla[] = preg_split("/\t/", $valores);
            $valoresX[] = floatval (preg_split("/\t/", $valores)[0]);
            $valoresY[] = floatval (preg_split("/\t/", $valores)[1]);
        }

        array_pop($tabla);
        $this->tabla=$tabla;
        $this->valoresX = $valoresX;
        $this->valoresY = $valoresY;
    }

    private function calcularMmediaY($nValores){
        $subArray = array_slice($this->valoresY, 0, $nValores);
        $SUM = array_sum($subArray);        
        $this->mediaY = $SUM/$nValores;
        //app('debugbar')->info($this->mediaY );
    }

    private function calcularMaxY(){
        $this->maxY = max($this->valoresY);
        //app('debugbar')->info($this->maxY);      
    }

    private function calcularMediaMaximoY($nValores){
        $posInArray = array_search($this->maxY,$this->valoresY);
       // app('debugbar')->info($posInArray); 
        $inicial = $posInArray - $nValores;
        //app('debugbar')->info( $inicial); 
        $nDatos = ($posInArray + $nValores)-$inicial;
     //   app('debugbar')->info($nDatos);
        $subArray =  array_slice($this->valoresY, $inicial,$nDatos);
      //  app('debugbar')->info($subArray);
        $SUM = array_sum($subArray);
      //  app('debugbar')->info($SUM);
        $this->mediaMaxY = $SUM / ($nValores*2);
    //    app('debugbar')->info( $this->mediaMaxY); 
    }

    private function calcularFWHM(){
        $this->FWHM = ($this->mediaMaxY + $this->mediaY)/2;
      //  app('debugbar')->info($this->FWHM);      
    }

    private function calcularLimitesFWHM(){
        $Ycandidatas = [];         
        foreach ($this->valoresY as $data) {                
            if(abs($this->FWHM-$data) <= $this->error){
               // app('debugbar')->info($data);    
                  $Ycandidatas[] =  $data;      
            }
        }

        $this->Ycandidatas = $Ycandidatas;
          
        //app('debugbar')->info(abs($data-$this->FWHM)."<".$this->error);
    }

    private function findXCandidatas(){
        $XcandidatasPreMax = [];
        $XcandidatasPostMax = [];

        foreach ($this->Ycandidatas as $data) {
            $index = array_search($data,$this->valoresY);
           // app('debugbar')->info($data . '<' . $this->maxY);     
            if($this->valoresX[$index] < $this->XMax)              
                $XcandidatasPreMax[] = $this->valoresX[$index];
            else
                $XcandidatasPostMax[] = $this->valoresX[$index];
            
        } 
        $this->XcandidatasPreMax = $XcandidatasPreMax;
        $this->XcandidatasPostMax = $XcandidatasPostMax;

        //app('debugbar')->info($this->XcandidatasPostMax);     
    }

    private function findXMax(){
        $this->XMax = $this->valoresX[array_search($this->maxY,$this->valoresY)];
    }

    private function mediaXcandidatas(){
        if(count($this->XcandidatasPreMax) != 1){
            $SUM = array_sum($this->XcandidatasPreMax);
            $this->XcandidatasPreMax = $SUM / count($this->XcandidatasPreMax);
        }

        else{
            $this->XcandidatasPreMax = $this->XcandidatasPreMax[0];
        }

        if(count($this->XcandidatasPostMax) != 1){
            $SUM = array_sum($this->XcandidatasPostMax);
            $this->XcandidatasPostMax = $SUM / count($this->XcandidatasPostMax);
        }

        else{
           $this->XcandidatasPostMax = $this->XcandidatasPostMax[0]; 
        }

        //app('debugbar')->info($this->XcandidatasPostMax."/".$this->XcandidatasPreMax);
    }

    public function abrirFichero()
    {
        $validator = validator::make(Input::all(), [
            'fichero' => 'required',
            'rangomin' => 'required',
            'rangomax' => 'required',
            'toleranciay' => 'required'
        ]); 
       
        if ($validator->fails())
        {
            $messages = $validator->messages();
            return Redirect::to('/')       
                ->withErrors($validator);
        }else {

            $this->error = floatval(Request::input('toleranciay'));
            $this->rangomax = floatval(Request::input('rangomax'));
            $this->rangomin =  floatval(Request::input('rangomin'));
            $content = file_get_contents(Input::file('fichero')->getRealPath());

            $this->readFile($content);
            $this->calcularMmediaY( floatval ($this->rangomin) );
            $this->calcularMaxY();
            $this->calcularMediaMaximoY( floatval ($this->rangomax) );
            $this->calcularFWHM();
            $this->calcularLimitesFWHM();
            $this->findXMax();
            $this->findXCandidatas();
            $this->mediaXcandidatas();

            app('debugbar')->info($this->FWHM);
            app('debugbar')->info($this->maxY);
            app('debugbar')->info($this->XMax);
            app('debugbar')->info($this->XcandidatasPreMax); 
            app('debugbar')->info($this->XcandidatasPostMax);

            return View::make('welcome')
                ->with('tabla', $this->tabla)
                ->with('XcandidatasPreMax', $this->XcandidatasPreMax)
                ->with('XcandidatasPostMax', $this->XcandidatasPostMax)
                ->with('Xmax',$this->XMax)
                ->with('xab',$this->XcandidatasPostMax-$this->XcandidatasPreMax)
                ->with('XDXI',(($this->XcandidatasPostMax-$this->XcandidatasPreMax)+$XcandidatasPreMax)/2);   
        }
    }
}
