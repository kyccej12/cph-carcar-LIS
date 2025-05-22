<?php 
	
	session_start();
	require_once "handlers/_generics.php";
	
    $o = new _init;

    $order = $o->getArray("select *, date_format(extractdate,'%m/%d/%Y') as exdate from lab_samples where record_id = '$_REQUEST[lid]';");
    $a = $o->getArray("SELECT docointkey, a.enccode, SUBSTR(enccode,8,15) AS hmrno, DATE_FORMAT(dodate,'%m/%d/%Y %h:%i %p') AS orderdate, DATE_FORMAT(dodate,'%Y-%m-%d') AS xorderdate, a.hpercode, concat(c.patlast,', ', c.patfirst,' ', c.patmiddle) as pname, DATE_FORMAT(c.patbdate,'%m/%d/%Y') AS bday, DATE_FORMAT(c.patbdate,'%Y-%m-%d') AS xbday, IF(c.patsex='F','FEMALE','MALE') AS sex, c.patsex as gender, a.proccode, b.procdesc, a.donotes AS remarks, a.licno, a.estatus, entby FROM hospital_dbo.hdocord a LEFT JOIN hospital_dbo.hprocm b ON a.proccode = b.proccode LEFT JOIN hospital_dbo.hperson c ON a.hpercode = c.hpercode WHERE a.enccode = '$order[enccode]';");
    $b = $o->getArray("select * from lab_uaresult where enccode = '$order[enccode]' and serialno = '$order[serialno]';");
   
    $age = $o->calculateAge($a['xorderdate'],$a['xbday']);

    /* SET DEFAULT VALUE */
    //if(!$b['glucose']) { $b['glucose'] = 'NEGATIVE'; }
    //if(!$b['protein']) { $b['protein'] = 'NEGATIVE'; }

    /* if($b['ph'] >= 7) { 
        $uratesDisabled = "disabled"; 
        $poDisabled = '';
    } else {
        $uratesDisabled = ''; 
        $poDisabled = "disabled";
    } */


?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Prime Care Cebu, Inc.</title>
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/texteditor/jquery-te-1.4.0.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="ui-assets/texteditor/jquery-te-1.4.0.min.js"></script>
	<script language="javascript" src="js/main.js?sid=<?php echo uniqid(); ?>"></script>
    <script>
        $(function() { 
            $("#ua_date").datepicker(); 
            
            var availableOptions = [
                "NEGATIVE",
                "POSITIVE"
            ];

            var availableOptions2 = [
                "POSITIVE",
                "NEGATIVE",
                "1+",
                "2+",
                "3+",
                "4+",
                "+",
                "++",
                "+++",
                "++++",
                "SMALL",
                "MODERATE",
                "LARGE",
                "TRACE"
            ];

            var availableOptions3 = [
                "AMORPHOUS URATE: ",
                "AMOURPHOUS PHOSPATE: ",
                "AMMONIUM BIURATE: ",
                "URIC ACID CRYSTAL: ",
                "TRIPLE PHOSPATE: ",
                "CALCIUM OXALATE: ",
                "BILIRUBIN CRYSTALS: ",
                "CHOLESTEROL CRYSTALS: "
            ];

            var availableOptions4 = [
                "HYALINE CAST: 0-1/LPF",
                "FINE GRANULAR CAST: 0-1/LPF",
                "COARSE GRANULAR CAST: 0-1/LPF",
                "RBC CAST: 0-1/LPF",
                "WBC CAST: 0-1/LPF",
                "WAXY CAST: 0-1/LPF",
                "CYLINDROIDS"
            ];

            var availableOptions5 = [
                "MODERATE",
                "FEW",
                "RARE",
                "ABUNDANT",
                "MANY"
            ];

            var availableOptions6 = [
                "0.2",
                "1.0",
                "2.0",
                "4.0",
                "12.0",
                "NEGATIVE",
                "NORMAL",
                "1+",
                "2+",
                "3+",
                "4+",
                "+",
                "++",
                "+++",
                "TRACE"
            ];

            var availableOptions7 = [
                "NEGATIVE",
                "TRACE",
                "TRACE - INTACT",
                "TRACE - HEMOLYZED",
                "1+",
                "2+",
                "3+",
                "4+",
                "+",
                "++",
                "+++",
                "++++",
                "SMALL",
                "MODERATE",
                "LARGE",
                "TRACE"
            ];

            var remarksSelection = [
                "TEST DONE TWICE",
            ];

            $("#remarks").autocomplete({
                source: remarksSelection,
                minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });

            $("#protein,  #ketone, #bilirubin, #leukocyte" ).autocomplete({
                 source: availableOptions2,
                 minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });

            $("#nitrite" ).autocomplete({
                 source: availableOptions,
                 minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });

            $("#glucose, #blood" ).autocomplete({
                 source: availableOptions7,
                 minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });

            $("#urobilinogen" ).autocomplete({
                 source: availableOptions6,
                 minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });

            $("#crystals, #crystals1, #crystals2, #crystals3, #crystals4, #crystals5, #crystals5, #crystals6, #crystals7").autocomplete({
                 source: availableOptions3,
                 minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            }); 

            $("#casts, #casts1, #casts2, #casts3, #casts4, #casts5, #casts6").autocomplete({
                 source: availableOptions4,
                 minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });

            $("#bacteria, #epith, #mucus_thread, #amorphous_urates, #amorphous_po4").autocomplete({
                source: availableOptions5,
                minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });
        
        });

        $(document).on('keypress', 'input', function(e) {
            if(e.keyCode == 13) {
                e.preventDefault();
                var inputs = $(this).closest('form').find(':input:visible');
                 inputs.eq( inputs.index(this)+ 1 ).focus();
            }
        });



    </script>
