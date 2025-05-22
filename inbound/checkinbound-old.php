<?php
	
    include("../handlers/_generics.php");
    $con = new _init();

    
    function decodeDate($dateString) {
        $date = substr($dateString,0,4) . "-" . substr($dateString,4,2) . "-" . substr($dateString,6,2);
        return $date;
    }

    
    $dir = 'in';
    $content = scandir($dir, 1);

    foreach($content as $hfile) {

        $tfile = explode(".",$hfile);

        if($tfile[1] == 'hl7') {

            $file = "in/$hfile";// Your Temp Uploaded file
            $handle = fopen($file, "r"); // Make all conditions to avoid errors
            $read = file_get_contents($file); //read
            $lines = explode("\r", $read);//get
            $i= 0;//initialize

            $specimenID = ''; $updateString = '';

            foreach($lines as $key => $value) {
                $cols[$i] = explode("|", $value);

				if($cols[$i][0] == 'MSH') {
					 $date = decodeDate($cols[$i][6]);
				}

                if($cols[$i][0] == "PID") {
                    $specimenID = $cols[$i][3];
                
                    if($specimenID != '') {
                        list($icount) = $con->getArray("SELECT COUNT(*) from ppp_danao.lab_cbcresult_temp WHERE serialno = '$specimenID';");
                        if($icount == 0) {
                            $con->dbquery("INSERT IGNORE INTO ppp_danao.lab_cbcresult_temp (serialno,result_date) VALUES ('$specimenID','$date');");
                        }
                    }
                }

                /* RESULTS SEGMENT */
                if($cols[$i][0] == "OBX") {

                    $identifier = trim($cols[$i][3],'^');

                    switch($identifier) {
                        case "WBC":
                            $updateString .= ",wbc = '".$cols[$i][5]."'";
                        break;
                        case "RBC":
                            $updateString .= ",rbc = '".$cols[$i][5]."'";
                        break;
                        case "HGB":
                            $updateString .= ",hemoglobin = '".$cols[$i][5]."'";
                        break;
                        case "HCT":
                            $updateString .= ",hematocrit = '".$cols[$i][5]."'";
                        break;
                        case "Neu%":
                            $updateString .= ",neutrophils = '".$cols[$i][5]."'";
                        break;
                        case "Lym%":
                            $updateString .= ",lymphocytes = '".$cols[$i][5]."'";
                        break;
                        case "Mon%":
                            $updateString .= ",monocytes = '".$cols[$i][5]."'";
                        break;
                        case "Eos%":
                            $updateString .= ",eosinophils = '".$cols[$i][5]."'";
                        break;
                        case "Bas%":
                            $updateString .= ",basophils = '".$cols[$i][5]."'";
                        break;
                        case "PLT":
                            $updateString .= ",platelate = '".$cols[$i][5]."'";
                        break;
                        case "MCV":
                            $updateString .= ",mcv = '".$cols[$i][5]."'";
                        break;
                        case "MCH":
                            $updateString .= ",mch = '".$cols[$i][5]."'";
                        break;
                        case "MCHC":
                            $updateString .= ",mchc = '".$cols[$i][5]."'";
                        break;
                        case "RDW-CV":
                            $updateString .= ",rdwcv = '".$cols[$i][5]."'";
                        break;
                        case "RDW-SD":
                            $updateString .= ",rdwsd = '".$cols[$i][5]."'";
                        break;
                        case "MPV":
                            $updateString .= ",mpv = '".$cols[$i][5]."'";
                        break;
                        case "PDW-CV":
                            $updateString .= ",pdwcv = '".$cols[$i][5]."'";
                        break;
                        case "PDW-SD":
                            $updateString .= ",pdwsd = '".$cols[$i][5]."'";
                        break;
                        case "PCT":
                            $updateString .= ",pct = '".$cols[$i][5]."'";
                        break;
                        case "P-LCC":
                            $updateString .= ",plcc = '".$cols[$i][5]."'";
                        break;
                        case "P-LCR":
                            $updateString .= ",plcr = '".$cols[$i][5]."'";
                        break;
                    }
                }

                $i++;
            }

            fclose($handle);

            if($specimenID != '') {

                $newFileName = $specimenID . ".hl7";
                $updateQuery = "UPDATE IGNORE ppp_danao.lab_cbcresult_temp set parsed_on = now(), parsed_file = '$newFileName' $updateString WHERE serialno = '$specimenID';";
                $con->dbquery($updateQuery);
                rename("$file", "out/$newFileName");
                
            } else {
                rename("$file", "stray/$hfile");
            }

        }

    }
    

?>