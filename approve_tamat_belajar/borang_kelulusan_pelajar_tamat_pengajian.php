<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL ^ E_DEPRECATED);

while (ob_get_level())
ob_end_clean();
header('Content-Encoding: None', true);

define('FPDF_FONTPATH',PROJECT_PATH.'/psm/rekrut/print/font/');

require('print/fpdf.php'); 
require_once('Connections/conPsm.php');
require_once('Connections/conSel.php');
require_once('Connections/conLec.php');
require_once('class_islamic_calendar.php');


$matrik_pljr = $_GET['matrik'];
/**************************************dapatkan id student*******************************************************/
$query_getidstud = sprintf("SELECT a.idstud FROM cms_sql._pelajar a WHERE matrik = '%s'", $matrik_pljr);
        $rs_getidstud = mysql_query($query_getidstud, $conPsm) or die(mysql_error());
        $row_getidstud = mysql_fetch_assoc($rs_getidstud);

        $idstud = $row_getidstud['idstud'];

/***********************************calling class for get islamic calendar*****************************************/
$tahun = date("Y");
$bulan = date("m");
$hari = date("d");

$time = date("h:i:sa");

 $acc_class_DC = new Date_conv();
 $jul_hijri = $acc_class_DC -> gregorianToHijri($tahun, $bulan, $hari);



$combine_date = '';
foreach ($jul_hijri as $key => $value) 
{
   $combine_date .= $value;
}


$get_tahun = substr($combine_date,0,4);
$get_bulan = substr($combine_date,4,2);
$get_hari = substr($combine_date,6,2);

$combine_format_date = $get_hari.'/'.$get_bulan.'/'.$get_tahun;

$acc_class_HD = new Hijri_date_id();

$result_date = $acc_class_HD -> date($combine_format_date);


/***************************************determine month******************************************************/

$case_month = $bulan;


   if($case_month == "01")
  {
     $case_month = "Januari";
  }
  else if($case_month == "02")
  {
     $case_month = "Febuari";
  }
  else if($case_month == "03")
  {
     $case_month = "Mac";
  }
  else if($case_month == "04")
  {
     $case_month = "April";
  }
  else if($case_month == "05")
  {
     $case_month = "Mei";
  }
  else if($case_month == "06")
  {
     $case_month = "Jun";
  }
  else if($case_month == "07")
  {
     $case_month = "Julai";
  }
  else if($case_month === "08")
  {
     $case_month = "Ogos";
  }
  else if($case_month == "09")
  {
     $case_month = "September";
  }
  else if($case_month == "10")
  {
     $case_month = "Oktober";
  }
  else if($case_month == "11")
  {
     $case_month = "November";
  }
  else if($case_month == "12")
  {
     $case_month = "Disember";
  }
  else
  
    $case_month = "Bulan Error";
  

#QUERY MAKLUMAT PEMOHON
$query_staf = sprintf("SELECT p.idstud, p.matrik, p.nama, p.kp_pass, p.kod_prog, p.almt1_pljr, p.pos_pljr, p.pos_pljr,  n.negeri_nama ,p.nohp_pljr, p.status, p.sesisem_daftar, p.tarikh_senat, pr.namaprog_bm, sp.keterangan, ps.sem, ps.sesi_sem, tb.JENIS_PERMOHONAN_TS, CASE WHEN tb.JENIS_PERMOHONAN_TS = 'TS' then 'MEMOHON' ELSE 'TIDAK MEMOHON' END AS jpTS, tb.JENIS_PERMOHONAN_ST, CASE WHEN tb.JENIS_PERMOHONAN_ST = 'ST' then 'MEMOHON' ELSE 'TIDAK MEMOHON' END AS jpST, tb.CARA_TUNTUTAN, CASE when tb.CARA_TUNTUTAN = 'POS' then 'POS KE ALAMAT SURAT MENYURAT' when tb.CARA_TUNTUTAN = 'KUP' then 'AMBIL DI KAUNTER UNIT PEPERIKSAAN' END AS cara_ambil, tb.NO_HP, tb.EMAIL, tb.TARIKH_MOHON
    FROM cms_sql._pelajar p
    LEFT JOIN cms_sql.status_pelajar sp ON (p.status = sp.status)
    LEFT JOIN cms_sql.pljr_sem ps ON (p.idstud = ps.idstud)
    LEFT JOIN cms_sql._program pr ON (p.kod_prog = pr.kod_prog)
    LEFT JOIN cms_sql.permohon_sijil_transkrip tb on p.matrik = tb.MATRIK
    INNER JOIN cms_sql.negeri n on p.negeri_pljr = n.negeri_id
    WHERE tb.idstud = '%s'  AND ps.aktif = '1' GROUP BY tb.idstud", 
    $idstud);
