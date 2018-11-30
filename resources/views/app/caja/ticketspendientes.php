<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<div class="col-xs-12">
				<form method="POST" action="http://localhost/juanpablo/ticket/buscar" accept-charset="UTF-8" onsubmit="return false;" class="form-inline" role="form" autocomplete="off" id="formBusquedaTicket"><input name="_token" type="hidden" value="fzMi63wo0hi1zWJq3rB67PM26duZnFUPRy6CYD03">
					<input id="page" name="page" type="hidden" value="1">
					<input id="accion" name="accion" type="hidden" value="listar">
					<div class="form-group">
						<label for="fecha">Fecha:</label>
						<input class="form-control input-xs" id="fecha" name="fecha" type="date" value="2018-11-30">
					</div>
					<div class="form-group">
						<label for="numero">Nro:</label>
						<input class="form-control input-xs" id="numero" name="numero" type="text" value="">
					</div>
					<button class="btn btn-success btn-xs" id="btnBuscar" onclick="buscar('Ticket')" type="button">
						<i class="glyphicon glyphicon-search"></i> Buscar
					</button>
				</form>
			</div>
		</div>
		<div class="box-body" id="listadoTicket">
			<h3 class="text-warning">No se encontraron resultados.</h3>
		</div>
		<!-- /.box -->
	</div>
	<!-- /.col -->
</div>