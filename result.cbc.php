<?php 
	
	session_start();
	require_once "handlers/_generics.php";
	
    $o = new _init;

    $order = $o->getArray("select *, date_format(extractdate,'%m/%d/%Y') as exdate from lab_samples where record_id = '$_REQUEST[lid]';");
    $a = $o->getArray("SELECT docointkey, a.enccode, SUBSTR(enccode,16,15) AS hmrno, DATE_FORMAT(dodate,'%m/%d/%Y %h:%i %p') AS orderdate, DATE_FORMAT(dodate,'%Y-%m-%d') AS xorderdate, a.hpercode, CONCAT(c.patlast,', ', c.patfirst,', ', c.patmiddle) AS pname, DATE_FORMAT(c.patbdate,'%m/%d/%Y') AS bday, DATE_FORMAT(c.patbdate,'%Y-%m-%d') AS xbday, IF(c.patsex='F','FEMALE','MALE') AS sex, c.patsex AS gender, a.proccode, b.procdesc, a.donotes AS remarks, a.licno, a.estatus, entby FROM hospital_dbo.hdocord a LEFT JOIN hospital_dbo.hprocm b ON a.proccode = b.proccode LEFT JOIN hospital_dbo.hperson c ON a.hpercode = c.hpercode WHERE a.enccode = '$order[enccode]' AND a.dodate = '$order[dotime]' AND a.proccode = '$order[code]'; ");
    
    list($isResult) = $o->getArray("select count(*) from lab_cbcresult where enccode = '$a[enccode]' and serialno = '$order[serialno]';");
    $o->calculateAge($a['xorderdate'],$a['xbday']);

    if($isResult > 0) {   
        $b = $o->getArray("select * from lab_cbcresult where enccode = '$a[enccode]' and serialno = '$order[serialno]';");
    } else {
        $b = $o->getArray("select * from lab_cbcresult_temp where serialno = '$order[serialno]';");
    }

    /* Previous Results */
    $c = $o->getArray("select *, concat('<br/>',date_format(result_date,'%m/%d/%Y')) as rdate from lab_cbcresult where SUBSTR(enccode,16,15) = '$a[hpercode]' and result_date < '$a[xorderdate]' limit 1,1;");
    $d = $o->getArray("select *, concat('<br/>',date_format(result_date,'%m/%d/%Y')) as rdate from lab_cbcresult where SUBSTR(enccode,16,15) = '$a[hpercode]' and result_date < '$a[xorderdate]' limit 2,1;");
    $e = $o->getArray("select *, concat('<br/>',date_format(result_date,'%m/%d/%Y')) as rdate from lab_cbcresult where SUBSTR(enccode,16,15) = '$a[hpercode]' and result_date < '$a[xorderdate]' limit 3,1;");
    

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Primecare Cebu WebLIS System Ver. 1.0b</title>
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="ui-assets/datatables/css/jquery.dataTables.css">
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
    <script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
    <script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
	<script language="javascript" src="js/main.js?sid=<?php echo uniqid(); ?>"></script>
    <script>
        $(function() { 
            
            $("#cbc_date").datepicker(); 
        
            var myTable = $('#itemlist').DataTable({
                "scrollY":  "540",
                "scrollCollapse": true,
                "select":	'single',
                "searching": false,
                "bSort": false,
                "paging": false,
                "info": false,
              
                "aoColumnDefs": [
                    { "className": "dt-body-center", "targets": [1,2,3,4,5,6] },
                ]
            });

            var remarksSelection = [
                "MANUAL PLATELET DONE",
            ];

            $("#remarks").autocomplete({
                source: remarksSelection,
                minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });
        
        });

        $(document).on('keydown', 'input[pattern]', function(e){
            var input = $(this);
            var oldVal = input.val();
            var regex = new RegExp(input.attr('pattern'), 'g');

            setTimeout(function(){
                var newVal = input.val();
                if(!regex.test(newVal)){
                input.val(oldVal); 
                }
            }, 1);
        });

        $(document).on('keypress', 'input', function(e) {
            if(e.keyCode == 13) {
                e.preventDefault();
                var inputs = $(this).closest('form').find(':input:visible');
                inputs.eq( inputs.index(this)+ 1 ).focus();
            }
        });

        $('input.number').keyup(function (event) {
                // skip for arrow keys
                if (event.which >= 37 && event.which <= 40) return;
                // format number
                $(this).val(function (index, value) {
                    return value
                    .replace(/\D/g, "")
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                    ;
                });
            });

        function separateMe(val) {

            valu = parseFloat(parent.stripComma(val));

            $("#platelate").val(parent.kSeparator(valu));
        }

    </script>
    <style>
        .dataTables_wrapper {
            display: inline-block;
            font-size: 11px;
            width: 100%;
        }
        
        table.dataTable tr.odd { background-color: #f5f5f5;  }
        table.dataTable tr.even { background-color: white; }
        .dataTables_filter input { width: 250px; }


    </style>
</head>
<body>
    <form name="frmCBCResult" id="frmCBCResult"> 
        <table width=100% cellpadding=0 cellspacing=0 valign=top>
         <tr>
             <td width=30% valign=top>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">REFERENCE CODE&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="cbc_enccode" id="cbc_enccode" value="<?php echo $a['enccode']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Request Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="cbc_sodate" id="cbc_sodate" value="<?php echo $a['orderdate']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">HMR #&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="cbc_pid" id="cbc_pid" value="<?php echo $a['hmrno']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Result Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="cbc_date" id="cbc_date" value="<?php if($rdate !='') { echo $rdate; } else { echo date('m/d/Y'); } ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient Name&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="cbc_pname" id="cbc_pname" value="<?php echo $a['pname']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>

                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Gender&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="cbc_gender" id="cbc_gender" value="<?php echo $a['sex']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Birthdate&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="cbc_birthdate" id="cbc_birthdate" value="<?php echo $a['bday']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Age&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="cbc_age" id="cbc_age" value="<?php echo $o->ageDisplay; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Requesting Physician&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="cbc_physician" id="cbc_physician" value="<?php echo $order['physician']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                </table>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Test or Procedure&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="cbc_procedure" id="cbc_procedure" value="<?php echo $order['procedure']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Procedure Code&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="cbc_ihomis_code" id="cbc_ihomis_code" value="<?php echo $order['code']; ?>">
                            <input type="hidden" class="gridInput" style="width:100%;" name="cbc_code" id="cbc_code" value="<?php echo $order['primecarecode']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Specimen Type&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="cbc_spectype" id="cbc_spectype">
                                <?php
                                    $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                                    while(list($aa,$ab) = $iun->fetch_array()) {
                                        echo "<option value='$aa'";
                                        if($aa == $order['sampletype']) { echo "selected"; }
                                       echo ">$ab</option>";
                                    }
                                ?>
                            </select>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Machine&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="cbc_machine" id="cbc_machine">
                               <option value = 'GENRUI' <?php if($b['machine'] == 'GENRUI') { echo "selected"; } ?>>Genrui KT-6610</option>
                               <option value = 'H500' <?php if($b['machine'] == 'H500') { echo "selected"; } ?>>Yumizen H500</option>
                            </select>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Sample Serial No.&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="cbc_serialno" id="cbc_serialno" value="<?php echo $order['serialno']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Date Extracted&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="cbc_extractdate" id="cbc_extractdate" value="<?php echo $order['exdate']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Time Extracted&nbsp;:</td>
                        <td align=left>
                
                            <input type="text" class="gridInput" style="width:100%;" name="cbc_extracttime" id="cbc_extracttime" value="<?php echo $order['extractime']; ?>" readonly>

                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Extracted By&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="cbc_extractby" id="cbc_extractby" value="<?php echo $order['extractby']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Section&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="cbc_location" id="cbc_location">
                                <?php
                                    $iun = $o->dbquery("select id,location from lab_locations;");
                                    while(list($aa,$ab) = $iun->fetch_array()) {
                                        echo "<option value='$aa' ";
                                        if($aa == $a['location']) { echo "selected"; }
                                        echo ">$ab</option>";
                                    }
                                ?>
                            </select>
                        </td>				
                    </tr>
                </table>   
            </td>
            <td width=1%>&nbsp;</td>
        
            <td width=69% valign=top >
                <table width=100% id = "itemlist" class="cell-border" style="font-size:11px;">
                    <thead>
                        <tr>
                            <th>PARAMETER</th>
                            <th>CURRENT</th>
                            <th>FLAG</th>
                            <th>PREVIOUS <?php echo $e['rdate']; ?></th>
                            <th>PREVIOUS <?php echo $d['rdate']; ?></th>
                            <th>PREVIOUS <?php echo $c['rdate']; ?></th>
                            <th>REFERENCE VALUES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>WBC</td>
                            <td><input type="text" style="border: none; text-align: center; background-color: inherit !important;"  name="wbc" id="wbc" pattern="^\d*(\.\d{0,2})?$" value="<?php echo $b['wbc']; ?>"></td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"WBC",$b['wbc']); ?></td>
                            <td><?php echo $e['wbc']; ?></td>
                            <td><?php echo $d['wbc']; ?></td>
                            <td><?php echo $c['wbc']; ?></td>
                            <td align="left"><?php echo $o->getCBCAttribute($o->age,$a['gender'],"WBC"); ?></td>	
                        </tr>
                        <tr>
                            <td>RBC</td>
                            <td><input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="rbc" id="rbc" pattern="^\d*(\.\d{0,2})?$" value="<?php echo $b['rbc']; ?>"></td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"RBC",$b['rbc']); ?></td>
                            <td><?php echo $e['rbc']; ?></td>
                            <td><?php echo $d['rbc']; ?></td>
                            <td><?php echo $c['rbc']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"RBC"); ?></td>	
                        </tr>
                        <tr>
                            <td>Hemoglobin</td>
                            <td align=left><input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="hemoglobin" id="hemoglobin" pattern="^\d*(\.\d{0,2})?$" value="<?php echo $b['hemoglobin']; ?>"></td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"HEMOGLOBIN",$b['hemoglobin']); ?></td>
                            <td><?php echo $e['hemoglobin']; ?></td>
                            <td><?php echo $d['hemoglobin']; ?></td>
                            <td><?php echo $c['hemoglobin']; ?></td>
                            <td align="left"><?php echo $o->getCBCAttribute($o->age,$a['gender'],"HEMOGLOBIN"); ?></td>	
                        </tr>
                        <tr>
                            <td>HEMATOCRIT</td>
                            <td><input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="hematocrit" id="hematocrit" pattern="^\d*(\.\d{0,2})?$" value="<?php echo $b['hematocrit']; ?>"></td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"HEMATOCRIT",$b['hematocrit']); ?></td>
                            <td><?php echo $e['hematocrit']; ?></td>
                            <td><?php echo $d['hematocrit']; ?></td>
                            <td><?php echo $c['hematocrit']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"HEMATOCRIT"); ?></td>	
                        </tr>
                        <tr>
                            <td colspan=7><b>DIFFERENTIAL COUNT</b></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                        </tr>
                        <tr>
                            <td style="padding-left: 35px;">Neutrophils</td>
                            <td><input style="border: none; text-align: center; background-color: inherit !important;" name="neutrophils" id="neutrophils" pattern="^\d*(\.\d{0,2})?$" value="<?php echo $b['neutrophils']; ?>"></td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"NEUTROPHILS",$b['neutrophils']); ?></td>
                            <td><?php echo $e['neutrophils']; ?></td>
                            <td><?php echo $d['neutrophils']; ?></td>
                            <td><?php echo $c['neutrophils']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"NEUTROPHILS"); ?></td>	
                        </tr>
                        <tr>
                            <td style="padding-left: 35px;">Lymphocytes</td>
                            <td><input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="lymphocytes" id="lymphocytes" pattern="^\d*(\.\d{0,2})?$" value="<?php echo $b['lymphocytes']; ?>"></td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"LYMPHOCYTES",$b['lymphocytes']); ?></td>
                            <td><?php echo $e['lymphocytes']; ?></td>
                            <td><?php echo $d['lymphocytes']; ?></td>
                            <td><?php echo $c['lymphocytes']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"LYMPHOCYTES"); ?></td>	
                        </tr>
                        <tr>
                            <td style="padding-left: 35px;">Monocytes</td>
                            <td><input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="monocytes" id="monocytes" pattern="^\d*(\.\d{0,2})?$" value="<?php echo $b['monocytes']; ?>"></td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"MONOCYTES",$b['monocytes']); ?></td>
                            <td><?php echo $e['monocytes']; ?></td>
                            <td><?php echo $d['monocytes']; ?></td>
                            <td><?php echo $c['monocytes']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"MONOCYTES"); ?></td>	
                        </tr>
                        <tr>
                            <td style="padding-left: 35px;">Eosinophils</td>
                            <td><input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="eosinophils" id="eosinophils" pattern="^\d*(\.\d{0,2})?$" value="<?php echo $b['eosinophils']; ?>"></td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"EOSINOPHILS",$b['eosinophils']); ?></td>
                            <td><?php echo $e['eosinophils']; ?></td>
                            <td><?php echo $d['eosinophils']; ?></td>
                            <td><?php echo $c['eosinophils']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"EOSINOPHILS"); ?></td>	
                        </tr>
                        <tr>
                            <td style="padding-left: 35px;">Basophils</td>
                            <td><input style="border: none; text-align: center; background-color: inherit !important;" name="basophils" id="basophils" pattern="^\d*(\.\d{0,2})?$" value="<?php echo $b['basophils']; ?>"></td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"BASOPHILS",$b['basophils']); ?></td>
                            <td><?php echo $e['basophils']; ?></td>
                            <td><?php echo $d['basophils']; ?></td>
                            <td><?php echo $c['basophils']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"BASOPHILS"); ?></td>	
                        </tr>
                        <tr>
                            <td>MCV</td>
                            <td>
                                <input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="mcv" class="number" id="mcv" pattern="^\d*(\.\d{0,2})?$" value="<?php if($b['mcv'] > 0) { echo $b['mcv']; } ?>">
                            </td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"MCV",$b['mcv']) ?></td>
                            <td><?php echo $e['mcv']; ?></td>
                            <td><?php echo $d['mcv']; ?></td>
                            <td><?php echo $c['mcv']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"MCV"); ?></td>	
                        </tr>
                        <tr>
                            <td>MCH</td>
                            <td>
                                <input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="mch" class="number" id="mch" pattern="^\d*(\.\d{0,2})?$" value="<?php if($b['mch'] > 0) { echo $b['mch']; } ?>">
                            </td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"MCH",$b['mch']); ?></td>
                            <td><?php echo $e['mch']; ?></td>
                            <td><?php echo $d['mch']; ?></td>
                            <td><?php echo $c['mch']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"MCH"); ?></td>	
                        </tr>
                        <tr>
                            <td>MCHC</td>
                            <td>
                                <input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="mchc" class="number" id="mchc" pattern="^\d*(\.\d{0,2})?$" value="<?php if($b['mchc'] > 0) { echo $b['mchc']; } ?>">
                            </td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"MCHC",$b['mchc']); ?></td>
                            <td><?php echo $e['mchc']; ?></td>
                            <td><?php echo $d['mchc']; ?></td>
                            <td><?php echo $c['mchc']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"MCHC"); ?></td>	
                        </tr>
                        <tr>
                            <td>RDW-CV</td>
                            <td>
                                <input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="rdwcv" class="number" id="rdwcv" pattern="^\d*(\.\d{0,2})?$" value="<?php if($b['rdwcv'] > 0) { echo $b['rdwcv']; } ?>">
                            </td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"RDW-CV",$b['rdwcv']) ?></td>
                            <td><?php echo $e['rdwcv']; ?></td>
                            <td><?php echo $d['rdwcv']; ?></td>
                            <td><?php echo $c['rdwcv']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"RDW-CV"); ?></td>	
                        </tr>
                        <tr>
                            <td>RDW-SD</td>
                            <td>
                                <input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="rdwsd" class="number" id="rdwsd" pattern="^\d*(\.\d{0,2})?$" value="<?php if($b['rdwsd'] > 0) { echo $b['rdwsd']; } ?>">
                            </td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"RDW-SD",$b['rdwsd']); ?></td>
                            <td><?php echo $e['rdwsd']; ?></td>
                            <td><?php echo $d['rdwsd']; ?></td>
                            <td><?php echo $c['rdwsd']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"RDW-SD"); ?></td>	
                        </tr>
                        <tr>
                            <td>Platelet Count</td>
                            <td>
                                <input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="platelate" class="number" id="platelate" pattern="^\d*(\.\d{0,2})?$" value="<?php if($b['platelate'] > 0) { echo $b['platelate']; } ?>" onchange="javascript: separateMe(this.value);">
                            </td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"PLATELATE",$b['platelate']); ?></td>
                            <td><?php echo $e['platelate']; ?></td>
                            <td><?php echo $d['platelate']; ?></td>
                            <td><?php echo $c['platelate']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"PLATELATE"); ?></td>	
                        </tr>
                        <tr>
                            <td>MPV</td>
                            <td>
                                <input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="mpv" class="number" id="mpv" pattern="^\d*(\.\d{0,2})?$" value="<?php if($b['mpv'] > 0) { echo $b['mpv']; } ?>">
                            </td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"MPV",$b['platelate']); ?></td>
                            <td><?php echo $e['mpv']; ?></td>
                            <td><?php echo $d['mpv']; ?></td>
                            <td><?php echo $c['mpv']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"MPV"); ?></td>	
                        </tr>
                        <tr>
                            <td>PDW-CV</td>
                            <td>
                                <input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="pdwcv" class="number" id="pdwcv" pattern="^\d*(\.\d{0,2})?$" value="<?php if($b['pdwcv'] > 0) { echo $b['pdwcv']; } ?>">
                            </td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"PDW-CV",$b['pdwcv']); ?></td>
                            <td><?php echo $e['pdwcv']; ?></td>
                            <td><?php echo $d['pdwcv']; ?></td>
                            <td><?php echo $c['pdwcv']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"PDW-CV"); ?></td>	
                        </tr>
                        <tr>
                            <td>PDW-SD</td>
                            <td>
                                <input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="pdwsd" class="number" id="pdwsd" pattern="^\d*(\.\d{0,2})?$" value="<?php if($b['pdwsd'] > 0) { echo $b['pdwsd']; } ?>">
                            </td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"PDW-SD",$b['pdwsd']); ?></td>
                            <td><?php echo $e['pdwsd']; ?></td>
                            <td><?php echo $d['pdwsd']; ?></td>
                            <td><?php echo $c['pdwsd']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"PDW-SD"); ?></td>	
                        </tr>
                        <tr>
                            <td>PCT</td>
                            <td>
                                <input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="pct" class="number" id="pct" pattern="^\d*(\.\d{0,2})?$" value="<?php if($b['pct'] > 0) { echo $b['pct']; } ?>">
                            </td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"PCT",$b['pct']); ?></td>
                            <td><?php echo $e['pct']; ?></td>
                            <td><?php echo $d['pct']; ?></td>
                            <td><?php echo $c['pct']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"PCT"); ?></td>	
                        </tr>
                        <tr>
                            <td>P-LCC</td>
                            <td>
                                <input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="plcc" class="number" id="plcc" pattern="^\d*(\.\d{0,2})?$" value="<?php if($b['plcc'] > 0) { echo $b['plcc']; } ?>">
                            </td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"P-LCC",$b['plcc']); ?></td>
                            <td><?php echo $e['plcc']; ?></td>
                            <td><?php echo $d['plcc']; ?></td>
                            <td><?php echo $c['plcc']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"P-LCC"); ?></td>	
                        </tr>
                        <tr>
                            <td>P-LCR</td>
                            <td>
                                <input type="text" style="border: none; text-align: center; background-color: inherit !important;" name="plcr" class="number" id="plcr" pattern="^\d*(\.\d{0,2})?$" value="<?php if($b['plcr'] > 0) { echo $b['plcr']; } ?>">
                            </td>
                            <td><?php echo $o->checkCBCValues($o->age,$a['gender'],"P-LCR",$b['plcr']); ?></td>
                            <td><?php echo $e['plcr']; ?></td>
                            <td><?php echo $d['plcr']; ?></td>
                            <td><?php echo $c['plcr']; ?></td>
                            <td><?php echo $o->getCBCAttribute($o->age,$a['gender'],"P-LCR"); ?></td>	
                        </tr>
                        <tr>
                            <td valign=top>Remarks</td>
                            <td colspan=6>
                                <textarea name="remarks" id="remarks" style="width: 90%;" rows=3><?php echo $b['remarks']; ?></textarea>
                            </td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>              
</form>
</body>
</html>