$result_profile_stud = mysql_query($query_staf, $conPsm) or die(mysql_error());
$row_pelajar = mysql_fetch_assoc($result_profile_stud);

$getSesi0 = $row_pelajar['sesisem_daftar'];


$getSesi1 = substr($getSesi0, -2, 1);

$removeSesi0 = substr($getSesi0, -3);

if($getSesi1 == 1)
{
  $studyMonth = "Jun";
}

else if($getSesi1 == 2)
{
  $studyMonth = "November";
}

$removeSesi = str_replace($removeSesi0,"", $getSesi0);

$combineRegisStudy = $studyMonth." ".$removeSesi;





/*****************************************determine application student approve*****************************/
$query_total_kelulusan = sprintf("select count(MATRIK) as bil_kelulusan from cms_sql.permohon_sijil_transkrip_approval where MATRIK = '$matrik_pljr'  and STATUS_APP = 'Y'");
               $ret_total_kelulusan = mysql_query($query_total_kelulusan);
               $row_total_kelulusan = mysql_fetch_assoc($ret_total_kelulusan);
$app_app = $row_total_kelulusan['bil_kelulusan'];
/*****************************************determine application student not approve*****************************/
$query_total_lulus_xlulus = sprintf("select count(MATRIK) as bil_kelulusan from cms_sql.permohon_sijil_transkrip_approval where MATRIK = '$matrik_pljr'  and (STATUS_APP = 'Y' OR STATUS_APP = 'N')");
               $ret_total_lulus_xlulus = mysql_query($query_total_lulus_xlulus);
               $row_total_lulus_xlulus = mysql_fetch_assoc($ret_total_lulus_xlulus);

$app_disapp = $row_total_lulus_xlulus['bil_kelulusan'];
/**************************************************sort date*******************************************************/
$originalDate = $row_pelajar['TARIKH_MOHON'];
$sort_date = date("d-m-Y", strtotime($originalDate));
/******************************************dapatkan alamat semasa student******************************************/
$query_alamat_semasa = sprintf("SELECT * FROM cms_sql.alamatsemasa  WHERE idstud = '%s'",$idstud);
        
        $rsalamat_semasa= mysql_query($query_alamat_semasa, $conLec) or die(mysql_error());
        $row_alamat_semasa = mysql_fetch_assoc($rsalamat_semasa);

$query_rsBandar = sprintf("SELECT bandar.bandar_nama FROM cms_select.bandar WHERE bandar.bandar_id = '%s'", $row_alamat_semasa['bandar']);
            $rsBandar = mysql_query($query_rsBandar, $conSel) or die(mysql_error());
            $rowBandar = mysql_fetch_assoc($rsBandar);


$query_rsNegeri = sprintf("SELECT negeri.negeri_nama FROM cms_select.negeri WHERE negeri.negeri_id = '%s'", $row_alamat_semasa['negeri']);
            $rsNegeri = mysql_query($query_rsNegeri, $conSel) or die(mysql_error());
            $rowNegeri = mysql_fetch_assoc($rsNegeri);

$alamat1_student = $row_alamat_semasa['alamat1'];


    $alamat_student1 = $alamat1_student;  
    $alamat_student2 = $row_alamat_semasa['alamat2'];
    $bandar_student  = $rowBandar['bandar_nama'];
    $poskod_student  = $row_alamat_semasa['poskod'];
    $negeri_student  = $rowNegeri['negeri_nama'];

/*************************************************************************************************************/


$pdf = new FPDF('P', 'mm', 'A4');
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();

