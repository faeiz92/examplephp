<?php

while (ob_get_level())
ob_end_clean();
header('Content-Encoding: None', true);

define('FPDF_FONTPATH',PROJECT_PATH.'/psm/rekrut/print/font/');

require('print/fpdf.php'); 
require_once('Connections/conPsm.php');
require_once('Connections/conSel.php');
require_once('Connections/conLec.php');


$matrik_pljr = $_GET['matrik'];


/**************************************dapatkan id student*******************************************************/
$query_getidstud = sprintf("SELECT a.idstud FROM cms_sql._pelajar a WHERE matrik = '%s'", $matrik_pljr);
        $rs_getidstud = mysql_query($query_getidstud, $conPsm) or die(mysql_error());
        $row_getidstud = mysql_fetch_assoc($rs_getidstud);

        $idstud = $row_getidstud['idstud'];
/****************************************************************************************************************/

#QUERY MAKLUMAT PEMOHON
$query_staf = sprintf("SELECT p.idstud, p.matrik, p.nama, p.kp_pass, p.kod_prog, p.almt1_pljr, p.pos_pljr, p.pos_pljr,  n.negeri_nama ,p.nohp_pljr, p.status,pr.namaprog_bm, sp.keterangan, ps.sem, ps.sesi_sem, tb.JENIS_PERMOHONAN_TS, CASE WHEN tb.JENIS_PERMOHONAN_TS = 'TS' then 'MEMOHON' ELSE 'TIDAK MEMOHON' END AS jpTS, tb.JENIS_PERMOHONAN_ST, CASE WHEN tb.JENIS_PERMOHONAN_ST = 'ST' then 'MEMOHON' ELSE 'TIDAK MEMOHON' END AS jpST, tb.CARA_TUNTUTAN, CASE when tb.CARA_TUNTUTAN = 'POS' then 'POS KE ALAMAT SURAT MENYURAT' when tb.CARA_TUNTUTAN = 'KUP' then 'AMBIL DI KAUNTER UNIT PEPERIKSAAN' END AS cara_ambil, tb.NO_HP, tb.EMAIL, tb.TARIKH_MOHON
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

$alamat_penuh_pljr = $row_pelajar['almt1_pljr'].' '.$row_pelajar['pos_pljr'].' '.$row_pelajar['negeri_nama'];

/**************************************alamat pelajar***********************************************************/
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


//if ($row_alamat_semasa['alamat2'] != NULL || $row_alamat_semasa['alamat2'] != '') 
//{ 
    $alamat_student1 = $alamat1_student;  
    $alamat_student2 = $row_alamat_semasa['alamat2'];
    $bandar_student  = $rowBandar['bandar_nama'];
    $poskod_student  = $row_alamat_semasa['poskod'];
    $negeri_student  = $rowNegeri['negeri_nama'];
    /***********************************************************************************************************/

$query_akademik = sprintf("SELECT  permohon_sijil_transkrip_approval.NOSTAF, ULASAN, TARIKH_LULUS, STATUS_APP, case when STATUS_APP = 'N' then 'TIDAK DISOKONG' when STATUS_APP = 'Y' then 'DISOKONG' end as STATUS_APP, staf_peribadi.nama FROM cms_sql.permohon_sijil_transkrip_approval
INNER JOIN cms_psm.staf_peribadi on staf_peribadi.nostaf = permohon_sijil_transkrip_approval.NOSTAF where MATRIK = '%s' AND JABATAN_LULUS = 'AKD'", 
    $matrik_pljr);
$result_akademik = mysql_query($query_akademik, $conPsm) or die(mysql_error());
$row_akademik = mysql_fetch_assoc($result_akademik);

$query_pustakawan = sprintf("SELECT  permohon_sijil_transkrip_approval.NOSTAF, ULASAN, TARIKH_LULUS, STATUS_APP, case when STATUS_APP = 'N' then 'TIDAK DISOKONG' when STATUS_APP = 'Y' then 'DISOKONG' end as STATUS_APP, staf_peribadi.nama FROM cms_sql.permohon_sijil_transkrip_approval
INNER JOIN cms_psm.staf_peribadi on staf_peribadi.nostaf = permohon_sijil_transkrip_approval.NOSTAF where MATRIK = '%s' AND JABATAN_LULUS = 'LIB'", 
    $matrik_pljr);
$result_pustakawan = mysql_query($query_pustakawan, $conPsm) or die(mysql_error());
$row_pustakawan = mysql_fetch_assoc($result_pustakawan);

$query_exam = sprintf("SELECT  permohon_sijil_transkrip_approval.NOSTAF, ULASAN, TARIKH_LULUS, STATUS_APP, case when STATUS_APP = 'N' then 'TIDAK DISOKONG' when STATUS_APP = 'Y' then 'DISOKONG' end as STATUS_APP, staf_peribadi.nama FROM cms_sql.permohon_sijil_transkrip_approval
INNER JOIN cms_psm.staf_peribadi on staf_peribadi.nostaf = permohon_sijil_transkrip_approval.NOSTAF where MATRIK = '%s' AND JABATAN_LULUS = 'EXAM'", $matrik_pljr);
$result_exam = mysql_query($query_exam, $conPsm) or die(mysql_error());
$row_exam = mysql_fetch_assoc($result_exam);

$query_hep1 = sprintf("SELECT  permohon_sijil_transkrip_approval.NOSTAF, ULASAN, TARIKH_LULUS, STATUS_APP, case when STATUS_APP = 'N' then 'TIDAK DISOKONG' when STATUS_APP = 'Y' then 'DISOKONG' end as STATUS_APP, staf_peribadi.nama FROM cms_sql.permohon_sijil_transkrip_approval
INNER JOIN cms_psm.staf_peribadi on staf_peribadi.nostaf = permohon_sijil_transkrip_approval.NOSTAF where MATRIK = '%s' AND JABATAN_LULUS = 'HEP-A'", 
    $matrik_pljr);
$result_hep = mysql_query($query_hep1, $conPsm) or die(mysql_error());
$row_hep = mysql_fetch_assoc($result_hep);

$query_kewangan = sprintf("SELECT  permohon_sijil_transkrip_approval.NOSTAF, ULASAN, TARIKH_LULUS, STATUS_APP, case when STATUS_APP = 'N' then 'TIDAK DISOKONG' when STATUS_APP = 'Y' then 'DISOKONG' end as STATUS_APP, staf_peribadi.nama FROM cms_sql.permohon_sijil_transkrip_approval
INNER JOIN cms_psm.staf_peribadi on staf_peribadi.nostaf = permohon_sijil_transkrip_approval.NOSTAF where MATRIK = '%s' AND JABATAN_LULUS = 'KEWG'", 
    $matrik_pljr);
$result_kewangan = mysql_query($query_kewangan, $conPsm) or die(mysql_error());
$row_kewangan = mysql_fetch_assoc($result_kewangan);

$query_tp = sprintf("SELECT  permohon_sijil_transkrip_approval.NOSTAF, ULASAN, TARIKH_LULUS, STATUS_APP, case when STATUS_APP = 'N' then 'TIDAK DISOKONG' when STATUS_APP = 'Y' then 'DISOKONG' end as STATUS_APP, staf_peribadi.nama FROM cms_sql.permohon_sijil_transkrip_approval
INNER JOIN cms_psm.staf_peribadi on staf_peribadi.nostaf = permohon_sijil_transkrip_approval.NOSTAF where MATRIK = '%s' AND JABATAN_LULUS = 'TP'", 
    $matrik_pljr);
$result_tp = mysql_query($query_tp, $conPsm) or die(mysql_error());
$row_tp = mysql_fetch_assoc($result_tp);

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();

# row 1
$pdf->SetFont('Arial', 'B', 9);
$pdf->Image('media/img/letterhead_kias_2014.jpg', 7, 7, 200, 'C');
$pdf->Ln(38);
$pdf->Cell(0, 0, '___________________________________________________________________________________________________________', 0, 1, 'C', false);
$pdf->Ln(8);



$pdf->SetFont('Arial', 'B', 11);


$pdf->Cell(0, 0, 'BORANG PERMOHONAN TAMAT BELAJAR', 0, 1, 'C', false);

$pdf->Ln(10);


# row 3
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 0, 'A. MAKLUMAT PEMOHON', 0, 1, 'L', false);
$pdf->SetFont('Arial', '', 9);
$pdf->Ln(5);
$pdf->SetWidths(array(5, 50, 15, 90));
$pdf->Row(array('', 'NAMA', ':', html_entity_decode($row_pelajar['nama'], ENT_QUOTES)));
$pdf->Row(array('', 'PROGRAM', ':', html_entity_decode($row_pelajar['namaprog_bm'], ENT_QUOTES)));
$pdf->Row(array('', 'NO MATRIK', ':', html_entity_decode($row_pelajar['matrik'], ENT_QUOTES)));
$pdf->Row(array('', 'SESI SEMASA', ':', html_entity_decode($row_pelajar['sesi_sem'], ENT_QUOTES)));
$pdf->Row(array('', 'SEMESTER SEMASA', ':', html_entity_decode($row_pelajar['sem'], ENT_QUOTES)));
$pdf->Row(array('', 'NO. TELEFON', ':', html_entity_decode($row_pelajar['nohp_pljr'], ENT_QUOTES)));
$pdf->Row(array('', 'ALAMAT', ':', html_entity_decode($alamat_student1, ENT_QUOTES)));
$pdf->Row(array('', '', '', html_entity_decode($alamat_student2, ENT_QUOTES)));
$pdf->Row(array('', '', '', html_entity_decode($poskod_student, ENT_QUOTES)));
$pdf->Row(array('', '', '', html_entity_decode($bandar_student, ENT_QUOTES)));
$pdf->Row(array('', '', '', html_entity_decode($negeri_student, ENT_QUOTES)));

$pdf->Ln(5);



#MAKLUMAT PERMOHONAN
# row 3
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 0, 'B. MAKLUMAT PEMOHON', 0, 1, 'L', false);
$pdf->SetFont('Arial', '', 9);
$pdf->Ln(5);
$pdf->SetWidths(array(5, 50, 15, 90));
$pdf->Row(array('', 'TRANSKRIP SEMENTARA', ':', html_entity_decode($row_pelajar['jpTS'], ENT_QUOTES)));
$pdf->Row(array('', 'SURAT TAMAT', ':', html_entity_decode($row_pelajar['jpST'], ENT_QUOTES)));

