<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
        <meta charset="utf-8">
        <meta name="generator" content="Bootply">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="description" content="Bootstrap  example.">
        {!! Html::style('bootstrap/css/bootstrap.min.css') !!}

        <!-- CSS code from Bootply.com editor -->
        
        <style type="text/css">
            html,body {
                padding:0;
                margin:0;
                height:87%;
                min-height:100%;
            }
            .quad{
                width:50%;
                height:100%;
                float:left;
                border-style: double;
            }
            .line{
                height:50%;
                width:100%;
            }              

            .equal, .equal > div[class*='col-'] {  
                display: -webkit-box;
                display: -moz-box;
                display: -ms-flexbox;
                display: -webkit-flex;
                display: flex;
                flex:1 0 auto;
            }

        </style>
    </head>
    
    <!-- HTML code from Bootply.com editor -->
    
    <body style="">
        <div class="line">
            <div class="col-md-6 quad" id="listadoConsultas"></div>
            <div class="col-md-6 quad" id="listadoEmergencias"></div>
            <div class="col-md-6 quad" id="listadoOjos"></div>
            <div class="col-md-6 quad" id="listadoLectura"></div>
        </div>
        
    {!! Html::script('plugins/jQuery/jquery-2.2.3.min.js') !!}
    <!-- Bootstrap 3.3.6 -->
    {!! Html::script('bootstrap/js/bootstrap.min.js') !!}

    <script>
        function buscar2(){
            $.ajax({
                type: "POST",
                url: "ventaadmision/cola",
                data: "_token=<?php echo csrf_token(); ?>",
                dataType: 'json',
                success: function(a) {
                    $("#listadoConsultas").html(a.consultas);
                    $("#listadoEmergencias").html(a.emergencias);
                    $("#listadoOjos").html(a.ojos);
                    $("#listadoLectura").html(a.lectura);
                }
            });
            $('.llamando').fadeTo(500, .1).fadeTo(500, 1) ;
        }
        setInterval(buscar2, 1000);
    </script> 
    </body>
</html>