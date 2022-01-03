<?php

require_once('Connections/conPsm.php');
require_once('Connections/conSel.php');
require_once('Connections/conLec.php');

$idstaf = $_SESSION['MM_nostaf'];
$tarikh_isi = date("Y/m/d");

$query_get_name = "SELECT staf_peribadi.nama FROM cms_psm.staf_peribadi WHERE staf_peribadi.nostaf = '$idstaf'";
        $result_get_name = mysql_query($query_get_name, $conPsm) or die(mysql_error());
        $result_get_name = mysql_fetch_assoc($result_get_name);

$get_namastaf = $result_get_name['nama'];


if(isset($_POST["btnSubmit"]))
{
    $nostaf = $_POST['nostaf'];
    $kodjabatan_individu = $_POST['nama_jab_individu'];
   
      $insert_kelulusan_tamat_belajar = "INSERT INTO cms_sql.kelulusan_tamat_belajar 
                    (nostaf, kodjabatan) 
                    VALUES ('$nostaf','$kodjabatan_individu')";
     mysql_query($insert_kelulusan_tamat_belajar, $conPsm) or die(mysql_error());
    
}

if(isset($_POST['btn_del'])) 
{

  $nostaf = $_POST['check_nostaf'];
  $kod_jabatan = $_POST['ret_kod_jabatan'];


  for($i=0;$i<count($kod_jabatan);$i++)
  {
    $del_nostaf = $nostaf[$i];
    $del_kod_jabatan = $kod_jabatan[$i];

    //echo $del_nostaf.' '.$del_kod_jabatan; exit();

    $delete_kelulusan_tamat_belajar = "DELETE from cms_sql.kelulusan_tamat_belajar 
                    where nostaf = '$del_nostaf' and kodjabatan = '$del_kod_jabatan'";
     mysql_query($delete_kelulusan_tamat_belajar, $conPsm) or die(mysql_error());
  }

 
}


include('base.php');

?>

<script type="text/javascript">


</script>

<?php startblock('script'); ?> 
<?php superblock(); ?>

<?php endblock(); ?>


<?php startblock('style'); ?>
<?php superblock(); ?>
<style type="text/css">
.input-sm {
    height: 30px;
    padding: 5px 10px;
    font-size: 12px;
    line-height: 1.5;
    border-radius: 3px;
}

.input-upload-sm {
    height: 25px;
    padding: 5px 5px 30px;
    font-size: 12px;
    line-height: 1.5;
    border-radius: 3px;
}

select.input-sm {
    height: 30px;
    line-height: 30px;
}

textarea.input-sm, 
select[multiple].input-sm {
    height: auto;
}

.no-border td, 
.no-border th {
    border: none !important;
}
</style>

<script type="text/javascript">

$(document).ready(function() {
    $('#selectcheck').click(function() {
        var checked = this.checked;
        $('input[name="check[]"]').each(function() {
        this.checked = checked;
    });
    })
});


$(document).ready(function() {
    $('#selectcheck2').click(function() {
        var checked = this.checked;
        $('input[name="ret_kod_jabatan[]"]').each(function() {
        this.checked = checked;
    });
    })
});



$(document).ready(function()
    { 
     

      $('#btnSubmit').click(function()
      {  
        
         var selectName = $('#nostaf').val();

         if (selectName === "") 
         {
            alert("SILA PILIH NAMA KELULUSAN!");
            return false;
         }

         var selectJabatan = $('#nama_jab_individu').val();

         if (selectJabatan === "") 
         {
            alert("SILA PILIH NAMA JABATAN!");
            return false;
         }
      });
    });

   
    

</script>


<?php endblock(); ?>

<?php startblock('content'); ?>

