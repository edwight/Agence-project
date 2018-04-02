<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DesempenhoController extends Controller
{

	public function __construct(Request $request){
		$this->dateFrom = $request->input('dateFrom');
    	$this->dateTo = $request->input('dateTo');
    	$this->users = $request->input('users');
		$this->factura = Factura::join('cao_os AS os', 'cao_factura.co_os', '=', 'os.co_os')
                ->join('cao_usuario AS usuario', 'os.co_usuario', '=', 'usuario.co_usuario')
                ->leftJoin('cao_salario AS salario', 'os.co_usuario', '=', 'salario.co_usuario')
                ->whereIn('os.co_usuario', $this->users)
                ->whereBetween('cao_factura.data_emissao', [$this->dateFrom, $this->dateTo])
                ->select('usuario.no_usuario AS nombre',
                'salario.brut_salario AS custo_fixo',
                'usuario.co_usuario AS id',
                'cao_factura.data_emissao as periodo',                    
                DB::raw('SUM( cao_factura.valor - (cao_factura.valor * (cao_factura.total_imp_inc / 100)) ) AS receita_liquida'),
                DB::raw('SUM( (cao_factura.valor - (cao_factura.valor * (cao_factura.total_imp_inc / 100))) * (cao_factura.comissao_cn / 100) ) AS comissao')
                )
                ->groupBy('nombre', 'periodo', 'custo_fixo', 'id')
                ->get();

                //this->factura = $factura;
	}
    public function getRelatorio(Request $request){
    	return $request->all();
    	$result = $this->factura->groupBy('nombre');
    	//return response()->json($result);
    	foreach ($result as $key => $value) {
				$sumaReceitaLiquida =[];
				$sumaCustoFixo=[];
				$sumaComissao=[];
				$sumaLucro=[];
				foreach ($value as $datos) {
					$sumaReceitaLiquida[] += $datos->receita_liquida;
					$sumaCustoFixo[] += $datos->custo_fixo;
					$sumaComissao[] += $datos->comissao;
					$lucro = ($datos->receita_liquida - ($datos->custo_fixo + $datos->comissao));
					$sumaLucro[] += $lucro;
				}
				$factura[] = [
					'name'=>$key,
					'totalReceita'=>array_sum($sumaReceitaLiquida),
					'totalCustoFixo'=>array_sum($sumaCustoFixo),
					'totalComissao'=>array_sum($sumaComissao),
					'totalLucro'=>array_sum($sumaLucro),
					'value'=>$value
				];
				//print_r($nombre);
			}
    	return response()->json($factura);
    	//return view('relatorio',compact('factura','users'));
    
    }
    public function getBarchart(){
    	$params = $this->factura->groupBy('nombre');
			$categories = $this->factura->groupBy('periodo')->keys()->all();
			foreach ($params as $key => $value) {
				$receita_liquida = [];
				foreach ($value as $datos) {
					$receita_liquida[] += $datos->receita_liquida;
					//$custo_fixo_medio[] += $datos->custo_fixo;
				}
				$result[] = [
					'name'=>$key,
					'data'=>$receita_liquida
				];
				//print_r($nombre);
			}
			
			//https://laracasts.com/discuss/channels/laravel/return-two-dimensional-array?page=0
    		//$nombre = $this->factura->groupBy('nombre')->keys()->all();
        	return response()->json(['series'=>$result,'categories'=>$categories]);
    }
    public function getPiechart(){
    	$params = $this->factura->groupBy('nombre');
			foreach ($params as $key => $value) {
				$receita_liquida = [];
				foreach ($value as $datos) {
					$receita_liquida[] += $datos->receita_liquida;
					//$custo_fixo_medio[] += $dato$->custo_fixo;
				}
				$result[] = [
					'name'=>$key,
					'y'=>array_sum($receita_liquida)
				];
				//print_r($nombre);
			}
        	return response()->json(['series'=>$result]);
    }
    

}
