<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script>
			function consultaRUC(){
			    var ruc = $("#txtNroDoc").val();
			    $.ajax({
			        type: 'GET',
			        url: "demo.php",
			        data: "ruc="+ruc,
			        beforeSend(){
			         alert("Consultando...");
			        },
			        success: function (data, textStatus, jqXHR) {
			         alert("Datos Recibidos");
			            $("#txtNombres").val(data.RazonSocial);
			            $("#txtDireccion").val(data.Direccion);
			            $("#txtNombres").focus();
			            $("#txtDireccion").focus();
			        }
			    });
			}
		</script>
	</head>
	<body>
		NUM DOC<input type="text" id="txtNroDoc">
		NOMBRES <input type="text" id="txtNombres">
		DIRECCION <input type="text" id="txtDireccion">
		<button onclick="consultaRUC()">ENVIAR</button>
	</body>
</html>