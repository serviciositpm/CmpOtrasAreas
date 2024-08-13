<?php
    include("conexion.php");
    $con=conectar();
    if(!$con) {     
        echo"Error al conectar a la base de datos"; 
        exit();               
    }
    //Recibo el contador
    $contador = $_POST['contador'];
    $cantFilas=0;
    //1.- Obtengo la cantidad de Registros
    $sqlCantRows = "Select IsNull(Count(*),0) Cantidad From Vsp_DatosRecepcion Where Tipo	='Saldo' And Proceso = 'CC X CC' And TipoProceso =   'P'";
    $resultCantRows=sqlsrv_query($con,$sqlCantRows);
    while($mostrarCantRows=sqlsrv_fetch_array($resultCantRows)){
        $cantFilas=$mostrarCantRows['Cantidad'];
    }
    //2.- Obtengo Cantidad de Páginas
    $per_page = 12;
    $entero=0;
    $start=1;
    $desde  =   0;
    if($cantFilas>0){
        $numeroPaginas=floor($cantFilas/$per_page);
        if($cantFilas%$per_page>0){
            $numeroPaginas=$numeroPaginas+1;
        }
        
        if($contador>$numeroPaginas){
            $entero =   floor($contador/$numeroPaginas);
            $pagina =   $entero;
        }else{
            $pagina =   $contador;
        }
        if($pagina  >   1){
            $desde  =   (($pagina*($per_page-1))-$per_page)+1;

        }else{
            //$desde  =   $contador-1;
            $desde  =   0;
        }
        if($numeroPaginas==1){
            $pagina =   1;
            $desde  =   0;
        }
        /*
        echo "Desde ->".$desde;
        echo "          Cant Filas   ->".$per_page;
        */
        echo"<div class='titulo_tabla_dash'>";
            echo"<h2>Detalle Guìas de Pesca (CC x CC)</h2>";
            echo"<h2 class='titulo_tabla_page'>Pág ".$pagina." De ".$numeroPaginas."</h2>";
        echo"</div>";

        $sql="  Select	IngresoSeguridad			                                                    ,
                        NoGuia						                                                    ,
                        --Sec							                                                ,
                        Proveedor					                                                    ,
                        Piscina						                                                    ,
                        OrdenPesca                                                                      ,
                        FechaLLegadaPlanta			                                                    ,
                        Kilos						                                                    ,
                        [Gramaje Calidad] GramajeCalidad                                                ,
                        Mudado                                                                          ,
                        Flacido                                                                         ,
                        TiempoTratamiento                                                               ,
                        Case 
                            When [Fecha maxima Tratamiento Inicial] <>''
                                Then Convert(Char(5),Cast([Fecha maxima Tratamiento Inicial] As Time)) 
                            Else
                                ''
                        End 'TiempoMax'                                                                 ,
                        --TiempoInicioPescaPlanta		                                                    ,
                        TiempoEsperaRecepcionPlanta	                                                    ,
                        Case 
                            When	TiempoEsperaRecepcionPlanta	>='00:00'	And	TiempoEsperaRecepcionPlanta	<=	'04:59'
                                Then	10 --Verde 
                            When	TiempoEsperaRecepcionPlanta	>='05:00'	And	TiempoEsperaRecepcionPlanta	<=	'07:59'
                                Then	20 --Amarillo
                            When	TiempoEsperaRecepcionPlanta	>='08:00'
                                Then	30	--Rojo
                        End	'Indicador'                                                                 ,
                        Case 
                            When TiempoHrasInterrupcionTratamientoInicial > TiempoTratamiento And [TipoTratamiento]='Directo' And HayEscurrido='S'
                                Then	10 --Rojo
                            When TiempoTratamiento - TiempoHrasInterrupcionTratamientoInicial <= 3 And [TipoTratamiento]='Directo' And HayEscurrido='S'
                                Then	20 --Amarillo
                            When [TipoTratamiento]='Tinas' --And HayEscurrido='S'
                                Then	2000
                            When [TipoTratamiento]='Directo' And HayEscurrido='N'
                                Then dbo.ObDifFechasenHoras(convert(date,getdate()),convert(char(5),getdate(),108),convert(date,[Fecha maxima Tratamiento Inicial])  ,convert(char(5),convert(datetime,[Fecha maxima Tratamiento Inicial]) ,108))   
                            When HayEscurrido='S'
                                Then	30 --Verde
                            When HayEscurrido='N'
                                Then 3000
                            
                        End 'Tiempo'                                                                   ,
                        Case
                            When    EstadoAnalisis = 'P'
                                Then 'S'
                            Else
                                ''
                        End 'EstadoAnalisis'                                                            ,
                        /*
                        Case
                            When [TipoTratamiento]='Directo' And HayEscurrido=''
                                Then dbo.ObDifFechasenHoras(convert(date,getdate()),convert(char(5),getdate(),108),convert(date,[Fecha maxima Tratamiento Inicial])  ,convert(char(5),convert(datetime,[Fecha maxima Tratamiento Inicial]) ,108))   
                            Else
                                2000
                        End 'Tiempo'                                                                      ,
                        */
                        [Rendimiento Calidad] 'Rendimiento'                                               ,
                        [Promedio Residual] 'PromedioResidual'                                            ,
                        [Tiempo Total Espera] 'TiempoTotalEspera'                                         ,
                        HayEscurrido                                                                      ,
                        [TipoTratamiento] 'TipTrat'                                                       ,
                        [Calidad Estado Cabezas] 'CalidadCabezas'                                         ,
                        [Trat. Cumplido] 'TratCumplido'                                                   ,
                         Case 
                            When [Tiempo Interrupcion Tratamiento Final]  <>''
                                Then Convert(Char(5),Cast([Tiempo Interrupcion Tratamiento Final]  As Time)) 
                            Else
                                ''
                        End 'TiempoFinalTrat'                                                             ,
                        [valor máximo de sulfitos] 'ValMaxSulf'

                        
                From Vsp_DatosRecepcion 
                Where   Tipo	    =   'Saldo' 
                And     Proceso     =   'CC X CC' 
                And     TipoProceso =   'P'
                Order By FechaLLegadaPlanta,IngresoSeguridad  
                OFFSET $desde ROWS 
                FETCH NEXT $per_page ROWS ONLY";
        $result=sqlsrv_query($con,$sql);
        echo"<table>";
                echo"<thead>";
                    echo"<tr>";
                        echo"<th class='ancho_celdas_normales'> Fecha Llegada Planta </th>"; //1
                        echo"<th class='ancho_celdas_normales'> # Ingreso</th>"; //2
                        echo"<th class='ancho_celdas_normales'> # Guia</th>"; //3
                        echo"<th class='ancho_celdas_normales'> Proveedor </th>"; //4
                        echo"<th class='ancho_celdas_normales'> # Pisc </th>"; //5
                        echo"<th class='ancho_celdas_normales'> Orden Pesca </th>"; //6
                        echo"<th class='ancho_celdas_normales'> Kilos </th>"; //7
                        echo"<th class='ancho_celdas_normales'> Gramaje </th>"; //8
                        echo"<th class='ancho_celdas_normales'> Mudado </th>"; //9
                        echo"<th class='ancho_celdas_normales'> Flácido </th>"; //10 
                        echo"<th class='ancho_celdas_normales'> Rendimiento </th>"; //11
                        echo"<th class='ancho_celdas_normales'> Cal.Est. Cab.</th>"; //12
                        echo"<th class='ancho_celdas_normales'>  </th>"; //13
                        echo"<th class='ancho_celdas_normales'> T. Tratamiento </th>"; //14
                        echo"<th class='ancho_celdas_normales'> T. Max Trat. Ini. </th>"; //15
                        echo"<th class='ancho_celdas_normales'> T. Max Trat. Fin. </th>"; // 16 +
                        echo"<th class='ancho_celdas_barra'> </th>"; //17
                        echo"<th class='ancho_celdas_normales'>  </th>"; //18
                        echo"<th class='ancho_celdas_normales'> Val. Máx. Sulf. </th>"; //19 +
                        echo"<th class='ancho_celdas_normales'> Prom. Resid. </th>"; //20
                        //echo"<th class='ancho_celdas_normales'> T. Pesca Planta</th>";
                        echo"<th class='ancho_celdas_normales'> T. Esp. Recepciòn </th>"; //21
                        echo"<th class='ancho_celdas_barra'>    </th>"; //22
                        echo"<th class='ancho_celdas_normales'> T. Tot. Espera </th>"; //23
                        
                    echo"</tr>";
                echo"</thead>";
                echo"<tbody>";
                    while($mostrar=sqlsrv_fetch_array($result)){
                        echo"<tr>";
                            echo"<td>".$mostrar['FechaLLegadaPlanta']->format('d/m/Y')."</td>"; //1
                            echo"<td>".$mostrar['IngresoSeguridad']."</td>"; //2
                            echo"<td>".$mostrar['NoGuia']."</td>"; //3
                            echo"<td>".$mostrar['Proveedor']."</td>"; //4
                            echo"<td>".$mostrar['Piscina']."</td>"; //5
                            echo"<td>".$mostrar['OrdenPesca']."</td>"; //6
                            echo"<td>".number_format($mostrar['Kilos'],2)."</td>"; //7
                            echo"<td>".number_format($mostrar['GramajeCalidad'],0)."</td>";      //8
                            echo"<td>".number_format($mostrar['Mudado'],2)."</td>";      //9
                            echo"<td>".number_format($mostrar['Flacido'],2)."</td>";      //10
                            echo"<td>".number_format($mostrar['Rendimiento'],2)."</td>";  //11                                      
                            echo"<td>".$mostrar['CalidadCabezas']."</td>";     //12                                   
                            $estadoAnalisis =   $mostrar['EstadoAnalisis']; //13
                            $hayEscurrido=$mostrar['HayEscurrido']; //14
                            if($estadoAnalisis=='S'){
                                echo"<td><i class='fas fa-info-circle'></i></td>";              
                                                      
                            }else{
                                echo"<td>".$mostrar['EstadoAnalisis']."</td>";                                        
                            }
                            
                            echo"<td>".$mostrar['TiempoTratamiento']."</td>"; //15
                            echo"<td>".$mostrar['TiempoFinalTrat']."</td>"; //16
                            echo"<td>".$mostrar['TiempoMax']."</td>"; //17
                            echo"<td>"; //18
                                $valortiempo = $mostrar['Tiempo'];
                                $hayescurrido= $mostrar['HayEscurrido'];
                                $tipoTratamiento = $mostrar['TipTrat'];
                                //Solo cuando el tratamiento es O debe ser un azul 
                                $tratcumplido = $mostrar['TratCumplido'];
                                if($hayescurrido=='S' && $tipoTratamiento <> 'Tinas' && $tratcumplido<> 'O'){
                                    //if($valortiempo>3 && $valortiempo<>2000){
                                    if($valortiempo==30){ //Rojo
                                        echo "
                                        <div  class='btn btn-success btn-circle btn-circle-sm m-1'>
                                            
                                        </div>
                                        ";
                                        /* <i class='fa fa-check'></i> */
                                        

                                    }
                                    if($valortiempo==20){ //Amarillo
                                    //if($valortiempo<3 && $valortiempo>0 && $valortiempo<>2000){        
                                        echo "
                                        <div  class='btn btn-warning btn-circle btn-circle-sm m-1'>
                                        </div>
                                        
                                        ";
                                        /* <i class='fa fa-tags'></i> */
                                        
                                    }
                                    if($valortiempo==10){ //Verde
                                    //if($valortiempo<=0 && $valortiempo<>2000){        
                                        echo "
                                        <div  class='btn btn-danger btn-circle btn-circle-sm m-1'>
                                        </div>";
                                        /* <i class='fa fa-times'></i> */
                                    }
                                }if($hayescurrido=='N' && $tipoTratamiento <> 'Tinas' && $tratcumplido<> 'O'){
                                    //Rojo
                                    if($valortiempo>3 && $valortiempo<>3000){
                                        
                                            echo "
                                            <div  class='btn btn-success btn-circle btn-circle-sm m-1'>
                                                
                                            </div>
                                            ";
                                            /* <i class='fa fa-check'></i> */
                                            

                                        }
                                        //Amarillo
                                        if($valortiempo<3 && $valortiempo>0 && $valortiempo<>3000){        
                                            echo "
                                            <div  class='btn btn-warning btn-circle btn-circle-sm m-1'>
                                            </div>
                                            
                                            ";
                                            /* <i class='fa fa-tags'></i> */
                                            
                                        }
                                        //Verde
                                        if($valortiempo<=0 && $valortiempo<>3000){        
                                            echo "
                                            <div  class='btn btn-danger btn-circle btn-circle-sm m-1'>
                                            </div>";
                                            /* <i class='fa fa-times'></i> */
                                        }
                                }if($tratcumplido== 'O'){
                                    echo "
                                        <div  class='btn btn-blue btn-circle btn-circle-sm m-1'>
                                        </div>";
                                }
                            echo"</td>";
                            if($hayEscurrido=='S'){
                                echo"<td><i class='fas fa-check'></i></td>";                                               
                            }else{
                                echo"<td></td>";                                               
                            }
                            echo"<td>".number_format($mostrar['ValMaxSulf'],0)."</td>";
                            echo"<td>".number_format($mostrar['PromedioResidual'],0)."</td>";
                            //echo"<td>".$mostrar['TiempoInicioPescaPlanta']."</td>";
                            echo"<td>".$mostrar['TiempoEsperaRecepcionPlanta']."</td>";
                            
                            echo"<td>";
                                $porcentaje = $mostrar['Indicador'];
                                if($porcentaje==10){
                                    echo "
                                    <div  class='btn btn-success btn-circle btn-circle-sm m-1'>
                                        
                                    </div>
                                    ";
                                    /* <i class='fa fa-check'></i> */
                                }
                                if($porcentaje==20){        
                                    echo "
                                    <div  class='btn btn-warning btn-circle btn-circle-sm m-1'>
                                    </div>
                                    
                                    ";
                                    /* <i class='fa fa-tags'></i> */
                                }
                                if($porcentaje==30){        
                                    echo "
                                    <div  class='btn btn-danger btn-circle btn-circle-sm m-1'>
                                    </div>";
                                    /* <i class='fa fa-times'></i> */
                                }
                            echo"</td>";
                            echo"<td>".number_format($mostrar['TiempoTotalEspera'],2)."</td>";
                        echo"</tr>";
                            
                    }
                echo"</tbody>";
        echo"</table>";
    }else{
        echo"<div class='titulo_tabla_dash'>";
            echo"<h2>Detalle Guìas de Pesca (CC x CC)</h2>";
            //echo"<h2 class='titulo_tabla_page'>Pág ".$pagina." De ".$numeroPaginas."</h2>";
        echo"</div>";
        echo"<table>";
                echo"<thead>";
                    echo"<tr>";
                        echo"<th class='ancho_celdas_normales'> Fecha Llegada Planta </th>";
                        echo"<th class='ancho_celdas_normales'> # Ingreso</th>";
                        echo"<th class='ancho_celdas_normales'> # Guia</th>";
                        echo"<th class='ancho_celdas_normales'> Proveedor </th>";
                        echo"<th class='ancho_celdas_normales'> # Pisc </th>";
                        echo"<th class='ancho_celdas_normales'> Orden Pesca </th>";
                        echo"<th class='ancho_celdas_normales'> Kilos </th>";
                        echo"<th class='ancho_celdas_normales'> Gramaje </th>";
                        echo"<th class='ancho_celdas_normales'> Mudado </th>";
                        echo"<th class='ancho_celdas_normales'> Flácido </th>";
                        echo"<th class='ancho_celdas_normales'> Rendimiento </th>";
                        echo"<th class='ancho_celdas_normales'>  </th>";
                        echo"<th class='ancho_celdas_normales'> T. Tratamiento </th>";
                        echo"<th class='ancho_celdas_normales'> T. Max Trat. Ini. </th>";
                        echo"<th class='ancho_celdas_normales'> T. Max Trat. Fin. </th>";
                        echo"<th class='ancho_celdas_barra'> </th>";
                        echo"<th class='ancho_celdas_normales'> Val. Máx. Sulf. </th>"; 
                        echo"<th class='ancho_celdas_normales'> Prom. Resid. </th>";
                        //echo"<th class='ancho_celdas_normales'> T. Pesca Planta</th>";
                        echo"<th class='ancho_celdas_normales'> T. Esp. Recepciòn </th>";
                        echo"<th class='ancho_celdas_barra'>    </th>";
                        echo"<th class='ancho_celdas_normales'> T. Tot. Espera </th>";
                        
                    echo"</tr>";
                echo"</thead>";
                echo"<tbody>";
                    
                echo"</tbody>";
        echo"</table>";
    }
    


    
    /*
    $query = "SELECT * FROM tabla LIMIT $start, $per_page";
    $result = mysqli_query($conn, $query);
    */
    // Mostrar los registros en una tabla HTML
    /* echo "<table>";
    while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['campo1'] . "</td>";
    echo "<td>" . $row['campo2'] . "</td>";
    echo "</tr>";
    }
    echo "</table>"; */



    