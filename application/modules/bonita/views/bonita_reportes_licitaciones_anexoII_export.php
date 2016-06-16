    <?php   

   /// Exporta el archivo a Excell - En {filename} va armada la tabla a exportar.

    header("Content-Description: File Transfer");
    header("Content-type: application/x-msexcel" ); 
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=".$new_filename);
    header("Content-Description: PHP Generated XLS Data" );                
    header("Content-type: text/html; charset=utf-8" ); 
 ?>

<H3 colspan="5">REPORTES LICITACIONES CERRADAS</H3>
    <table class="table">
        <tr>
            <td>RESOLUCI&#211;N:</td>
            <td>FECHA DE LICITACI&#211;N:</td>
            <td>FECHA DE CIERRE:</td>
            <td>CUPO M&#193;XIMO:</td>
            <td>M&#193;XIMO POR ENTIDAD FINANCIERA:</td>
        </tr>
        {datos_licitacion}
    </table>
    <table class="table">
        <tr>
            <td>N&#186;:</td>
            <td>ENTDIDAD FINANCIERA:</td>
            <td>MONTO ADJUDICADO:</td>
            <td>% DE ADJUDICACI&#211;N:</td>
        </tr>
        {lista_ofertas}
    </table>
    <button id="exportar">
        Exportar
    </button>