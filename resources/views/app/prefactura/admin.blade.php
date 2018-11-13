<!-- Content Header (Page header) -->
<style>
.tr_hover{
	color:red;
}
</style>
<section class="content-header">
	<h1>
		{{ $title }}
		{{-- <small>Descripción</small> --}}
	</h1>
</section>
<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<div class="row">
						<div class="col-xs-12">
							{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
							{!! Form::hidden('page', 1, array('id' => 'page')) !!}
							{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
							<div class="form-group">
								{!! Form::label('fechainicial', 'Fecha Inicial:') !!}
								{!! Form::date('fechainicial', date('Y-m-d'), array('class' => 'form-control input-xs', 'id' => 'fechainicial')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('fechafinal', 'Fecha Final:') !!}
								{!! Form::date('fechafinal', date('Y-m-d'), array('class' => 'form-control input-xs', 'id' => 'fechafinal')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('paciente', 'Paciente:') !!}
								{!! Form::text('paciente', '', array('class' => 'form-control input-xs', 'id' => 'paciente')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('numero', 'Nro:') !!}
								{!! Form::text('numero', '', array('class' => 'form-control input-xs', 'id' => 'numero')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('servicio', 'Servicio:') !!}
								{!! Form::text('servicio', '', array('class' => 'form-control input-xs', 'id' => 'servicio')) !!}
							</div>
							<div class="form-group">
                    			{!! Form::label('tiposervicio', 'Tipo:') !!}
                   				{!! Form::select('tiposervicio', $cboTipoServicio, null, array('class' => 'form-control input-xs', 'id' => 'tiposervicio' , 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
                    		</div>
							<div class="form-group">
								{!! Form::label('tipobusqueda', 'Búsqueda:')!!}
								{!! Form::select('tipobusqueda', $cboTipobusqueda,null, array('class' => 'form-control input-xs')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('descargado', 'Descargado:')!!}
								{!! Form::select('descargado', $cboDescargado,null, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('filas', 'Filas a mostrar:')!!}
								{!! Form::selectRange('filas', 1, 30, 20, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-info btn-xs', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Excel', array('class' => 'btn btn-success btn-xs', 'id' => 'btnExcel','onclick' => 'excel();')) !!}
							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Reporte Usuario', array('class' => 'btn btn-success btn-xs', 'id' => 'btnExcel','onclick' => 'excelUsuario();')) !!}
							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Reporte Diario', array('class' => 'btn btn-success btn-xs', 'id' => 'btnExcel','onclick' => 'excelDiario();')) !!}
							{!! Form::close() !!}
						</div>
					</div>
				</div>
				<!-- /.box-header -->
				<div class="box-body" id="listado{{ $entidad }}">
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
</section>
<!-- /.content -->	
<script>
	$(document).ready(function () {
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="paciente"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
			if(key == 38 || key == 40) {
	            var tabladiv='tablaLista';
				var child = document.getElementById(tabladiv).rows;
				var indice = -1;
				var i=0;
	            $('#tablaLista tr').each(function(index, elemento) {
	                if($(elemento).hasClass("tr_hover")) {
	    			    $(elemento).removeClass("par");
	    				$(elemento).removeClass("impar");								
	    				indice = i;
	                }
	                if(i % 2==0){
	    			    $(elemento).removeClass("tr_hover");
	    			    $(elemento).addClass("impar");
	                }else{
	    				$(elemento).removeClass("tr_hover");								
	    				$(elemento).addClass('par');
	    			}
	    			i++;
	    		});		 
				// abajo
				if(key == 40) {
					if(indice == (child.length - 1)) {
					   indice = 1;
					} else {
					   if(indice==-1) indice=0;
	                   indice=indice+1;
					} 
				// arriba
				} else if(key == 38) {
					indice = indice - 1;
					if(indice==0) indice=-1;
					if(indice < 0) {
						indice = (child.length - 1);
					}
				}	 
				child[indice].className = child[indice].className+' tr_hover';
			
        	}
		});
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="numero"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
			if(key == 38 || key == 40) {
	            var tabladiv='tablaLista';
				var child = document.getElementById(tabladiv).rows;
				var indice = -1;
				var i=0;
	            $('#tablaLista tr').each(function(index, elemento) {
	                if($(elemento).hasClass("tr_hover")) {
	    			    $(elemento).removeClass("par");
	    				$(elemento).removeClass("impar");								
	    				indice = i;
	                }
	                if(i % 2==0){
	    			    $(elemento).removeClass("tr_hover");
	    			    $(elemento).addClass("impar");
	                }else{
	    				$(elemento).removeClass("tr_hover");								
	    				$(elemento).addClass('par');
	    			}
	    			i++;
	    		});		 
				// abajo
				if(key == 40) {
					if(indice == (child.length - 1)) {
					   indice = 1;
					} else {
					   if(indice==-1) indice=0;
	                   indice=indice+1;
					} 
				// arriba
				} else if(key == 38) {
					indice = indice - 1;
					if(indice==0) indice=-1;
					if(indice < 0) {
						indice = (child.length - 1);
					}
				}	 
				child[indice].className = child[indice].className+' tr_hover';
			
        	}
		});
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="servicio"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
			if(key == 38 || key == 40) {
	            var tabladiv='tablaLista';
				var child = document.getElementById(tabladiv).rows;
				var indice = -1;
				var i=0;
	            $('#tablaLista tr').each(function(index, elemento) {
	                if($(elemento).hasClass("tr_hover")) {
	    			    $(elemento).removeClass("par");
	    				$(elemento).removeClass("impar");								
	    				indice = i;
	                }
	                if(i % 2==0){
	    			    $(elemento).removeClass("tr_hover");
	    			    $(elemento).addClass("impar");
	                }else{
	    				$(elemento).removeClass("tr_hover");								
	    				$(elemento).addClass('par');
	    			}
	    			i++;
	    		});		 
				// abajo
				if(key == 40) {
					if(indice == (child.length - 1)) {
					   indice = 1;
					} else {
					   if(indice==-1) indice=0;
	                   indice=indice+1;
					} 
				// arriba
				} else if(key == 38) {
					indice = indice - 1;
					if(indice==0) indice=-1;
					if(indice < 0) {
						indice = (child.length - 1);
					}
				}	 
				child[indice].className = child[indice].className+' tr_hover';
			
        	}
		});

	});
	function cargado(check,idmov,tipo){
		$.ajax({
	        type: "POST",
	        url: "prefactura/cargado",
	        data: "id="+idmov+"&check="+check+"&tipo="+tipo+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),
			error: function(xhr, status, error) {
        		// handle error
        		alert('No se pudo guardar...intente de nuevo');
    		},
	        success: function(a) {
	            if(a!='OK'){
	            	alert('Error guardando descargo');
	            }
	        }
    	}).fail( function() {
		    alert('No se pudo guardar...intente de nuevo');
		});
	}
	function guardarObservacion(value,idmov,tipo){
		$.ajax({
	        type: "POST",
	        url: "prefactura/observacion",
	        data: "id="+idmov+"&value="+value+"&tipo="+tipo+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),
	        success: function(a) {
	            if(a!='OK'){
	            	alert('Error guardando observacion');
	            }
	        }
    	});
	}
	function excel(entidad){
	    window.open("prefactura/excel?"+$(IDFORMBUSQUEDA + '{{ $entidad }}').serialize(),"_blank");
	}
	function excelUsuario(entidad){
	    window.open("prefactura/excelUsuario?"+$(IDFORMBUSQUEDA + '{{ $entidad }}').serialize(),"_blank");
	}
	function excelDiario(entidad){
	    window.open("prefactura/excelDiario?"+$(IDFORMBUSQUEDA + '{{ $entidad }}').serialize(),"_blank");
	}
</script>