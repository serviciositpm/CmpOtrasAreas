<?php
    include("conexion.php");
    $con=conectar();
    if(!$con) {     
        echo"Error al conectar a la base de datos"; 
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
        <link rel="stylesheet" href="fuentes/css/all.css" >
        
        <!--<link rel="stylesheet" href="csstitulos.css" type="text/css">-->
    </head>
    <div>
        <div class="contenedor_titulo">
            <div class="div_container_titulo">
                <img src="img/logopm.jpg">
            </div>
            <div class="contenido_titulo">
                <h2 class="titulo_principal">
                   <u>Datos Recepciòn </u> 
                </h2>
            </div>
        </div>
        <div id="container">
            <?php 
                $sql="
                        Select	Top 1	'AG#'+Convert(Varchar,PyAqNo) 'Aguaje'	        ,
                        Case
                            When	CONVERT(nvarchar(10), GETDATE(), 108)>'07:00'
                                And	CONVERT(nvarchar(10), GETDATE(), 108)<'19:00'
                            Then	'Día'
                            Else	'Noche'
                        End	 'Turno'		                                             ,
                        (Select Sum(Kilos)  From Vsp_DatosRecepcion Where Tipo	='Saldo' And Proceso = 'CC X CC') 'TotKilos',
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
                $result=sqlsrv_query($con,$sql);
                while($muestra=sqlsrv_fetch_array($result)){
                    $aguaje=$muestra['Aguaje'];
                    $turno=$muestra['Turno'];
                    $totKilos=$muestra['TotKilos'];
                    $fechaactual=$muestra['FechaActual'];
                }
            ?>
            <div class="kpi-card orange">
                <span class="card-value">Fecha</span>
                <span class="card-text"><?php 
                        
                       //$Dateactual = $fechaactual->format('d/m/Y');
                        //echo date('d/m/Y');
                        //setlocale(LC_TIME, "spanish");
                        //echo strftime("%d de %B del %Y");
                        echo  $fechaactual;
                        //echo date('F j, Y, g:i a');
                        //echo $Dateactual;
                        
                        
                        ?> 
                </span>
                <i class="fas fa-calendar icon"></i>
                
                
            </div>
            <div class="kpi-card red">
                <span class="card-value">Turno</span>
                <span class="card-text">
                    <?php 
                        echo $turno; 
                    ?>
                </span>
                <i class="fas fa-stopwatch icon"></i>
                
            </div>
            <div class="kpi-card purple">
                <span class="card-value">Aguaje</span>
                <span class="card-text"><?php echo $aguaje?> </span>
                <i class="fas fa-moon icon"></i>
            </div>
            
            <div class="kpi-card purple">
                <span class="card-value">Hora</span>
                <span class="card-text">
                    <?php 
                        //$DateAndTime = date('m-d-Y h:i:s a', time());  
                        date_default_timezone_set('America/Bogota');    
                        echo date('H:i ', time());
                        //echo date('h:i a', time());
                    ?> 
                </span>
                <i class="fas fa-clock icon"></i>
            </div>
            
            
            <div class="kpi-card red">
                <span class="card-value">Total KG</span>
                <span class="card-text">
                    <?php 
                        echo number_format($totKilos,2); 
                    ?>
                </span>
                <i class="fas fa-weight-hanging icon"></i>
                
            </div>
        </div>
        <!-- <div class="titulo_tabla_dash">
            <h2>Detalle Guìas de Pesca (CC x CC)</h2>
            <h2 class="titulo_tabla_page">Pág #</h2>
            <h2>1</h2>
            <h2>De</h2>
            <h2>5</h2>
        </div> -->
        <div id="tabla_registros">
            
            <!-- Aqui va la tabla -->
            <script type="text/javascript">
                let contador=1;
                $(document).ready(function(){
                    $.ajax({
                        url: 'registrostabla.php',
                        type: 'post',
                        data: {contador:contador},
                        success: function(data) {
                            $('#tabla_registros').html(data);
                        }
                    });    
                });
                
                setInterval(function(){
                    $.ajax({
                        url: 'registrostabla.php',
                        type: 'post',
                        data: {contador:contador},
                        success: function(data) {
                            $('#tabla_registros').html(data);
                            contador+=1;
                        }
                    });    
                },10000);

                function actualizar(){
                    contador = 1
                    location.reload(true);
                }
                //Función para actualizar cada 5 segundos(5000 milisegundos)
                setInterval("actualizar()",60000);
            </script>
        </div>
        
    </div>
    <!-- <script type="text/javascript">
        const progress = document.querySelector(".js-completed-bar");
        if (progress) {
            progress.style.width = progress.getAttribute("data-complete") + "%";
            progress.style.opacity = 1;
        }
        //Función actualizar
        
        function actualizar(){location.reload(true);}
        //Función para actualizar cada 5 segundos(5000 milisegundos)
        setInterval("actualizar()",10000);
        
    </script> -->
    
</html>
