<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Lucía es una gandula</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.4/semantic.min.css" rel="stylesheet" type="text/css">		
    </head>
    <body>
        <div class="ui top attached menu">
            @if (Route::has('login'))
            	<div class="right menu">
	                <div class="item right">
	                    <a href="{{ url('/login') }}">Login</a>
	                </div>
	                <div class="item right">
	                    <a href="{{ url('/register') }}">Register</a>
	                </div>
                </div>
       		  @endif         
        </div>
        <div class="ui grid">
        	<div class="five wide column">
	        	<div class="ui card fluid">
				  	<div class="content">
				    	<div class="header">Ficheros</div>
				 	</div>
	  				<div class="content">
					    {!! Form::open(array('url'=>'fichero/abrir','class'=>'ui form','method'=>'POST', 'files'=>true)) !!}
							<div class="field">
								<label>Seleccione el fichero que quiere analizar</label>
								{!! Form::file('fichero')!!}
								 @if ($errors->has('fichero')) <p class="help-block">{{ $errors->first('fichero') }}</p> @endif
								<div class="field">
		    						<label>Parametros</label>
		   							<div class="two fields">
			      						<div class="field">
			      							<span>Rango para calcular mínimo</span>	      							
			        						<input type="text" name="rangomin">
			      						</div>
			      						<div class="field">
			      							<span>Rango para calcular maximo</span>
			        						<input type="text" name="rangomax">
			      						</div>
			      						<div class="field">
			      							<span>Tolerancia para YF</span>
			        						<input type="text" name="toleranciay">
			      						</div>
		    						</div>
		  						</div> 
							</div>
							<center><button class="ui blue button" type="submit">Analizar</button></center>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
			<div class="eleven wide column">
				<div class="ui card fluid">
				  	<div class="content">
				    	<div class="header">Resultados</div>
				 	</div>
	  				<div class="content">	  					
						<table class="ui very basic collapsing celled table">
								 	<thead>
									    <tr>
									    	<th>Xmax</th>
									    	<th>XFWHMizq</th>
									    	<th>XFWHMDer</th>
									    	<th>FWHM</th>
									    	<th>XcentroFWHM</th>
									  	</tr>
								  	</thead>
								  	<tbody>
									<tr>
									@if(isset($XcandidatasPreMax) && isset($XcandidatasPostMax) && isset($Xmax) && isset($xab) && isset($XDXI))
									   	<td>
									   		{{$Xmax}}
										    
									   	</td>
									    <td>
									    	{{$XcandidatasPreMax}}
									    </td>
									    <td>
									    	{{$XcandidatasPostMax}}									    	
									    </td>
									    <td>
									    	{{$xab}}
									    </td>
									    <td>
									    	{{$XDXI}}
									    </td>
									@endif
		   							</tr>
	   						</tbody>
	   					</table>
					</div>
				</div>
			</div>			
		</div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.4/semantic.min.js" type="text/javascript"></script> 
    </body>
</html>