# row 1
$pdf->SetFont('Arial', 'B', 9);
$pdf->Image('media/img/letterhead_kias_2014.jpg', 7, 7, 200, 'C');
$pdf->Ln(38);
$pdf->Cell(0, 0, '___________________________________________________________________________________________________________', 0, 1, 'C', false);
$pdf->Ln(5);


$pdf->SetFont('Arial','B',12);
$pdf->Cell(40,10,'KIAS/HEA/0.6/05/9-5');
$pdf->Ln(7);


$pdf->Multicell(150,4,$result_date.' / '.$hari.' '.$case_month.' '.$tahun.'M',0,1);
$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(40,10,'Kepada:');
$pdf->Ln(7);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(40,10,'SESIAPA YANG BERKENAAN');
$pdf->Ln(7);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(40,10,'Tuan/Puan');
$pdf->Ln(7);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(40,10,'PENGESAHAN TAMAT BELAJAR');
$pdf->Ln(7);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(40,10,'Dengan hormatnya saya merujuk kepada perkara di atas.');
$pdf->Ln(15);

$pdf->SetFont('Arial','',12);
$pdf->Multicell(0,5,'Adalah diakui dan disahkan bahawa penama di bawah merupakan seorang pelajar yang berdaftar di Kolej Islam Antarabangsa Sultan Ismail Petra. Butiran penama adalah seperti berikut:',0,10);
$pdf->Ln(7);



$pdf->SetFont('Arial','B');

$pdf->SetX(40);
$pdf->Multicell(0,5,'Nama : '.$row_pelajar['nama'],0,5);
$pdf->Ln(3);

$pdf->SetFont('Arial','B');
$pdf->SetX(40);
$pdf->Multicell(0,5,'No. K/P : '.$row_pelajar['kp_pass'],0,5);
$pdf->Ln(3);

$pdf->SetFont('Arial','B');
$pdf->SetX(40);
$pdf->Multicell(0,5,'No. Matrik : '.$row_pelajar['matrik'],0,5);
$pdf->Ln(3);

$pdf->SetFont('Arial','B');
$pdf->SetX(40);
$pdf->Multicell(0,5,'Program :'.$row_pelajar['namaprog_bm'],0,5);
$pdf->Ln(3);

$pdf->SetFont('Arial','B');
$pdf->SetX(40);
$pdf->Multicell(0,5,'Tarikh Mula Belajar : '.$combineRegisStudy,0,5);
//$pdf->Multicell(100,0,'Tarikh Mula Belajar :'.$row_pelajar['namaprog_bm'],0,10,'L');
$pdf->Ln(5);

$programName = $row_pelajar['namaprog_bm'];

$tarikh_senat1 = $row_pelajar['tarikh_senat'];
$tarikh_senat = date("d-m-Y", strtotime($tarikh_senat1));

$x = $pdf->GetX();
$y = $pdf->GetY();


$pdf->SetFont('Arial','',12);
$pdf->Multicell(0,5,'Sukacita juga dimaklumkan bahawa pelajar di atas telah menyempurnakan syarat-syarat untuk bergraduat dalam bidang '.$programName. ', iaitu sebagaimana keputusan Mesyuarat Senat KIAS pada '.$tarikh_senat.'.',0,5);
$pdf->Ln(7);



$pdf->SetFont('Arial');
$pdf->Multicell(150,4,'Sekian, terima kasih.',0,1);
$pdf->Ln(5);

$pdf->SetFont('Arial');
$pdf->Multicell(150,4,'Yang benar,',0,1);
$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);
$pdf->Multicell(150,4,'NORIZAN BINTI ISMAIL',0,1);
$pdf->Ln(1);
$pdf->SetFont('Arial');
$pdf->Multicell(150,4,'Timbalan Pendaftar',0,1);

$pdf->Ln(10);

$pdf->SetFont('Arial','I',12);
$pdf->Cell(0,11,'*Ini adalah cetakan berkomputer, tandatangan tidak diperlukan*',0,0,'C');


$pdf->Output('Surat Tamat Belajar'.$tahun.'_'.$matrik_pljr.'.pdf', 'I');

?>