</head>
<body>
    <form name="frmUrinalysisReport" id="frmUrinalysisReport"> 
        <input type="hidden" name="ua_primecarecode" id="ua_primecarecode" value = '<?php echo $order['primecarecode']; ?>'>
        <table width=100% cellpadding=0 cellspacing=0 valign=top>
         <tr>
             <td width=35% valign=top>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Reference Code&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="ua_enccode" id="ua_enccode" value="<?php echo $a['enccode']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Request Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="ua_orderdate" id="ua_orderdate" value="<?php echo $a['orderdate']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Hospital No.&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_pid" id="ua_pid" value="<?php echo $a['hmrno']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Result Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="ua_date" id="ua_date" value="<?php if($rdate !='') { echo $rdate; } else { echo date('m/d/Y'); } ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient Name&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_pname" id="ua_pname" value="<?php echo $a['pname']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>

                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Gender&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_gender" id="ua_gender" value="<?php echo $a['gender']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Birthdate&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_birthdate" id="ua_birthdate" value="<?php echo $a['bday']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Age&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_age" id="ua_age" value="<?php echo $age; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Requesting Physician&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_physician" id="ua_physician" value="<?php echo $order['physician']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                </table>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Test or Procedure&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_procedure" id="ua_procedure" value="<?php echo $order['procedure']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Procedure Code&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_code" id="ua_code" value="<?php echo $order['code']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Specimen Type&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="ua_spectype" id="ua_spectype">
                                <?php
                                    $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                                    while(list($aa,$ab) = $iun->fetch_array()) {
                                        echo "<option value='$aa'";
                                        if($aa == $a['sampletype']) { echo "selected"; }
                                       echo ">$ab</option>";
                                    }
                                ?>
                            </select>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Sample Serial No.&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_serialno" id="ua_serialno" value="<?php echo $order['serialno']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Date Extracted&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_extractdate" id="ua_extractdate" value="<?php echo $order['exdate']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Time Extracted&nbsp;:</td>
                        <td align=left>
                
                            <input type="text" class="gridInput" style="width:100%;" name="ua_extracttime" id="ua_extracttime" value="<?php echo $order['extractime']; ?>" readonly>

                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Extracted By&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_extractby" id="ua_extractby" value="<?php echo $a['extractby']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Extraction Site&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="ua_location" id="ua_location">
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
            <td width=64% valign=top >
                 <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
                 <table width=100% cellpadding=0 cellspacing=3 class="td_content">
                    <tr>
                        <td align="left" colspan=3 class="bareBold" style="padding-left: 15px;"><b>MACROSCOPIC EXAMINATION&nbsp;:</b></td>
                    </tr>
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 25px;">Color&nbsp;:</td>
                        <td align=left width=30%>
                            <select name="color" id="color" class="gridInput" style="width:100%;">
                                <option value="Yellow" <?php if($b['color'] == 'Yellow') { echo "selected"; } ?>>Yellow</option>
                                <option value="Light Yellow" <?php if($b['color'] == 'Light Yellow') { echo "selected"; } ?>>Light Yellow</option>
                                <option value="Pale Yellow" <?php if($b['color'] == 'Pale Yellow') { echo "selected"; } ?>>Pale Yellow</option>
                                <option value="Dark Yellow" <?php if($b['color'] == 'Dark Yellow') { echo "selected"; } ?>>Dark Yellow</option>
                                <option value="Amber" <?php if($b['color'] == 'Amber') { echo "selected"; } ?>>Amber</option>
                                <option value="Straw" <?php if($b['color'] == 'Straw') { echo "selected"; } ?>>Straw</option>
                                <option value="Dark Brown" <?php if($b['color'] == 'Dark Brown') { echo "selected"; } ?>>Dark Brown</option>
                                <option value="Bright Yellow" <?php if($b['color'] == 'Bright Yellow') { echo "selected"; } ?>>Bright Yellow</option>
                                <option value="Red" <?php if($b['color'] == 'Red') { echo "selected"; } ?>>Red</option>
                            </select>
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 25px;">Transparency&nbsp;:</td>
                        <td align=left>
                            <select name="transparency" id="transparency" class="gridInput" style="width:100%;">
                                <option value="Clear" <?php if($b['transparency'] == 'Clear') { echo "selected"; } ?>>Clear</option>
                                <option value="Hazy" <?php if($b['transparency'] == 'Hazy') { echo "selected"; } ?>>Hazy</option>
                                <option value="Slightly Hazy" <?php if($b['transparency'] == 'Slightly Hazy') { echo "selected"; } ?>>Slightly Hazy</option>
                                <option value="Cloudy" <?php if($b['transparency'] == 'Cloudy') { echo "selected"; } ?>>Cloudy</option>
                                <option value="Slightly Cloudy" <?php if($b['transparency'] == 'Slightly Cloudy') { echo "selected"; } ?>>Slightly Cloudy</option>
                                <option value="Turbid" <?php if($b['transparency'] == 'Turbid') { echo "selected"; } ?>>Turbid</option>
                            </select>
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>	
                    </tr>
                    <tr>
                        <td align="left" colspan=3 class="bareBold" style="padding-left: 15px;"><b>CHEMICAL EXAMINATION&nbsp;:</b></td>
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Glucose&nbsp;:</td>
                        <td align=left>
                            <input  type="text" class="gridInput" style="width:100%;" name="glucose" id="glucose" value="<?php echo $b['glucose']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Bilirubin&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bilirubin" id="bilirubin" value="<?php echo $b['bilirubin']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Ketone&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ketone" id="ketone" value="<?php echo $b['ketone']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;" valign=top>Specific Gravity&nbsp;:</td>
                        <td align=left valign=top>
                            <select name="gravity" id="gravity" class="gridInput" style="width:100%;">
                            <?php
                                for($sgloop = 1.000; $sgloop <= 1.030; $sgloop+=0.005) {
                                    $valsg = number_format($sgloop, 3);

                                    echo "<option value='".$valsg."' "; 
                                    if($b['gravity'] == $valsg) { echo "selected"; }
                                    echo ">".$valsg."</option>";
                                }
                            ?>
                            </select>

                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;" valign=top></td>	
                    </tr>
                    
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Blood&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="blood" id="blood" value="<?php echo $b['blood']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 25px;">pH&nbsp;:</td>
                        <td align=left>
                            <select name="ph" id="ph" class="gridInput" style="width:100%;" onchange="javascript: checkPhValue(this.value);">
                            <?php
                                for($phloop = 4.5; $phloop <= 9; $phloop+=0.5) {
                                    echo "<option value='".number_format($phloop,1)."' "; 
                                    if($b['ph'] == $phloop) { echo "selected"; }
                                    echo ">".number_format($phloop,1)."</option>";
                                }

                            ?>
                            </select>
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Protein&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="protein" id="protein" value="<?php echo $b['protein']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Urobilinogen&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="urobilinogen" id="urobilinogen" value="<?php echo $b['urobilinogen']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Nitrite&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="nitrite" id="nitrite" value="<?php echo $b['nitrite']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Leukocyte&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="leukocyte" id="leukocyte" value="<?php echo $b['leukocyte']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" colspan=3 class="bareBold" style="padding-left: 15px; padding-top: 20px;"><b>MICROSCOPIC EXAMINATION&nbsp;:</b></td>
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">PUS Cells&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="pus" id="pus" value="<?php echo $b['pus']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;">/HPF</td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">RBC&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="rbc_hpf" id="rbc_hpf" value="<?php echo $b['rbc_hpf']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;">/HPF</td>	
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Epithelial Cells&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="epith" id="epith" value="<?php echo $b['epith']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Mucus Threads&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="mucus_thread" id="mucus_thread" value="<?php echo $b['mucus_thread']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Bacteria&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bacteria" id="bacteria" value="<?php echo $b['bacteria']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>

                    <tr><td colspan=5><hr style="color: white;"></td></tr>
                    <tr>
                        <td align="left" width=20% class="bareBold" style="padding-left: 25px;">Casts&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="casts" id="casts" value="<?php echo $b['casts']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="casts1" id="casts1" value="<?php echo $b['casts1']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="casts2" id="casts2" value="<?php echo $b['casts2']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="casts3" id="casts3" value="<?php echo $b['casts3']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="casts4" id="casts4" value="<?php echo $b['casts4']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="casts5" id="casts5" value="<?php echo $b['casts5']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="casts6" id="casts6" value="<?php echo $b['casts6']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr><td colspan=5><hr style="color: white;"></td></tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Crystals&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="crystals" id="crystals" value="<?php echo $b['crystals']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="crystals1" id="crystals1" value="<?php echo $b['crystals1']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="crystals2" id="crystals2" value="<?php echo $b['crystals2']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="crystals3" id="crystals3" value="<?php echo $b['crystals3']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="crystals4" id="crystals4" value="<?php echo $b['crystals4']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="crystals5" id="crystals5" value="<?php echo $b['crystals5']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="crystals6" id="crystals6" value="<?php echo $b['crystals6']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:120%;" name="crystals7" id="crystals7" value="<?php echo $b['crystals7']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>
                    </tr>
                    <tr><td colspan=5><hr style="color: white;"></td></tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Amorphous (Urates)&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="amorphous_urates" id="amorphous_urates" value="<?php echo $b['amorphous_urates']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Amorphous (Phosphates)&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="amorphous_po4" id="amorphous_po4" value="<?php echo $b['amorphous_po4']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px;" valign=top><b>Others&nbsp;:</b></td>
                        <td align=left width=75% colspan=3>
                            <input type="text" class="gridInput" style="width:90%; " name="others" id="others" value="<?php echo $b['others']; ?>">
                        </td>
                    </tr>             
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px;" valign=top><b>Remarks&nbsp;:</b></td>
                        <td align=left width=75% colspan=3>
                            <input type="text" class="gridInput" style="width:90%; " name="remarks" id="remarks" value="<?php echo $b['remarks']; ?>">
                        </td>
                    </tr> 
                </table>
            </td>
        </tr>
    </table>              
</form>
</body>
</html>