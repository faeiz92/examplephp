<?php

require_once('Connections/conPsm.php');
require_once('Connections/conSel.php');
require_once('Connections/conLec.php');

include('base.php');
include('tab_tangguh_berhenti.php');
$idstaf = $_SESSION['MM_nostaf'];

$query_check_jabatan = sprintf("SELECT  kodjabatan FROM cms_sql.kelulusan_tamat_belajar  WHERE nostaf = '%s'", $idstaf);
        $rsCheck_jabatan = mysql_query($query_check_jabatan, $conPsm) or die(mysql_error());
        $row_check_jabatan = mysql_fetch_assoc($rsCheck_jabatan);


 $identify_jab = $row_check_jabatan['kodjabatan'];


?>

<?php startblock('style'); ?>
<?php superblock(); ?>

<script type="text/javascript">

</script>

<style type="text/css">



#table {
  width: 50%;
  counter-reset: row-num;
}
#table tr {
  counter-increment: row-num;
}
#table tr td:first-child::before {
    content: counter(row-num) ". ";
}

</style>

<?php endblock(); ?>

<?php startblock('content'); ?>

<div class="page-heading" >
            <div class="container">
                <div class="col text-center">
                    <h5><b>SEMAK DAN CETAK PERMOHONAN TAMAT PENGAJIAN</b></h>
                </div>
            </div>
<div>
&nbsp

<form action="" method="post"> 
  <div class="container">
    <div class="col text-center">
      Matrik: <input type="text" name="search_matrik" id="search_matrik" class="container" /><br /> 
      <input type="submit" value="SEARCH" name="btn_search" id="btn_search" class="btn btn-primary btn-sm" />       
    </div>
  </div>
</form>

<div class="container">
  <div class="col text-center">
    <p><b>*SETIAP PELAJAR WAJIB MENDAPAT TUJUH (7) KELULUSAN JABATAN</p></h>
  </div>
</div>

