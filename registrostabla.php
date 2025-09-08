<?php
    include("conexion.php");
    $con=conectar();
    if(!$con) {     
        echo"Error al conectar a la base de datos"; 
        exit();               
    }
    // Recibo el contador y lo convierto a entero
    $contador = isset($_POST['contador']) ? (int)$_POST['contador'] : 1;
    $cantFilas=0;
    //1.- Obtengo la cantidad de Registros
    $sqlCantRows = "Select IsNull(Count(*),0) Cantidad From Vi_Guias_CMP_Completa";
    $resultCantRows=sqlsrv_query($con,$sqlCantRows);
    if($resultCantRows) {
        while($mostrarCantRows=sqlsrv_fetch_array($resultCantRows)){
            $cantFilas=$mostrarCantRows['Cantidad'];
        }
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
            $desde  =   (($pagina-1)*$per_page)+($pagina-1);

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
            echo "<a href='#' id='play-paginacion' class='btn-control-paginacion'><i class='fas fa-play'></i></a>";
            echo "<a href='#' id='pause-paginacion' class='btn-control-paginacion'><i class='fas fa-pause'></i></a>";
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

        $sql="  Select  *                                                                                                                                       
                From Vi_Guias_CMP_Completa 
                Order By Status,NroGuia
                OFFSET $desde ROWS 
                FETCH NEXT $per_page ROWS ONLY";
        $result=sqlsrv_query($con,$sql);
        echo"<table>";
                echo"<thead>";
                    echo"<tr>";
                        echo"<th colspan='7' class='border-right-delimiter'>PROGRAMA PESCA</th>";
                        /* echo"<th colspan='1' class='border-right-delimiter'>MUELLE</th>"; */
                        echo"<th colspan='4' class='border-right-delimiter'>GRANJA</th>";                        
                        echo"<th colspan='4' class='border-right-delimiter'>PLANTA</th>";                        
                        echo"<th rowspan='1'></th>";                        
                    echo"</tr>";
                    echo"<tr>";
                        echo"<th class='ancho_celdas_normales'>GPS</th>";
                        echo"<th class='ancho_celdas_normales'>  TP     </th>";
                        echo"<th class='ancho_celdas_normales'> N° PESCA       </th>";
                        echo"<th class='ancho_celdas_normales'> N° GUÍA             </th>";
                        echo"<th class='ancho_celdas_normales'> GRANJA          </th>";
                        echo"<th class='ancho_celdas_normales'> SALIDA REAL   </th>";
                        echo"<th class='ancho_celdas_normales border-right-delimiter'> SALIDA MUELLE</th>";
                        echo"<th class='ancho_celdas_normales'> PROGRAMADA     </th>";
                        echo"<th class='ancho_celdas_normales'> ESTIMADA   </th>";
                        echo"<th class='ancho_celdas_normales'> LLEGADA   </th>";
                        echo"<th class='ancho_celdas_normales border-right-delimiter'>SALIDA </th>";
                        echo"<th class='ancho_celdas_normales'> ESTIMADA </th>";
                        echo"<th class='ancho_celdas_normales'> KG REM. </th>";
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
                        $totKgRem           =   $mostrar['TotalKilosRemitidos'];
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
                            echo"<td>".$mostrar['FechaSalidaMuelleTexto']."</td>"; //Salida Muelle
                            echo"<td>".$mostrar['FecProgTexto']."</td>"; //Fec. Programada
                            echo"<td>".$mostrar['fechaEstimadallegadaCamaroneraNewTexto']."</td>"; //Fecha Estimada Llegada Camaronera
                            /* echo"<td>".$mostrar['FechaEstimadaLlegadaCamaroneraCalcTexto']."</td>"; //Fecha Estimada Llegada Camaronera */
                            echo"<td>".$mostrar['FecLlegCamTexto']."</td>";//Llegó a Granja
                            echo"<td>".$mostrar['FechaCamaroneraPlantaTexto']."</td>";//Salió de Granja
                            echo"<td>".$mostrar['fechaEstimadaLlegadaPlantaNewTexto']."</td>"; //Fecha Estimada LLegada Planta
                            /* echo"<td>".$mostrar['FechaEstimadaLlegadaPlantaCalcTexto']."</td>";//Fecha Estimada Llegada Planta */
                            echo"<td>".number_format($totKgRem, 0, '.', ',')."</td>";//Kilos Remitidos
                            echo"<td>".$mostrar['FechaRealLlegadaTexto']."</td>";//Llegó a Planta 
                            echo"<td>".$mostrar['FechaProgramadaLlegadaTexto']."</td>"; //Fec. Prog. Lleg.
                            if ($status  =='4'){
                                echo"<td class='status'><span class='haciacamaronera'>Ruta a Granja</span></td>";  
                            }
                            if ($status  =='3'){
                                echo"<td class='status'><span class='llegocamaronera'>En Granja</span></td>";  
                            }
                            if ($status  =='2'){
                                echo"<td class='status'><span class='haciaplanta'>Ruta a Planta</span></td>";  
                            }
                            if ($status  =='1'){
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
                        echo"<th colspan='4' class='border-right-delimiter'>PLANTA</th>";                        
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
                        echo"<th class='ancho_celdas_normales'> KG REM. </th>";
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