<!DOCTYPE html>
<html lang="en">
	<head>
  <!--Jeremy Lee 2015 -->
  <meta charset="utf-8">
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script type="text/javascript" src="js/jspdf.min.js"></script>
  <script type="text/javascript" src="js/html2canvas.js"></script>

  <script>
  $(document).ready(function() {


  var pdf = new jsPDF('p','pt','letter');
  var options = {
         pagesplit: true
    };
  pdf.addHTML(document.body,options,function() {
    var string = pdf.output('datauristring');
    $('#pdfdownload').attr('href', string);
  });

  });
  </script>
</head>
<div class="container">
<div class="row">
  <br />
  <div class="col-sm-6 pull-left">
    <form action="index.php" method="post" enctype="multipart/form-data">
        <div class="row">
          <p class="pull-left">Select file to upload:</p>
          <input class="pull-left" type="file" name="file" id="file">
        </div>
        <div class="row">
          <input class="pull-left" type="submit" value="Upload" name="submit">
        </div>
    </form>
  </div>
  <div class="col-sm-4 pull-left">
    <a href="#" class="btn btn-default" id="pdfdownload">Download PDF</a>
  </div>  
<body>

  <div class="row">
    <div class="col-sm-10">
<?php 
if (isset($_FILES["file"]["tmp_name"])){
	echo '<div class="page-header"><h1>Background Report</h1></div>';
	$names = array();
	$no_record = array();
  //the array formatting is as follows
  //[name of title in txt file] => array([name wanted in html file], 
  //[either length after colon or flag (m for multiline with ## for number of lines) or (f for function)], [name of function])
	$values_map = array("Date of Birth" => array("DOB", 10), 
                      "SSN" => array("SS", 12),
                      "Driver's License Information" => array("DL", "f", "printDL"),
                      "Address Summary" => array("Prior addresses reported", "m01"),
                      "Current Property Deeds" => array("Properties owned by subject", "f", "printProperties"),
                      "Current Vehicle Information" => array("Vehicle Information", "f", "printVehicles"),
                      "Professional Licenses" => array("Professional licenses reported", "f", "printProfessionalLicenses"),
                      "Bankruptcy Records" => array("Bankruptcies reported", "f", "printBankruptcies"),
                      "Judgments" => array("Judgements reported", "f", "printJudgments"),
                      "Possible Criminal Records" => array("Criminal convictions reported", "f", "printCriminal"), 
                      "Global Watch Lists Match" => array("Global Watch Lists Match", "f", "printWatchList", "red"),
                      "US Business Affiliations" => array("Possible Business Affiliations", "f", "printBusinessAffiliation"), 
                      "UCC Filings" => array("UCC Filings", "f", "printUCC"),
                      "Sexual Offences" => array("Sexual Offences"),
                      "Weapons Permits" => array("Concealed Weapons permits"),
                      "Reported Employment" => array("Reported Employment"),
                      "Aircraft Records" => array("FAA Aircraft ownership"),
                      "Watercraft registrations and ownership" => array("Watercraft registrations and ownership"),
                      "Voter Registrations" => array("Voters Registrations", "m06"),
                      "Hunting Permits" => array("Hunting/Fishing Permits")
                      );
	$txt_file = file_get_contents($_FILES["file"]["tmp_name"]);
	$rows = explode("\n", $txt_file);
	array_shift($rows);
  
	function printWatchList($data, $value, $rows, $line){
    echo '<div class="panel-heading">';
    if (isset($value[3])){ 
      echo '<p style="color:',$value[3], ';">';
    }else{
      echo '<p>';
    }

    echo '<u><b>' . $value[0] . '</u></b><br />';
    echo '</div><div class="panel-body">';
    $iterations =  20;
    for ($i = 1; $i <= $iterations; $i++) {
      if ($rows[$line + $i] == "Global Watch Lists Match:"){
        echo $rows[$line + $i + 2];
        echo '<br />';
        echo $rows[$line + $i + 3];
      }
    }
      echo '<br />';    
    echo '</p></div>';      
	}  
  
  function printCriminal($data, $value, $rows, $line){
    echo '<div class="panel-heading">';    
    $found = explode("(", $data);
    $iterations = intval($found[1][0]) * 30;
    echo '<b><u>Criminal Convictions reported</u></b><br />';
    echo '</div><div class="panel-body">';    
    echo 'Following cases reported<br />';
    for ($i = 1; $i <= $iterations; $i++) {       
      if (strpos($rows[intval($line) + $i], 'Name') == 1){
        echo explode(':', $rows[intval($line) + $i])[1] . '<br />';
      }     
      if (strpos($rows[intval($line) + $i], 'Crime Details') !== false){
        echo explode('-', $rows[intval($line) + $i ])[1] . '<br />';
      }   
      if (strpos($rows[intval($line) + $i], 'Case Number') !== false){
        echo "Case Number - " . explode(':', $rows[intval($line) + $i])[1] . '<br />';
      }         
      if (strpos($rows[intval($line) + $i], 'OffenseDescription1') !== false){
        echo substr($rows[intval($line) + $i], 21) . '<br />';
      }         
    }
  echo '</div>';  
  }
  
  function printUCC($data, $value, $rows, $line){
    echo '<div class="panel-heading">';       
    $found = explode("(", $data);
    $iterations = intval($found[1][0]) * 25;
    echo '<b><u>UCC Filings</u></b><br />';
    echo '</div><div class="panel-body">';        
    for ($i = 1; $i <= $iterations; $i++) {       
      if (strpos($rows[intval($line) + $i], 'Debtor') !== false){
        echo 'Debtor - ' . explode(':', $rows[intval($line) + $i + 1])[1] . '<br />';
      }     
      if (strpos($rows[intval($line) + $i], 'Secured Party') !== false){
        echo "Secured Party - " . explode(':', $rows[intval($line) + $i + 1])[1] . '<br />';
      }   
      if (strpos($rows[intval($line) + $i], 'Filing Number') !== false){
        echo "Filing Number - " . explode(':', $rows[intval($line) + $i])[1] . '<br />';
      }         
      if (strpos($rows[intval($line) + $i], 'Expiration Date') !== false){
        echo "Expiration Date - " . explode(':', $rows[intval($line) + $i])[1] . '<br />';
      }         
    }     
  echo '</div>';      
  }     
  
	function printJudgments($data, $value, $rows, $line){
    echo '<div class="panel-heading">';       
	$judgmentLine = 0;
	foreach($rows as $row => $data){
    $judgmentLine++;
    $judgementArray = array();
    if (strpos($data, 'Judgments') !== false && $data != 'Judgments: Yes'){
      $found = explode("(", $data);
        $iterations = intval($found[1][0]) * 30;
        echo '<b><u>Judgments Reported</u></b><br />';
        echo '</div><div class="panel-body">';           
        for ($i = 1; $i <= $iterations; $i++) {       
          if (strpos($rows[intval($judgmentLine) + $i], 'Court Case Number') !== false){
            echo 'Case number' . explode(':', $rows[intval($judgmentLine) + $i])[1] . '<br />';
          }
          if (strpos($rows[intval($judgmentLine) + $i], 'Filing County') !== false){
            echo 'Filed - ' . explode(':', $rows[intval($judgmentLine) + $i])[1] . '<br />';
          }     
          if (strpos($rows[intval($judgmentLine) + $i], 'Total Judgment Amount') !== false){
            echo "Judgment Amount - " . explode(':', $rows[intval($judgmentLine) + $i])[1] . '<br />';
          }   
          if (strpos($rows[intval($judgmentLine) + $i], 'Plaintiff') !== false){
            echo "Plaintiff - " . explode(':', $rows[intval($judgmentLine) + $i])[1] . '<br />';
          }               
        }  
      }
    }
  echo '</div>';      
	}
  
  function printBankruptcies($data, $value, $rows, $line){
    echo '<div class="panel-heading">';       
    $found = explode("(", $data);
    $iterations = intval($found[1][0]) * 30;
    echo '<b><u>Bankruptcies reported</u></b><br />';
    echo '</div><div class="panel-body">';      
    for ($i = 1; $i <= $iterations; $i++) {       
      if (strpos($rows[intval($line) + $i], 'Name') !== false){
        echo explode(':', $rows[intval($line) + $i])[1] . '<br />';
      }
      if (strpos($rows[intval($line) + $i], 'Chapter') !== false){
        echo 'Chapter ' . explode(':', $rows[intval($line) + $i])[1] . ' Bankruptcy' . '<br />';
      }     
      if (strpos($rows[intval($line) + $i], 'Court District') !== false){
        echo "Court - " . explode(':', $rows[intval($line) + $i])[1] . '<br />';
      }   
      if (strpos($rows[intval($line) + $i], 'Case Number') !== false){
        echo "Case Number - " . explode(':', $rows[intval($line) + $i])[1] . '<br />';
      }         
      if (strpos($rows[intval($line) + $i], 'Closed Date') !== false){
        echo "Closed - " . explode(':', $rows[intval($line) + $i])[1] . '<br />';
      }         
    }     
  echo '</div>';          
  }   

  function printProfessionalLicenses($data, $value, $rows, $line){
    echo '<div class="panel-heading">';      
    $found = explode("(", $data);
    echo '<b><u>Professional licenses reported</u></b><br />';
    echo '</div><div class="panel-body">';        
    echo 'Subject has the following reported professional licenses<br />';
    $iterations = intval($found[1][0]) * 30;
    for ($i = 1; $i <= $iterations; $i++) {      
      if (strpos($rows[intval($line) + $i], 'Phone') !== false){
        echo $rows[intval($line) + $i - 2] . '<br />';
      }
      if (strpos($rows[intval($line) + $i], 'License Type') !== false){
        echo "License Type - " . explode(':', $rows[intval($line) + $i])[1] . '<br />';
      }
      if (strpos($rows[intval($line) + $i], 'License State') !== false){
        echo "License State - " . explode(':', $rows[intval($line) + $i])[1] . '<br />';
      }     
      if (strpos($rows[intval($line) + $i], 'License Number') !== false){
        echo "License Number - " . explode(':', $rows[intval($line) + $i])[1] . '<br />';
      }   
      if (strpos($rows[intval($line) + $i], 'License Status') !== false){
        echo "License status - " . explode(':', $rows[intval($line) + $i])[1] . '<br />';
      }          
    }    
  echo '</div>';         
  } 
  
  function printProperties($data, $value, $rows, $line){
    echo '<div class="panel-heading">';     
    $found = explode("(", $data);
    echo '<u><b>' . $value[0] . '</u></b><br />';    
    $iterations = intval($found[1][0]) * 23;
    echo 'Subject listed as owning following property<br />';
    echo '</div><div class="panel-body">';            
    for ($i = 1; $i <= $iterations; $i++) {   
      if (strpos($rows[intval($line) + $i], 'Mailing Address') !== false){
        echo explode(':', $rows[intval($line) + $i])[1] . '<br />';
      }
      if (strpos($rows[intval($line) + $i], 'APN:') !== false){
        echo 'APN' . explode(':', $rows[intval($line) + $i])[1] . '<br />';
      }
      if (strpos($rows[intval($line) + $i], 'Mortgage Information not available') !== false){
        echo $rows[intval($line) + $i]  . '<br />';
      }      
      if ($rows[intval($line) + $i] == 'Mortgage'){
        $iterations = $iterations + 15;
        echo 'Mortgage'  . '<br />';
        for ($j = 1; $j <= 11; $j++) {  
          if ($rows[intval($line) + $i + $j] == 'Mortgage' ){
            break;
          }else if(strpos($rows[intval($line) + $i + $j], 'Past Property Deeds') !== false){
            break 2;
          }else{
            echo $rows[intval($line) + $i + $j]  . '<br />';
          }
        }
      }   
    }     
  echo '</div>';         
  }  
  
  function printDL($data, $value, $rows, $line){
    echo '<div class="panel-heading">';        
    echo '<b><u>DL </u></b>';
    echo '</div><div class="panel-body">';      
    for ($i = 1; $i <= 10; $i++) {
      if (strpos($rows[intval($line) + $i], 'DL Number') !== false){
        echo 'DL# ' . str_replace('Issuing State', '', explode(":", $rows[intval($line) + $i])[1]) . '<br />';
        echo $rows[intval($line) + $i - 2] . '<br />';
        echo $rows[intval($line) + $i - 1] . '<br />';
      }
      if (strpos($rows[intval($line) + $i], 'Date of Birth') !== false){
        echo 'Date of Birth' . explode(',', explode(':', $rows[intval($line) + $i])[1])[0] . '<br />';
      }
    }     
  echo '</div>';         
  }
  
	function printBusinessAffiliation($data, $value, $rows, $line){
    echo '<div class="panel-heading">';      
    echo '<b><u>Possible Business Affiliations</u></b><br />';
    echo '</div><div class="panel-body">'; 
    echo 'Subject reported to be affiliated with<br />';
    echo explode("(", $rows[intval($line) + 2])[0] . ', ';
    for ($i = 1; $i <= 10; $i++) {
      if (strpos($rows[intval($line) + $i], 'Link Number') !== false){
        echo explode("(", $rows[intval($line) + $i + 1])[0] . '<br />';
      }
    }  
  echo '</div>';       
	}

	function printVehicles($data, $value, $rows, $line){
    echo '<div class="panel-heading">';     
    $vehicleArray = array();
    $found = explode("(", $data);
    echo '<b><u>' . $value[0] . '</u></b><br />';   
    echo '</div><div class="panel-body">'; 
    $vehicleLines = 100;
    $iterations = intval($found[1][0]) * $vehicleLines;
    for ($i = 1; $i <= $iterations; $i++) {
      if(isset($rows[$i + $line])){
        $colonParse = explode(":", $rows[$i + $line]);
        if (count($colonParse) > 1 && strpos($colonParse[1], 'Model') !== false){
          $vehicleArray[0] = trim(explode("-", $colonParse[0])[0]);
          $vehicleArray[1] = trim($colonParse[2]);
        }elseif ($colonParse[0] == 'Most Current Tag #'){
          $vehicleArray[2] = explode("Valid", trim($colonParse[1]))[0];
        }
        if ($rows[$i + $line] == 'Lien Holders'){
          $vehicleArray[3] = $rows[$i + $line + 1];
          $vehicleArray[4] = $rows[$i + $line + 2];
        }
        if ($rows[$i + $line] == 'Title Holders'){
          $vehicleArray[5] = $rows[$i + $line + 1];
        }      
        if (count($vehicleArray) == 6){
          echo $vehicleArray[0] . ' ' . $vehicleArray[1] . ' Lic. Pt. ';
          echo $vehicleArray[2] . '<br />';
          echo 'Lien Holder - ' . $vehicleArray[3] . ', '. $vehicleArray[4] . '<br />';
          echo 'Title in name of ' . $vehicleArray[5] . '<br />';
          $vehicleArray = array();
        }
      }
    }
  echo '</div>';     
	}

	function printMultiline($data, $value, $rows, $line){
    echo '<div class="panel-heading">';  
    $found = explode("(", $data);
    echo '<u><b>' . $value[0] . '</u></b><br />';
    echo '</div><div class="panel-body">'; 
    $iterations = intval($found[1][0]) * intval($value[1][1] . $value[1][2]);
    for ($i = 1; $i <= $iterations; $i++) {
      $str = explode("(", $rows[$i + $line]);
      if ($str == ""){
        $iterations++;
      }
      if (strpos($str[0], 'Phone') !== false && isset($str[1])){
        echo str_replace(')', '', $str[0] . $str[1]);
      }else{
        echo $str[0];
      }
      echo '<br />';   
    }
  echo '</div>';       
	}

	function printColon($data, $value){
    echo '<div class="panel-heading">';      
    $found = explode(":", $data);
    if (count($value) == 2 && is_int($value[1])){
      echo '<u><b>' . $value[0] . '</u></b>  ' . substr(trim($found[1]), 0, $value[1]) . '<br />';
    }else{
      echo '<u><b>' . $value[0] . '</u></b>' . $found[1] . '<br />';
    }  
    echo '</div>'; 
	}

	function printName($rows, $names){
    $maxline = 0;
  echo '<div class="panel panel-default">';    
    echo '<div class="panel-heading">';  
    foreach($rows as $row => $data){
      $maxline++;
      if(strpos($data, "Name") === 0 ){
        array_push($names, trim(explode("(", explode(":", $data)[1])[0]));
      }
      if ($maxline == 40){
        break;
      }
    }
    
    $map_names = array_map('strlen', $names);
    echo $names[array_search(min($map_names), $map_names)] . " [" . $names[array_search(max($map_names), $map_names)] . "]<br />";
    echo '</div></div>';     
    }
    
    // main function
    printName($rows, $names);
    foreach ($values_map as $key => $value) {
      $line = 0;
      foreach($rows as $row => $data){
      $line++;
        if (strpos($data, $key) === 0 ){
          if (strpos($data, 'No') !== false || strpos($data, "None" !== false)){
          array_push($no_record, $value[0]);
          }else{
            //checks for flags
            echo '<div class="panel panel-default">';
            if (strpos($data,':') !== false && count($value) <= 2){
              printColon($data, $value);
            }elseif($value[1][0] == "m" && strpos($data,'(') !== false){
              printMultiline($data, $value, $rows, $line);
            }elseif($value[1][0] == "f"){
              //calls function that is specified in array
              $value[2]($data, $value, $rows, $line);
            }else{
              echo $data;
              echo '<br />';   
            }
            echo "</div>";
          }
        break;
        }
    }
	}
  //prints values that did not contain any information
	$no_record_clean = array_unique($no_record);
  echo '<div class="panel panel-default">';
  echo '<div class="panel-heading">';  
	echo '<br /><b><u> Other areas checked with no records found</b></u><br />';
  echo '</div><div class="panel-body">';   
	foreach ($no_record_clean as $value){
    echo $value . '<br />';
	}
  echo '</div></div>';
}
?>
</div>
</div>
</div>
</body>
</html>