$pdf->Ln(5);

$exam_Date = $row_exam['TARIKH_LULUS'];
$exam_Date1 = date("d-m-Y", strtotime($exam_Date));

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 0, '1. URUSAN PEPERIKSAAN', 0, 1, 'L', false);
$pdf->SetFont('Arial', '', 9);
$pdf->Ln(5);
$pdf->SetWidths(array(5, 50, 15, 90));
$pdf->Row(array('', 'STATUS SOKONG', ':', html_entity_decode($row_exam['STATUS_APP'], ENT_QUOTES)));
$pdf->Row(array('', 'ULASAN', ':', html_entity_decode(strtoupper($row_exam['ULASAN']), ENT_QUOTES)));
$pdf->Row(array('', 'TARIKH SOKONG', ':', html_entity_decode($exam_Date1, ENT_QUOTES)));
$pdf->Row(array('', 'DISOKONG OLEH', ':', html_entity_decode($row_exam['nama'], ENT_QUOTES)));
$pdf->Ln(5);

$student_Date = $row_pelajar['TARIKH_MOHON'];
$student_Date1 = date("d-m-Y", strtotime($student_Date));

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 0, '2. URUSAN PEJABAT AKADEMIK', 0, 1, 'L', false);
$pdf->SetFont('Arial', '', 9);
$pdf->Ln(5);
$pdf->SetWidths(array(5, 50, 15, 90));
$pdf->Row(array('', 'TARIKH MOHON', ':', html_entity_decode($student_Date1, ENT_QUOTES)));
$pdf->Row(array('', 'STATUS SOKONG', ':', html_entity_decode($row_akademik['STATUS_APP'], ENT_QUOTES)));
$pdf->Row(array('', 'ULASAN', ':', html_entity_decode(strtoupper($row_akademik['ULASAN']), ENT_QUOTES)));
$pdf->Row(array('', 'TARIKH SOKONG', ':', html_entity_decode($row_akademik['TARIKH_LULUS'], ENT_QUOTES)));
$pdf->Row(array('', 'DISOKONG OLEH', ':', html_entity_decode($row_akademik['nama'], ENT_QUOTES)));

