<?php
include("conexion.php");
$con = conectar();
if (!$con) {
    echo "Error al conectar a la base de datos";
    exit();
}
?>

<html>

<head>
    <!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="css.css" type="text/css">
    <link rel="stylesheet" href="css-tabla1.css" type="text/css">
    <link rel="stylesheet" href="cards.css" type="text/css">
    <script type="text/javascript" src="js/javascript.js"> </script>
    <link rel="stylesheet" href="fuentes/css/all.css">

    <!--<link rel="stylesheet" href="csstitulos.css" type="text/css">-->
</head>
<div>
    <div class="contenedor_titulo">
        <div class="div_container_titulo">
            <img src="img/logopm.jpg">
        </div>
        <div class="contenido_titulo">
            <h2 class="titulo_principal">
                CENTRO MONITOREO DE PESCAS
            </h2>
            
        </div>
        <div class="imagen_derecha">
            <img src="img/logo_CMP.png" alt="Imagen derecha">
        </div>
    </div>
    <div id="container">
        <?php
        $sql = "
                        Select	Top 1	'AG#'+Convert(Varchar,PyAqNo) 'Aguaje'	                ,
                        Case
                            When	CONVERT(nvarchar(10), GETDATE(), 108)>'07:00'
                                And	CONVERT(nvarchar(10), GETDATE(), 108)<'19:00'
                            Then	'Día'
                            Else	'Noche'
                        End	 'Turno'		                                                    ,
                        (
                            Select Count(*) From Vi_Guias_CMP
                        ) 'TotPlataformas'                                                      ,
                        (
                            Select IsNull(Count(*),0) From Vi_Guias_CMP Where FechaSalidaPlanta=''
                        ) 'NoIniciados'                                                         ,
                        (
                            Select IsNull(Count(*),0) From Vi_Guias_CMP Where FechaSalidaPlanta<>'' And FechaLlegadaCamaronera='' And FechaMovilListo='' And FechaCamaroneraPlanta='' And  FechaRealLlegada='' --Ruta Granja 
                        ) 'RutaGranja'                                                          ,
                        (
                            Select IsNull(Count(*),0) From Vi_Guias_CMP Where FechaSalidaPlanta<>'' And FechaLlegadaCamaronera<>'' And FechaMovilListo='' And FechaCamaroneraPlanta='' And  FechaRealLlegada='' --En Granja
                        )   'EnGranja'                                                          ,
                        (
                            Select IsNull(Count(*),0) From Vi_Guias_CMP Where FechaSalidaPlanta<>'' And FechaLlegadaCamaronera<>'' And FechaMovilListo<>'' And FechaCamaroneraPlanta<>'' And  FechaRealLlegada='' --Ruta a Planta
                        )   'RutPlanta'                                                         ,
                        (
                            Select IsNull(Count(*),0) From Vi_Guias_CMP Where FechaRealLlegada<>'' --En Planta
                        )   'EnPlanta'                                                          ,
                        (
                            Select IsNull(dbo.Fn_Cmp_Devulve_Kg_Remitidos('TKR',''),0)	
                        )'TotKgEnRuta'       ,
                        (
                            Select IsNull(dbo.Fn_Cmp_Devulve_Kg_Remitidos('TKP',''),0)	
                        )'TotKgEnPlanta'     ,
                        Right('00' + Ltrim(Rtrim(Day(GetDate()))),2) + ' de '	+	Case Month(GetDate())
																						When 1	Then 'Enero'
																						When 2	Then 'Febrero'
																						When 3	Then 'Marzo'
																						When 4	Then 'Abril'
																						When 5	Then 'Mayo'
																						When 6	Then 'Junio'
																						When 7	Then 'Julio'
																						When 8	Then 'Agosto'
																						When 9	Then 'Septiembre'
																						When 10	Then 'Octubre'
																						When 11	Then 'Noviembre'
																						When 12	Then 'Diciembre'
																					End  
																				+ ' del '+ Convert(Character(4),Year(GetDate()))		'FechaActual'   
                        

                        From	PRPYAQ	With(NoLock)
                        Where	PyAqFecIni	<=  GETDATE()             
                        Order	By PyAqFecIni Desc";
        $result = sqlsrv_query($con, $sql);
        while ($muestra = sqlsrv_fetch_array($result)) {
            $aguaje = $muestra['Aguaje'];
            $turno = $muestra['Turno'];
            $fechaactual = $muestra['FechaActual'];
            $totalPlataformas = $muestra['TotPlataformas'];
            $noIniciados = $muestra['NoIniciados'];
            $rutaGranja = $muestra['RutaGranja'];
            $enGranja = $muestra['EnGranja'];
            $rutaPlanta = $muestra['RutPlanta'];
            $enPlanta = $muestra['EnPlanta'];
            $totalKgEnRuta = $muestra['TotKgEnRuta'];
            $totalKgEnPlanta = $muestra['TotKgEnPlanta'];

        }
        ?>
        <div class="kpi-card orange">
            <table class="custom-table ">
                <tr>
                    <td class="izquierda"><strong>FECHA:</strong></td>
                    <td class="izquierda">
                        <?php
                        echo $fechaactual;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="izquierda"><strong>TURNO:</strong></td>
                    <td class="izquierda"><?php echo $turno; ?></td>
                </tr>
                <tr>
                    <td class="izquierda"><strong>AGUAJE:</strong></td>
                    <td class="izquierda"><?php echo $aguaje; ?></td>
                </tr>
            </table>
        </div>
        <div class="kpi-card red">
           
            <table class="custom-table custom-table-secondary">
                <tr>
                    <td><strong>N° PLATAFORMAS</strong></td>
                    <td>

                    </td>
                </tr>
                <tr colspan='2'>
                    <td><strong><?php echo $totalPlataformas; ?></strong></td>
                    <td></td>
                </tr>
                <tr colspan='2'>
                    <td><strong>No Iniciado:</strong></td>
                    <td><?php echo $noIniciados; ?></td>
                </tr>
            </table>

        </div>
        <div class="kpi-card purple">
            <table class="custom-table custom-table-tercera">
                <tr>
                    <td><strong>SALIDAS</strong></td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td><strong>Ruta a Granja:</strong></td>
                    <td><?php echo $rutaGranja ?></td>
                </tr>
                <tr>
                    <td><strong>En Granja :</strong></td>
                    <td><?php echo $enGranja ?></td>
                </tr>
            </table>
           
        </div>

        <div class="kpi-card purple">
            <table class="custom-table custom-table-tercera">
                <tr>
                    <td><strong>RETORNOS</strong></td>
                    <td>
                    <td><strong>KG REMITIDOS</strong></td>
                    </td>
                </tr>
                <tr>
                    <td><strong>Ruta a Planta:</strong></td>
                    <td><?php echo $rutaPlanta ?></td>
                    <td><strong>En Ruta:</strong></td>
                    <td><?php echo $totalKgEnRuta ?></td>
                </tr>
                <tr>
                    <td><strong>En Planta:</strong></td>
                    <td><?php echo $enPlanta ?></td>
                    <td><strong>En Planta:</strong></td>
                    <td><?php echo $totalKgEnPlanta ?></td>
                </tr>
            </table>
            
        </div>


    </div>
  
    <div id="tabla_registros">

        <!-- Aqui va la tabla -->
        <script type="text/javascript">
            let contador = 1;
            
            $(document).ready(function () {
                cargarTabla();
                $(document).on('click', 'a', function (event) {
                    event.preventDefault(); 
                    const numero = $(this).text().trim();
                    const id = $(this).attr('id');
                    console.log(id);
                    if (id=="siguiente" || id=="anterior") {
                        if (id=="siguiente") {
                            cargarTabla();
                        }
                        else{
                            if (contador>1) {
                                contador = contador - 1;
                                const numeroPaginas = $('#numeroPagi').val();
                                if(contador<1){
                                    contador = 1;
                                }
                                cargarTablaDatos(contador);
                            }
                        }
                        
                    }else{
                        cargarTablaDatos(id);
                    }
                });
            });

            setInterval(function () {
                cargarTabla();
            }, 8000);
            function cargarTabla() {
                const numeroPaginas = $('#numeroPagi').val();
                $.ajax({
                    url: 'registrostabla.php',
                    type: 'post',
                    data: { contador: contador },
                    success: function (data) {
                        $('#tabla_registros').html(data);
                        contador += 1;
                        if(contador>numeroPaginas){
                            contador = 1;
                        }
                    }
                });
            }
            function cargarTablaDatos(datos) {
                const numeroPaginas = $('#numeroPagi').val();
                console.log(numeroPaginas);
                $.ajax({
                    url: 'registrostabla.php',
                    type: 'post',
                    data: { contador: datos },
                    success: function (data) {
                        $('#tabla_registros').html(data);                        
                    }
                });
            }
            function actualizar() {
                contador = 1
                location.reload(true);
            }
            //Función para actualizar cada 5 segundos(5000 milisegundos)
            setInterval("actualizar()", 120000);
        </script>
    </div>

</div>


</html>