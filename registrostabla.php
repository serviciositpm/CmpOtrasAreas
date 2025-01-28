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
    $sqlCantRows = "Select IsNull(Count(*),0) Cantidad From Vi_Guias_CMP";
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
            $desde  =   (($pagina*($per_page-1))-$per_page)+2;//+1

        }else{
            //$desde  =   $contador-1;
            $desde  =   0;
        }
        if($numeroPaginas==1){
            $pagina =   1;
            $desde  =   0;
        }
        if ($pagina>$numeroPaginas){
            $pagina =   1;
            $desde  =   0;
        }
        /*
        echo "Desde ->".$desde;
        echo "          Cant Filas   ->".$per_page;
        */
        echo"<div class='titulo_tabla_dash'>";
            echo"<h2>Detalle Guías de Pesca CMP </h2>";
            echo"<h2 class='titulo_tabla_page'>Pág ".$pagina." De ".$numeroPaginas."</h2>";
            echo "<input type='hidden' id='numeroPagi' name='numeroPagi' value='$numeroPaginas'>";
        echo"</div>";
        // 6. Mostrar controles de paginación
        echo "<div id='paginas' class='titulo_tabla_dash paginacion'>";
        if ($pagina > 1) {
            echo "<a href=# id='anterior'>Anterior</a>";
        }
        for ($i = 1; $i <= $numeroPaginas; $i++) {
            echo "<a href=# id='$i'>" . ($i == $pagina ? "<strong>$i</strong>" : $i) . "</a>";
        }
        if ($pagina < $numeroPaginas) {
            echo "<a href=# id='siguiente'>Siguiente</a>";
        }
    echo "</div>";

        $sql="  Select  *                                                                                                                                       ,
                        Case 
                            When horaEstCamara Is Not Null And horaEstCamara <> '' And FechaLlegadaCamaronera Is Not Null And FechaLlegadaCamaronera <> ''
                                Then DateDiff(Minute,Cast(horaEstCamara As DateTime),Cast(FechaLlegadaCamaronera As DateTime))
                            Else 999999
                        End AS DifMinutosSem1                                                                                                                   ,
                        Case 
                            When FechaProgramadaLlegada Is Not Null And FechaProgramadaLlegada <> '' And FechaRealLlegada Is Not Null And FechaRealLlegada <> ''
                                Then DateDiff(Minute,Cast(FechaProgramadaLlegada As DateTime),Cast(FechaRealLlegada As DateTime))
                            Else 999999
                        End AS DifMinutosSem2                                                                                                                   ,
                        '' 'Hielo'                                                                                                                              ,
                        Case 
                            When Ltrim(Rtrim(horaEstCamara)) = '' 
                                Then '' 
                            Else	Format(Cast(Substring(horaEstCamara, 1, 10) As Date), 'dd ', 'es-ES') 
                                +	Upper(Left(Format(Cast(Substring(horaEstCamara, 1, 10) As Date), 'MMM', 'es-ES'), 1)) 
                                +	Lower(Substring(Format(Cast(Substring(horaEstCamara, 1, 10) As Date), 'MMM', 'es-ES'), 2, 2)) 
                                +	' ' 
                                +	Substring(horaEstCamara, 12, 5)

                        End As FecProgTexto                                                                                                                    ,
                        Case 
                            When Ltrim(Rtrim(FechaSalidaPlanta)) = '' 
                                Then '' 
                            Else	Format(Cast(Substring(FechaSalidaPlanta, 1, 10) As Date), 'dd ', 'es-ES') 
                                +	Upper(Left(Format(Cast(Substring(FechaSalidaPlanta, 1, 10) As Date), 'MMM', 'es-ES'), 1)) 
                                +	Lower(Substring(Format(Cast(Substring(FechaSalidaPlanta, 1, 10) As Date), 'MMM', 'es-ES'), 2, 2)) 
                                +	' ' 
                                +	Substring(FechaSalidaPlanta, 12, 5)

                        End As FecSalPlaTexto                                                                                                                   , 
                        Case 
                            When Ltrim(Rtrim(FechaLlegadaHielera)) = '' 
                                Then '' 
                            Else	Format(Cast(Substring(FechaLlegadaHielera, 1, 10) As Date), 'dd ', 'es-ES') 
                                +	Upper(Left(Format(Cast(Substring(FechaLlegadaHielera, 1, 10) As Date), 'MMM', 'es-ES'), 1)) 
                                +	Lower(Substring(Format(Cast(Substring(FechaLlegadaHielera, 1, 10) As Date), 'MMM', 'es-ES'), 2, 2)) 
                                +	' ' 
                                +	Substring(FechaLlegadaHielera, 12, 5)
                        End As FecLlegHieTexto                                                                                                                    ,
                        Case 
                            When Ltrim(Rtrim(FechaSalidaHielera)) = '' 
                                Then '' 
                            Else	Format(Cast(Substring(FechaSalidaHielera, 1, 10) As Date), 'dd ', 'es-ES') 
                                +	Upper(Left(Format(Cast(Substring(FechaSalidaHielera, 1, 10) As Date), 'MMM', 'es-ES'), 1)) 
                                +	Lower(Substring(Format(Cast(Substring(FechaSalidaHielera, 1, 10) As Date), 'MMM', 'es-ES'), 2, 2)) 
                                +	' ' 
                                +	Substring(FechaSalidaHielera, 12, 5)
                        End As FecSalHieTexto                                                                                                                    ,
                        Case 
                            When Ltrim(Rtrim(FechaLlegadaCamaronera)) = '' 
                                Then '' 
                            Else	Format(Cast(Substring(FechaLlegadaCamaronera, 1, 10) As Date), 'dd ', 'es-ES') 
                                +	Upper(Left(Format(Cast(Substring(FechaLlegadaCamaronera, 1, 10) As Date), 'MMM', 'es-ES'), 1)) 
                                +	Lower(Substring(Format(Cast(Substring(FechaLlegadaCamaronera, 1, 10) As Date), 'MMM', 'es-ES'), 2, 2)) 
                                +	' ' 
                                +	Substring(FechaLlegadaCamaronera, 12, 5)
                            
                        End As FecLlegCamTexto                                                                                                                     ,
                        Case 
                            When Ltrim(Rtrim(InicioPesca)) = '' 
                                Then '' 
                            Else	Format(Cast(Substring(InicioPesca, 1, 10) As Date), 'dd ', 'es-ES') 
                                +	Upper(Left(Format(Cast(Substring(InicioPesca, 1, 10) As Date), 'MMM', 'es-ES'), 1)) 
                                +	Lower(Substring(Format(Cast(Substring(InicioPesca, 1, 10) As Date), 'MMM', 'es-ES'), 2, 2)) 
                                +	' ' 
                                +	Substring(InicioPesca, 12, 5)

                        End As InicioPescaTexto                                                                                                                    ,
                        Case 
                            When Ltrim(Rtrim(FechaMovilListo)) = '' 
                                Then '' 
                            Else	Format(Cast(Substring(FechaMovilListo, 1, 10) As Date), 'dd ', 'es-ES') 
                                +	Upper(Left(Format(Cast(Substring(FechaMovilListo, 1, 10) As Date), 'MMM', 'es-ES'), 1)) 
                                +	Lower(Substring(Format(Cast(Substring(FechaMovilListo, 1, 10) As Date), 'MMM', 'es-ES'), 2, 2)) 
                                +	' ' 
                                +	Substring(FechaMovilListo, 12, 5)

                        End As FechaMovilListoTexto                                                                                                                    ,
                        Case 
                            When Ltrim(Rtrim(FechaCamaroneraPlanta)) = '' 
                                Then '' 
                            Else	Format(Cast(Substring(FechaCamaroneraPlanta, 1, 10) As Date), 'dd ', 'es-ES') 
                                +	Upper(Left(Format(Cast(Substring(FechaCamaroneraPlanta, 1, 10) As Date), 'MMM', 'es-ES'), 1)) 
                                +	Lower(Substring(Format(Cast(Substring(FechaCamaroneraPlanta, 1, 10) As Date), 'MMM', 'es-ES'), 2, 2)) 
                                +	' ' 
                                +	Substring(FechaCamaroneraPlanta, 12, 5)

                        End As FechaCamaroneraPlantaTexto                                                                                                                    ,
                        Case 
                            When Ltrim(Rtrim(FechaRealLlegada)) = '' 
                                Then '' 
                            Else	Format(Cast(Substring(FechaRealLlegada, 1, 10) As Date), 'dd ', 'es-ES') 
                                +	Upper(Left(Format(Cast(Substring(FechaRealLlegada, 1, 10) As Date), 'MMM', 'es-ES'), 1)) 
                                +	Lower(Substring(Format(Cast(Substring(FechaRealLlegada, 1, 10) As Date), 'MMM', 'es-ES'), 2, 2)) 
                                +	' ' 
                                +	Substring(FechaRealLlegada, 12, 5)

                        End As FechaRealLlegadaTexto                                                                                                                           ,         
                        Case 
                            When Ltrim(Rtrim(FechaProgramadaLlegada)) = '' 
								Then '' 
							Else	Format(Cast(Substring(FechaProgramadaLlegada, 1, 10) As Date), 'dd ', 'es-ES') 
								+	Upper(Left(Format(Cast(Substring(FechaProgramadaLlegada, 1, 10) As Date), 'MMM', 'es-ES'), 1)) 
								+	Lower(Substring(Format(Cast(Substring(FechaProgramadaLlegada, 1, 10) As Date), 'MMM', 'es-ES'), 2, 2)) 
								+	' ' 
								+	Substring(FechaProgramadaLlegada, 12, 5)
                        End As FechaProgramadaLlegadaTexto     ,
                        Case 
                            When Ltrim(Rtrim(FechaEstimadaLlegadaPlantaCalc)) = '' 
								Then '' 
							Else	Format(Cast(Substring(FechaEstimadaLlegadaPlantaCalc, 1, 10) As Date), 'dd ', 'es-ES') 
								+	Upper(Left(Format(Cast(Substring(FechaEstimadaLlegadaPlantaCalc, 1, 10) As Date), 'MMM', 'es-ES'), 1)) 
								+	Lower(Substring(Format(Cast(Substring(FechaEstimadaLlegadaPlantaCalc, 1, 10) As Date), 'MMM', 'es-ES'), 2, 2)) 
								+	' ' 
								+	Substring(FechaEstimadaLlegadaPlantaCalc, 12, 5)
                        End As FechaEstimadaLlegadaPlantaCalcTexto     ,
                         Case 
                            When Ltrim(Rtrim(FechaEstimadaLlegadaCamaroneraCalc)) = '' 
								Then '' 
							Else	Format(Cast(Substring(FechaEstimadaLlegadaCamaroneraCalc, 1, 10) As Date), 'dd ', 'es-ES') 
								+	Upper(Left(Format(Cast(Substring(FechaEstimadaLlegadaCamaroneraCalc, 1, 10) As Date), 'MMM', 'es-ES'), 1)) 
								+	Lower(Substring(Format(Cast(Substring(FechaEstimadaLlegadaCamaroneraCalc, 1, 10) As Date), 'MMM', 'es-ES'), 2, 2)) 
								+	' ' 
								+	Substring(FechaEstimadaLlegadaCamaroneraCalc, 12, 5)
                        End As FechaEstimadaLlegadaCamaroneraCalcTexto     ,
                        Case 
                            When	FechaSalidaPlanta<>'' And FechaLlegadaCamaronera='' And FechaMovilListo='' And FechaCamaroneraPlanta='' And FechaRealLlegada=''
                                Then	'3' --Ruta Camaronera
                            --When	FechaSalidaPlanta<>'' And FechaLlegadaCamaronera<>'' And FechaMovilListo='' And FechaCamaroneraPlanta='' And FechaRealLlegada=''
                            When	FechaLlegadaCamaronera<>'' And FechaMovilListo='' And FechaCamaroneraPlanta='' And FechaRealLlegada=''
                                Then	'2' --En Camaronera
                            --When	FechaSalidaPlanta<>'' And FechaLlegadaCamaronera<>'' And FechaMovilListo<>'' And FechaCamaroneraPlanta='' And FechaRealLlegada=''
                            When	FechaLlegadaCamaronera<>'' And FechaMovilListo<>'' And FechaCamaroneraPlanta='' And FechaRealLlegada=''
                                Then	'1' --Ruta Planta
                            --When	FechaSalidaPlanta<>'' And FechaLlegadaCamaronera<>'' And FechaMovilListo<>'' And FechaCamaroneraPlanta<>'' And FechaRealLlegada=''
                            When	FechaCamaroneraPlanta<>'' And FechaRealLlegada=''                            
                                Then	'1' --Ruta Planta
                            --When	FechaSalidaPlanta<>'' And FechaLlegadaCamaronera<>'' And FechaMovilListo<>'' And FechaCamaroneraPlanta<>'' And FechaRealLlegada<>''
                            When	FechaRealLlegada<>''
                                Then	'4' --En Planta
                            Else
                                '5'
                        End 'Status'
                        
                        --'En Ruta' 
                        
                        
                From Vi_Guias_CMP 
                Order By Status,NroGuia
                OFFSET $desde ROWS 
                FETCH NEXT $per_page ROWS ONLY";
        $result=sqlsrv_query($con,$sql);
        echo"<table>";
                echo"<thead>";
                    echo"<tr>";
                        echo"<th colspan='6' class='border-right-delimiter'>PROGRAMA PESCA</th>";
                        echo"<th colspan='4' class='border-right-delimiter'>GRANJA</th>";                        
                        echo"<th colspan='3' class='border-right-delimiter'>PLANTA</th>";                        
                        echo"<th rowspan='1'></th>";                        
                    echo"</tr>";
                    echo"<tr>";
                        echo"<th class='ancho_celdas_normales'>GPS</th>";
                        echo"<th class='ancho_celdas_normales'>  TP     </th>";
                        echo"<th class='ancho_celdas_normales'> N° PESCA       </th>";
                        echo"<th class='ancho_celdas_normales'> N° GUÍA             </th>";
                        echo"<th class='ancho_celdas_normales'> GRANJA          </th>";
                        echo"<th class='ancho_celdas_normales border-right-delimiter'> SALIDA REAL   </th>";
                        echo"<th class='ancho_celdas_normales'> PROGRAMADA     </th>";
                        echo"<th class='ancho_celdas_normales'> ESTIMADA   </th>";
                        echo"<th class='ancho_celdas_normales'> LLEGADA   </th>";
                        echo"<th class='ancho_celdas_normales border-right-delimiter'>SALIDA </th>";
                        echo"<th class='ancho_celdas_normales'> ESTIMADA </th>";
                        echo"<th class='ancho_celdas_normales'> LLEGADA  </th>";
                        echo"<th class='ancho_celdas_normales border-right-delimiter'>PROGRAMADA</th>";
                        echo"<th class='ancho_celdas_normales'> STATUS </th>"; //23
                        
                    echo"</tr>";
                echo"</thead>";
                echo"<tbody>";
                    while($mostrar=sqlsrv_fetch_array($result)){
                        $porcentaje         =   $mostrar['Porcentaje'];
                        $LlegoGranjaPorc    =   $mostrar['LlegoGranjaPorc'];
                        $SalioGranjaPorc    =   $mostrar['SalioGranjaPorc'];
                        $LLegoPlantaPorc    =   $mostrar['LLegoPlantaPorc'];
                        $minutosSemaf1      =   $mostrar['DifMinutosSem1'];
                        $minutosSemaf2      =   $mostrar['DifMinutosSem2'];
                        $status             =   $mostrar['Status'];
                        $tipoPesca          =   $mostrar['TipoPesca'];
                        $tieneGps           =   $mostrar['tienGps'];
                        echo"<tr >";
                            if ($tieneGps==1){
                                echo"<td><i class='fas fa-map-marker-alt gps'></i></td>";
                            }else{
                                echo"<td></i></td>";
                            }
                            if ($tipoPesca==1){
                                echo"<td><i class='fas fa-shrimp camaron'></i></td>";
                            }else{
                                echo"<td></i></td>";
                            }
                        /*  --> */
                            echo"<td>".$mostrar['NroPesca']."</td>";
                            echo"<td>".$mostrar['NroGuia']."</td>";
                            echo"<td>".$mostrar['camaronera']."</td>";
                            echo"<td>".$mostrar['FecSalPlaTexto']."</td>"; //Salió de Planta 
                            echo"<td>".$mostrar['FecProgTexto']."</td>"; //Fec. Programada
                            echo"<td>".$mostrar['FechaEstimadaLlegadaCamaroneraCalcTexto']."</td>"; //Fecha Estimada Llegada Camaronera
                            echo"<td>".$mostrar['FecLlegCamTexto']."</td>";//Llegó a Granja
                            echo"<td>".$mostrar['FechaCamaroneraPlantaTexto']."</td>";//Salió de Granja
                            echo"<td>".$mostrar['FechaEstimadaLlegadaPlantaCalcTexto']."</td>";//Fecha Estimada Llegada Planta
                            echo"<td>".$mostrar['FechaRealLlegadaTexto']."</td>";//Llegó a Planta 
                            echo"<td>".$mostrar['FechaProgramadaLlegadaTexto']."</td>"; //Fec. Prog. Lleg.
                            if ($status  =='3'){
                                echo"<td class='status'><span class='haciacamaronera'>Ruta a Granja</span></td>";  
                            }
                            if ($status  =='2'){
                                echo"<td class='status'><span class='llegocamaronera'>En Granja</span></td>";  
                            }
                            if ($status  =='1'){
                                echo"<td class='status'><span class='haciaplanta'>Ruta a Planta</span></td>";  
                            }
                            if ($status  =='4'){
                                echo"<td class='status'><span class='active'>En Planta</span></td>";  
                            }
                               //12        
                        echo"</tr>";
                            
                    }
                echo"</tbody>";
        echo"</table>";
    }else{
        echo"<div class='titulo_tabla_dash'>";
            echo"<h2>Detalle Guías de Pesca CMP </h2>";
            
        echo"</div>";
        echo"<table>";
                echo"<thead>";
                    echo"<tr>";
                        echo"<th colspan='5' class='border-right-delimiter'>PROGRAMA PESCA</th>";
                        echo"<th colspan='4' class='border-right-delimiter'>GRANJA</th>";                        
                        echo"<th colspan='3' class='border-right-delimiter'>PLANTA</th>";                        
                        echo"<th rowspan='1'></th>";                        
                    echo"</tr>";
                    echo"<tr>";
                        echo"<th class='ancho_celdas_normales'>GPS</th>";
                        echo"<th class='ancho_celdas_normales'>  TP     </th>";
                        echo"<th class='ancho_celdas_normales'> N° PESCA       </th>";
                        echo"<th class='ancho_celdas_normales'> N° GUÍA             </th>";
                        echo"<th class='ancho_celdas_normales'> GRANJA          </th>";
                        echo"<th class='ancho_celdas_normales border-right-delimiter'> SALIDA REAL   </th>";
                        echo"<th class='ancho_celdas_normales'> PROGRAMADA     </th>";
                        echo"<th class='ancho_celdas_normales'> ESTIMADA   </th>";
                        echo"<th class='ancho_celdas_normales'> LLEGADA   </th>";
                        echo"<th class='ancho_celdas_normales border-right-delimiter'>SALIDA </th>";
                        echo"<th class='ancho_celdas_normales'> ESTIMADA </th>";
                        echo"<th class='ancho_celdas_normales'> LLEGADA  </th>";
                        echo"<th class='ancho_celdas_normales border-right-delimiter'>PROGRAMADA</th>";
                        echo"<th class='ancho_celdas_normales'> STATUS </th>"; //23
                        
                    echo"</tr>";
                echo"</thead>";
                echo"<tbody>";
                    
                echo"</tbody>";
        echo"</table>";
    }
    


?>