$pdf->Ln(5);

$lib_Date = $row_pustakawan['TARIKH_LULUS'];
$lib_Date1 = date("d-m-Y", strtotime($lib_Date));

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 0, '3. URUSAN PUSTAKAWAN', 0, 1, 'L', false);
$pdf->SetFont('Arial', '', 9);
$pdf->Ln(5);
$pdf->SetWidths(array(5, 50, 15, 90));
$pdf->Row(array('', 'STATUS SOKONG', ':', html_entity_decode($row_pustakawan['STATUS_APP'], ENT_QUOTES)));
$pdf->Row(array('', 'ULASAN', ':', html_entity_decode(strtoupper($row_pustakawan['ULASAN']), ENT_QUOTES)));
$pdf->Row(array('', 'TARIKH SOKONG', ':', html_entity_decode($lib_Date1, ENT_QUOTES)));
$pdf->Row(array('', 'DISOKONG OLEH', ':', html_entity_decode($row_pustakawan['nama'], ENT_QUOTES)));
$pdf->Ln(30);


$hep1_Date = $row_hep['TARIKH_LULUS'];
$hep1_Date1 = date("d-m-Y", strtotime($hep1_Date));

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 0, '4. URUSAN HAL EHWAL PELAJAR', 0, 1, 'L', false);
$pdf->SetFont('Arial', '', 9);
$pdf->Ln(5);
$pdf->SetWidths(array(5, 50, 15, 90));
$pdf->Row(array('', 'STATUS SOKONG', ':', html_entity_decode($row_hep['STATUS_APP'], ENT_QUOTES)));
$pdf->Row(array('', 'ULASAN', ':', html_entity_decode(strtoupper($row_hep['ULASAN']), ENT_QUOTES)));
$pdf->Row(array('', 'TARIKH SOKONG', ':', html_entity_decode($hep1_Date1, ENT_QUOTES)));
$pdf->Row(array('', 'DISOKONG OLEH', ':', html_entity_decode($row_hep['nama'], ENT_QUOTES)));
$pdf->Ln(5);