<form method="post"  enctype="multipart/form-data" class="form-horizontal" id="frmtamatbelajar" name="frmtamatbelajar">
    <fieldset>
        <div class="container">
          <div class="row">
            <div class="col text-center">
              <h3 class="fa fa-pencil-square-o"><legend><b>Nama & Kod Jabatan Kelulusan Tamat Belajar</b></legend></h3>
            </div>
          </div>
        </div>

          <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">Nama</label>
            <?php $query_namastaf = "select nama, nostaf from cms_psm.staf_peribadi where status = 'A'  order by nama ASC";
                            $ret_namastaf = mysql_query($query_namastaf);?>
            <div class="col-sm-10">
              <select  id="nostaf" name="nostaf" class="form-control">
                <option value="">Pilih Nama</option>
                <?php while ($rownamastaf = mysql_fetch_assoc($ret_namastaf)) { ?>s
                <option value="<?= $rownamastaf['nostaf'] ?>" <?php if (isset($_GET['nama']) && ($_GET['nama'] == $rownamastaf['nostaf'])) {
                                    echo "selected='selected'";
                                }?> > <?= $rownamastaf['nama'] ?> </option>
                            <?php } ?>
              </select>
            </div>
          </div>


          <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">Jabatan Semasa Individu</label>
            <div class="col-sm-10">
              <select id="nama_jab_individu" name="nama_jab_individu" class="form-control">
                <option value="">Pilih Kod Jabatan</option>
                <?php $kodjabatan = array("AKADEMIK"=>'AKD',"PEPERIKSAAN"=>'EXAM', "PERPUSTAKAAN"=>'LIB', "KEWANGAN"=>'KEWG', "PEJABAT PENDAFTAR"=>'TP', "HAL EHWAL PELAJAR"=>'HEP-A'); ?>
                <?php foreach($kodjabatan as $nama_kodjabatan => $key2){ ?>
                <option value="<?= $key2 ?>"> <?php echo $nama_kodjabatan  ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

    </fieldset>

    <div>
        <div class="container">
          <div class="row">
            <div class="col text-center">
              <button class="btn btn-success" name="btnSubmit" id="btnSubmit">Simpan</button>
            </div>
          </div>
    </div>

</form>
&nbsp
<form method="post" action="" id="">
    <div>
        <table class="table table-bordered" id="tbl_maduan">

           <thead>
            <tr bgcolor="#999999">
              <th colspan="13" class="text-center">SENARAI NAMA KELULUSAN PELAJAR PERMOHONAN TAMAT BELAJAR</th>
            </tr>
               <tr>
                  <th scope="col">#</th>
                  <th scope="col">NAMA</th>
                  <th scope="col">JABATAN</th>
                  <th scope="col"><input type="checkbox" id="selectcheck2" name="" value=""></th>
                  
                </tr>
          </thead>
          <tbody>
            <?php

            $query_jab_kelulusan = sprintf("SELECT a.nostaf, a.kodjabatan, b.nama
                FROM cms_sql.kelulusan_tamat_belajar a 
                LEFT JOIN cms_psm.staf_peribadi b ON a.nostaf = b.nostaf");
            $ret_jab_kelulusan = mysql_query($query_jab_kelulusan);
            $i = 0;
            while ($row_jab_kelulusan = mysql_fetch_assoc($ret_jab_kelulusan) ) {
              $i++;

            ?>
            <tr>
              <th scope="row"><?= $i; ?></th>
              <td><?= $row_jab_kelulusan['nama']?></td>
              <td><?= $row_jab_kelulusan['kodjabatan']?></td>
              <input type="hidden" id="check_nostaf" name="check_nostaf[]" value="<?= $row_jab_kelulusan['nostaf']?>">
              <td>
                <input type="checkbox" id="ret_kod_jabatan" name="ret_kod_jabatan[]" value="<?= $row_jab_kelulusan['kodjabatan']?>">
              </td>
            </tr>

            <?php } ?>
            
          </tbody>
        </table>
    </div>

    <div class="container">
      <div class="row">
        <div class="col text-center">
          <p align="center"><button type="submit" class="btn btn-danger btn-sm" name='btn_del' value="">PADAM</button></p>
        </div>
      </div>
    </div>
</form>


<?php endblock(); ?>