<!-- <h4><b><centre></centre></b></h4> -->
<form method="post" action="" id="form-delete">
    <div>
        <table class="table table-bordered" id="tbl_maduan">

           <thead>
            <tr bgcolor="#999999">
                            <th colspan="13" class="text-center">SENARAI PELAJAR MOHON TAMAT BELAJAR</th>
            </tr>
               <tr>
                  <th scope="col">#</th>
                  <th scope="col">MATRIK</th>
                  <th scope="col">NAMA PELAJAR</th>
                  <th scope="col" style="text-align:center">BILANGAN JABATAN YANG MEMBERI STATUS SOKONG / LULUS</th>
                  <th scope="col" style="text-align:center">BILANGAN JABATAN YANG TELAH MENYEMAK</th>
                  <th scope="col" colspan="2" style="text-align:center">CETAK</th>
               </tr>
          </thead>
          <tbody>
            <?php
            

            if(isset($_POST['btn_search']))
            {

              $h = 1;
              $pageno = 0;
              $total_pages = 0;
    
              $carian_matrik = $_POST['search_matrik'];

              $query_maklumat_tamat_belajar = sprintf("select DISTINCT a.MATRIK, b.nama, c.JABATAN_LULUS from cms_sql.permohon_sijil_transkrip a INNER JOIN cms_sql._pelajar b on a.MATRIK = b.matrik INNER JOIN cms_sql.permohon_sijil_transkrip_approval c on a.MATRIK = c.MATRIK where a.MATRIK = '$carian_matrik' GROUP BY  c.MATRIK");
            }

            else 
            {

              if (isset($_GET['pageno']))
               {
                  $pageno = $_GET['pageno'];
                             
               } 
               else 
               {

                  $pageno = 1;
                                  
               }

               

              $no_of_records_per_page = 20;
              $offset = ($pageno-1) * $no_of_records_per_page;

              $h = $offset;
                      

              $totalPage = sprintf("select  count(MATRIK) as pelajarMatrik from cms_sql.permohon_sijil_transkrip");


              $retTotalPage = mysql_query($totalPage);

              $total_rows = mysql_fetch_array($retTotalPage)[0];

              $total_pages = ceil($total_rows / $no_of_records_per_page);
              

              $query_maklumat_tamat_belajar = sprintf("select DISTINCT a.MATRIK, b.nama, c.JABATAN_LULUS from cms_sql.permohon_sijil_transkrip a INNER JOIN cms_sql._pelajar b on a.MATRIK = b.matrik INNER JOIN cms_sql.permohon_sijil_transkrip_approval c on a.MATRIK = c.MATRIK GROUP BY c.MATRIK ORDER BY a.TARIKH_MOHON ASC LIMIT $offset, $no_of_records_per_page");
             
            }


            
            $ret_tamat_belajar = mysql_query($query_maklumat_tamat_belajar);
            $h = $h + 1;
            while ($row_tamat_belajar = mysql_fetch_assoc($ret_tamat_belajar)) {
               $i = $h++;

              $matrik = $row_tamat_belajar['MATRIK'];

              //echo $row_tamat_belajar['JABATAN_LULUS'];
              

              $query_total_kelulusan = sprintf("select count(MATRIK) as bil_kelulusan from cms_sql.permohon_sijil_transkrip_approval where MATRIK = '$matrik' and STATUS_APP = 'Y'");
               $ret_total_kelulusan = mysql_query($query_total_kelulusan);
               $row_total_kelulusan = mysql_fetch_assoc($ret_total_kelulusan);

               $query_total_review = sprintf("select count(MATRIK) as bil_kelulusan from cms_sql.permohon_sijil_transkrip_approval where MATRIK = '$matrik' and (STATUS_APP = 'Y' OR STATUS_APP = 'N')");
               $ret_total_review = mysql_query($query_total_review);
               $row_total_review = mysql_fetch_assoc($ret_total_review);

            ?>

            
            <tr>
              <th scope="row"><?= $i; ?></th>
              <td><a href="?modul=HEA&slug=mycms-app-tamat-belajar-jabatan-validation-tamat-belajar&matrik=<?= $row_tamat_belajar['MATRIK'] ?>&jabatan=<?= $identify_jab?>&nostaf=<?= $idstaf?>" target="_blank"><?= $row_tamat_belajar['MATRIK']?></a></td>
              <td><?= $row_tamat_belajar['nama']?></td>
              <td><?= $row_total_kelulusan['bil_kelulusan']?></td>
              <td><?=$row_total_review['bil_kelulusan']?></td>
              <td><a href="?modul=HEA&slug=hea-approve-tamat-belajar-borang-rujukan-staf-tamat-pengajian&matrik=<?= $row_tamat_belajar['MATRIK'] ?>" target="_blank" class="btn btn-sm">RUJUKAN JABATAN</a>
              </td>
              <td><a href="?modul=HEA&slug=hea-approve-tamat-belajar-borang-kelulusan-pelajar-tamat-pengajian&matrik=<?= $row_tamat_belajar['MATRIK'] ?>" target="_blank" class="btn btn-sm">SURAT PELAJAR</a>
              </td>
            </tr>

          <?php } ?>
            
          </tbody>
        </table>
    </div>
</form>

<ul class="pagination">   
        <li><a href="?modul=HEA&slug=hea-approve-tamat-belajar-semak-tamat-belajar&pageno=1">First</a></li>
        <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
            <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?modul=HEA&slug=hea-approve-tamat-belajar-semak-tamat-belajar&pageno=".($pageno - 1); } ?>">Prev</a>
        </li>
        <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
            <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?modul=HEA&slug=hea-approve-tamat-belajar-semak-tamat-belajar&pageno=".($pageno + 1); } ?>">Next</a>
        </li>
        <li><a href="?modul=HEA&slug=hea-approve-tamat-belajar-semak-tamat-belajar&pageno=<?php echo $total_pages; ?>">Last</a></li>
</ul>

<?php endblock(); ?>

   


 
