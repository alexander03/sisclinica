<form class="form-horizontal">
    <div class="form-group">
        <label for="product" class="col-lg-4 col-md-4 col-sm-4 control-label">Producto:</label>
        <div class="col-lg-8 col-md-8 col-sm-8">
            <input class="form-control input-xs" id="product" value="{{ $product }}" readonly="readonly">
        </div>
        <input type="hidden" id="product_id" value="{{ $idproducto }}">
    </div>
    <div class="form-group">
        <label for="pvf" class="col-lg-2 col-md-2 col-sm-2 control-label">PVF:</label>
        <div class="col-lg-4 col-md-4 col-sm-4">
            <input onfocus="$('#pps').val('');" onkeyup="ponerKayros();" onkeypress="return filterFloat(event, this);" class="form-control input-xs" id="pvf" placeholder="Ingrese PVF" name="pvf" type="text">
        </div>
        <label for="pps" class="col-lg-2 col-md-2 col-sm-2 control-label">PPS:</label>
        <div class="col-lg-4 col-md-4 col-sm-4">
            <input onfocus="$('#pvf').val('');" onkeyup="ponerKayros();" onkeypress="return filterFloat(event, this);" class="form-control input-xs" id="pps" placeholder="Ingrese PPS" name="pps" type="text">
        </div>
    </div>
    <div class="form-group">
        <label for="p_kayros" class="col-lg-4 col-md-4 col-sm-4 control-label">Precio Kayros:</label>
        <div class="col-lg-8 col-md-8 col-sm-8">
            <input class="form-control input-xs" id="p_kayros" name="p_kayros" type="text" value="0.00" readonly="readonly">
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-12 col-md-12 col-sm-12 text-right">
            <button class="btn btn-success btn-sm" id="btnGuardars" onclick="enviarKayros();" type="button">
                <i class="fa fa-check fa-lg"></i> Modificar
            </button>
            <button class="btn btn-warning btn-sm" id="btnCancelarConvenio" onclick="cerrarModall();" type="button">
                <i class="fa fa-exclamation fa-lg"></i> Cancelar
            </button>
        </div>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function() {
        configurarAnchoModal('400');
        $('#pvf').focus();
    }); 

    function ponerKayros() {
        var pvf = $('#pvf').val();
        var pps = $('#pps').val();

        if(pvf == '' && pps == '') {
            $('#p_kayros').val((0.00).toFixed(2));
        } else {
            if(pvf == '') {
                $('#p_kayros').val(parseFloat(pps).toFixed(2));
            } else if(pps == '') {
                var kay = ((133*parseFloat(pvf))/100).toFixed(2);
                $('#p_kayros').val(kay);
            }
        }            
    }

    function enviarKayros() {
        var kay = parseFloat($('#p_kayros').val());
        var idproducto = parseInt($('#product_id').val());
        var nomproducto = $('#nombreproducto').val();
        if(kay == 0) {
            alert('No puedes registrar un precio 0');
        } else {
            $.ajax({
                url: 'venta/cambiarKayros/' + kay + '/' + idproducto,
                type: 'GET',
                beforeSend: function() {
                    $('#btnGuardars').html('Cargando...').attr('disabled', 'disabled');
                },
                success: function(e) {
                    if(e !== '') {                        
                        $('#cantidad').focus();
                        $('#' + idproducto).attr('onclick', e + ", '" + $(IDFORMMANTENIMIENTO + 'Venta :input[id="stock"]').val() + "')");
                        $('#tdPrecioKayros' + idproducto).html(kay);
                        $(IDFORMMANTENIMIENTO + 'Venta :input[id="preciokayros"]').val(kay);
                        cerrarModal();
                    }                        
                }
            });
            
        }
    }

    function cerrarModall() {
        $(IDFORMMANTENIMIENTO + 'Venta :input[id="producto_id"]').val('');
        $(IDFORMMANTENIMIENTO + 'Venta :input[id="preciokayros"]').val('');
        $(IDFORMMANTENIMIENTO + 'Venta :input[id="precioventa"]').val('');
        $(IDFORMMANTENIMIENTO + 'Venta :input[id="stock"]').val('');      
        $(IDFORMMANTENIMIENTO + 'Venta :input[id="cantidad"]').focus();
        $('.escogerFila').css('background-color', 'white');
        cerrarModal();
    }

    function filterFloat(evt,input){
        var key = window.Event ? evt.which : evt.keyCode;    
        var chark = String.fromCharCode(key);
        var tempValue = input.value+chark;
        if(key >= 48 && key <= 57){
            if(filter(tempValue)=== false){
                return false;
            }else{       
                return true;
            }
        }else{
              if(key == 8 || key == 13 || key == 0) {     
                  return true;              
              }else if(key == 46){
                    if(filter(tempValue)=== false){
                        return false;
                    }else{       
                        return true;
                    }
              }else{
                  return false;
              }
        }
    }
    function filter(__val__){
        var preg = /^([0-9]+\.?[0-9]{0,2})$/; 
        if(preg.test(__val__) === true){
            return true;
        }else{
           return false;
        }       
    }
</script>