$kewangan_Date = $row_kewangan['TARIKH_LULUS'];
$kewangan_Date1 = date("d-m-Y", strtotime($kewangan_Date));

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 0, '6. URUSAN KEWANGAN', 0, 1, 'L', false);
$pdf->SetFont('Arial', '', 9);
$pdf->Ln(5);
$pdf->SetWidths(array(5, 50, 15, 90));
$pdf->Row(array('', 'STATUS SOKONG', ':', html_entity_decode($row_kewangan['STATUS_APP'], ENT_QUOTES)));
$pdf->Row(array('', 'ULASAN', ':', html_entity_decode(strtoupper($row_kewangan['ULASAN']), ENT_QUOTES)));
$pdf->Row(array('', 'TARIKH SOKONG', ':', html_entity_decode($kewangan_Date1, ENT_QUOTES)));
$pdf->Row(array('', 'DISOKONG OLEH', ':', html_entity_decode($row_kewangan['nama'], ENT_QUOTES)));
$pdf->Ln(5);


$tp_Date = $row_tp['TARIKH_LULUS'];
$tp_Date1 = date("d-m-Y", strtotime($tp_Date));

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 0, '7. URUSAN PEJABAT PENDAFTAR', 0, 1, 'L', false);
$pdf->SetFont('Arial', '', 9);
$pdf->Ln(5);
$pdf->SetWidths(array(5, 50, 15, 90));
$pdf->Row(array('', 'STATUS LULUS', ':', html_entity_decode($row_tp['STATUS_APP'], ENT_QUOTES)));
$pdf->Row(array('', 'ULASAN', ':', html_entity_decode(strtoupper($row_tp['ULASAN']), ENT_QUOTES)));
$pdf->Row(array('', 'TARIKH LULUS', ':', html_entity_decode($tp_Date1, ENT_QUOTES)));
$pdf->Row(array('', 'DILULUSKAN OLEH', ':', html_entity_decode($row_tp['nama'], ENT_QUOTES)));
$pdf->Ln(5);


$pdf->Output('TAMAT_BELAJAR'.$tahun.'_'.$matrik_pljr.'.pdf', 'I');

?>