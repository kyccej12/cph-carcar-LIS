<?php 
	session_start();
    //ini_set("display_errors","on");

	require_once "handlers/_generics.php";
	
    $o = new _init;

    $order = $o->getArray("select *, date_format(extractdate,'%m/%d/%Y') as exdate from lab_samples where record_id = '$_REQUEST[lid]';");
    $a = $o->getArray("SELECT docointkey, a.enccode, SUBSTR(enccode,8,15) AS hmrno, DATE_FORMAT(dodate,'%m/%d/%Y %h:%i %p') AS orderdate, DATE_FORMAT(dodate,'%Y-%m-%d') AS xorderdate, a.hpercode, CONCAT(c.patlast,', ', c.patfirst,', ', c.patmiddle) AS pname, DATE_FORMAT(c.patbdate,'%m/%d/%Y') AS bday, DATE_FORMAT(c.patbdate,'%Y-%m-%d') AS xbday, IF(c.patsex='F','FEMALE','MALE') AS sex, c.patsex AS gender, a.proccode, b.procdesc, a.donotes AS remarks, a.licno, a.estatus, entby FROM hospital_dbo.hdocord a LEFT JOIN hospital_dbo.hprocm b ON a.proccode = b.proccode LEFT JOIN hospital_dbo.hperson c ON a.hpercode = c.hpercode WHERE a.enccode = '$order[enccode]' AND a.dodate = '$order[dotime]' AND a.proccode = '$order[code]'; ");
    $b = $o->getArray("SELECT * FROM lab_ketone WHERE enccode = '$order[enccode]' and serialno = '$order[serialno]';");
    $o->calculateAge($a['xorderdate'],$a['xbday']);

    /* Previous Results */
    // $c = $o->getArray("select *, concat('<br/>',date_format(result_date,'%m/%d/%Y')) as rdate from lab_cbcresult where SUBSTR(enccode,8,15) = '$a[hmrno]' and result_date < '$a[xorderdate]' limit 1,1;");
    // $d = $o->getArray("select *, concat('<br/>',date_format(result_date,'%m/%d/%Y')) as rdate from lab_cbcresult where SUBSTR(enccode,8,15) = '$a[hmrno]' and result_date < '$a[xorderdate]' limit 2,1;");
    // $e = $o->getArray("select *, concat('<br/>',date_format(result_date,'%m/%d/%Y')) as rdate from lab_cbcresult where SUBSTR(enccode,8,15) = '$a[hmrno]' and result_date < '$a[xorderdate]' limit 3,1;");
    

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
    <form name="frmKetone" id="frmKetone"> 
        <table width=100% cellpadding=0 cellspacing=0 valign=top>
         <tr>
             <td width=40% valign=top>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">REFERENCE CODE&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="ketone_enccode" id="ketone_enccode" value="<?php echo $a['enccode']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Request Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="ketone_sodate" id="ketone_sodate" value="<?php echo $a['orderdate']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">HMR #&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ketone_pid" id="ketone_pid" value="<?php echo $a['hmrno']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Result Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="ketone_date" id="ketone_date" value="<?php if($rdate !='') { echo $rdate; } else { echo date('m/d/Y'); } ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient Name&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ketone_pname" id="ketone_pname" value="<?php echo $a['pname']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>

                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Gender&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ketone_gender" id="ketone_gender" value="<?php echo $a['sex']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Birthdate&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ketone_birthdate" id="ketone_birthdate" value="<?php echo $a['bday']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Age&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ketone_age" id="ketone_age" value="<?php echo $o->ageDisplay; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Requesting Physician&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ketone_physician" id="ketone_physician" value="<?php echo $order['physician']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                </table>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Test or Procedure&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ketone_procedure" id="ketone_procedure" value="<?php echo $order['procedure']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Procedure Code&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ketone_ihomis_code" id="ketone_ihomis_code" value="<?php echo $order['code']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Specimen Type&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="ketone_sampletype" id="ketone_sampletype">
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
                            <select class="gridInput" style="width:100%;" name="bloodchem_machine" id="bloodchem_machine" onchange="javascript: changeMachine(this.value);">
                                <option value ="FUJI" <?php if ($order['machine'] == 'FUJI') { echo "selected"; } ?>>FUJI NX-700</option>
                                <option value ="MINDRAY" <?php if ($order['machine'] == 'MINDRAY') { echo "selected"; } ?>>MINDRAY</option>
                                <!-- <option value ="BIOBASE" <?php if ($b['machine'] == 'BIOBASE') { echo "selected"; } ?>>BIOBASE BK Series</option> -->
                            </select>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Sample Serial No.&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ketone_serialno" id="ketone_serialno" value="<?php echo $order['serialno']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Date Extracted&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ketone_extractdate" id="ketone_extractdate" value="<?php echo $order['exdate']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Time Extracted&nbsp;:</td>
                        <td align=left>
                
                            <input type="text" class="gridInput" style="width:100%;" name="ketone_extracttime" id="ketone_extracttime" value="<?php echo $order['extractime']; ?>" readonly>

                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Extracted By&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ketone_extractby" id="ketone_extractby" value="<?php echo $order['extractby']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Section&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="ketone_location" id="ketone_location">
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
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
                        <table width=100% cellpadding=0 cellspacing=3 class="td_content">
                            <tr>
                                <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                                <td align=left>
                                    <input class="gridInput" style="width:100%;font-size:11px;" type=text name="ketone_date" id="ketone_date" value="<?php echo date('m/d/Y'); ?>">
                                </td>				
                            </tr>
                            <tr><td height=2></td></tr>
                            <tr>
                                <td align="left" colspan=3 class="bareBold" style="padding-left: 15px;"><b>MACROSCOPIC EXAMINATION&nbsp;:</b></td>
                            </tr>
                            <tr><td height=2></td></tr>
                            <tr>
                                <td align="left" width=45% class="bareBold" style="padding-left: 25px;">Color&nbsp;:</td>
                                <td align=left width=50%>
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
                            <tr><td height=2></td></tr>
                            <tr>
                                <td align="left" width=50% class="bareBold" style="padding-left: 25px;">Transparency&nbsp;:</td>
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
                            </tr>
                            <tr><td height=2></td></tr>
                            <tr>
                                <td align="left" colspan=3 class="bareBold" style="padding-left: 15px;"><b>CHEMICAL EXAMINATION&nbsp;:</b></td>
                            </tr>
                            <tr>
                                <td align="left" class="bareBold" style="padding-left: 25px;">Ketone&nbsp;:</td>
                                <td align=left>
                                    <input  type="text" class="gridInput" style="width:100%;" name="ketone" id="ketone" value="<?php echo $b['ketone']; ?>">
                                </td>
                                <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                            </tr>
                            <tr><td height=2></td></tr>
                            <tr>
                                <td align="left" class="bareBold" style="padding-left: 25px;">Time Collected&nbsp;:</td>
                                <td align=left>
                                    <input type="text" class="gridInput" style="width:100%;" name="time_collected" id="time_collected" value="<?php if($b['time_collected'] == '') { echo date('H:i A'); } else { echo $b['time_collected']; } ?>">
                                </td>
                                <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                            </tr>
                            <tr><td height=2></td></tr>
                            <tr>
                            <td align="left" class="bareBold" style="padding-left: 25px;">Remarks&nbsp;:</td>
                                <td colspan=4>
                                    <textarea name="remarks" id="remarks" style="width: 95%;" rows=3><?php echo $b['remarks']; ?></textarea>
                                </td>
                            </tr>
                    </table>
                </table>
            </td>
        </tr>
    </table>              
</form>
</body>
</html>