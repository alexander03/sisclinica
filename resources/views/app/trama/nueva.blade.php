<h4>Lote Nº <input type="number" id="lote" value="{{ $lote }}" disabled> </h4>

<div class="form-group">
	<select id="planD">
		<option value="POSITIVA">Positiva Seguros</option>
		<option value="POSITIVA EPS">Positiva EPS</option>
		<option value="PACIFICO">Pacifico</option>
		<option value="RIMAC EPS">RIMAC EPS</option>
		<option value="RIMAC SEGUROS">RIMAC SEGUROS</option>
		<option value="BANCO">FEBAN</option>
	</select>
	{{-- <input class="input-xs" type="text" name="documento" id="documento" placeholder="Nº Doc">
	<button class="btn btn-default btn-xs" onclick="agregarL()"><i class="glyphicon glyphicon-plus"></i> Agregar</button> --}}
	<button class="btn btn-warning btn-xs" onclick="actualizaLista()"><i class="glyphicon glyphicon-refresh"></i> Actualizar</button>
	<input type="checkbox" onclick="marcar()" checked>
	<button class="btn btn-danger btn-xs" style="position: absolute; right: 13px;" onclick="generar()"><i class="glyphicon glyphicon-upload"></i> Generar</button>
</div>

<div style="margin-top: 5px;padding: 5px 5px; border: 1px solid grey;" id="listaD">
	<p style="color:grey;">Lista vacía.</p>
</div>

<script type="text/javascript">
	var lista = [];
	var plan = '';
	var stotal = 0;

	$('#planD').change(function(){
		plan = $('#planD').val();
	});

	function definirPlan(){
		plan = $('#planD').val();
	}
	definirPlan();

	function agregarL(){
		var doc = $('#documento').val();
		lista.push(doc);
		actualizaLista();
	}

	function quitar(pos){
		lista.splice(pos, 1);;
		actualizaLista();
	}

	function actualizaLista(){
		var fechainicial = $('#fechainicial').val();
		var fechafinal = $('#fechafinal').val();
		$.ajax({
			type:'GET',
			url:"tramag/listarD",
			data:{'plan':plan,'lista':lista,'fechainicial':fechainicial,'fechafinal':fechafinal},
			success: function(a) {
				$('#listaD').html(a);
			}
		});
	}

	function generar(){
		$(".lista:checkbox:checked").each(function () {
			lista.push(this.id);
		});
		console.log (lista);

		var contenido = lista.length;
		var lote = $('#lote').val();
		if (contenido > 0) {
			$.ajax({
				type:'GET',
				url:"tramag/generar",
				data:{'plan':plan,'lista':lista,'lote':lote},
				success: function(a) {
					if (a == 'N') {
						alert("Error");
					} else {
						contenido > 1 ? contenido = lista.length+" facturas." : contenido = lista.length+" factura.";
						$('#listaD').html('Registrado lote Nº '+lote+' conteniendo '+contenido+' <button class="btn btn-danger btn-xs" style="position: absolute; right: 13px;" onclick="ver()"><i class="glyphicon glyphicon-upload"></i> Ver Trama</button>');
					}
				}
			});
		}
	}

	$(document).ready(function () {
		$('#documento').keyup(function (e) {
				var key = window.event ? e.keyCode : e.which;
				if (key == '13') {
					agregarL();
			}
		});

	});

	function ver(){
		window.open("trama",'_blank');
	}
	actualizaLista();

	$('#btnGuardar').click(function(){
		alert("lol");
		actualizaLista();
	});

	function marcar(){
		$(".lista:checkbox").each(function () {
			if( $(this).attr("checked") ) {
				$(this).attr("checked",false);
			} else {
				$(this).attr("checked",true);
			}
		});
	}
</script>