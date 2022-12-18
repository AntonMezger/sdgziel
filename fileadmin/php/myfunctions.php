<?php

// Defining constants
define("_en", 3);
define("_fr", 1);
define("_de", 0);
define("_it", 2);

function getUID($params, $ths)
{
    $uid = $params['uid'];
    //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($uid, "getuid uid");
    return $uid;
}

  function getL($params, $ths)
{
    $L = $params['L'];
    //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($uid, "getuid uid");
    return $L;
}

   function getError($params, $ths)
{
    $error = $params['error'];
    //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($uid, "getuid uid");
    if(isset($error)) return true;
    else return false;
}

/**
 * function testing the existence of a patient through its index
 */
function existPatientRecord($uid)
{
    $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
    if (!is_null($uid)) {
        $temp = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("*", "mytable", "uid=" . $uid);
    } else {
        return false;
    }
    //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "existpatientrecord " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
    
    if ($GLOBALS['TYPO3_DB']->sql_num_rows($temp)) {
        return true;
    } else {
        return false;
    }
}

function existUserRecord($uid)
{
    $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
    if (!is_null($uid)) {
        $temp = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("*", "fe_users", "uid=" . $uid);
    } else {
        return false;
    }
    //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "existuserrecord " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
    
    if ($GLOBALS['TYPO3_DB']->sql_num_rows($temp)) {
        return true;
    } else {
        return false;
    }
}

function getPatientData($uid)
{
    $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
    if (!is_null($uid)) {
        $temp = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("*", "mytable", "uid=" . $uid);
    } else {
        return null;
    }
    //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "getformdata " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
    
    if ($GLOBALS['TYPO3_DB']->sql_num_rows($temp)) {
        $row                         = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($temp);
        $output["uid"]               = $row['uid' . $field];
        $output["userid"]            = $row['userid' . $field];
        $output["year"]              = $row['year' . $field];
        $output["association"]       = $row['association' . $field];
        $output["berater"]           = $row['berater' . $field];
        $output["patient"]           = $row['patient' . $field];
        $output["beratungsdatum"]    = $row['beratungsdatum' . $field];
        $output["creation_date"]     = $row['creation_date' . $field];
        $output["modification_date"] = $row['modification_date' . $field];
        $output["zielerreichung"]    = $row['zielerreichung' . $field];
        $output["Text"]              = $row['Text' . $field];
    }
    return $output;
}

function getWhere($year, $berater, $association, $patient)
{
        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
        $superuser="Superuser";
        if(in_array($superuser, $GLOBALS['TSFE']->fe_user->groupData['title'])){ 
            if(strlen($association) < 2)  {
               $where = "WHERE";  
             } else {
               $where = "WHERE association='" . trim($association) ."'";
             }
        } else {    
             $where = "WHERE association='" . $GLOBALS["TSFE"]->fe_user->user["username"] ."'";                           
        }    
//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($where, "where"); 

        if(strlen($year)> 1) $where .= " AND year=" .  trim($year);
        if(strlen($berater)> 1) $where .= " AND berater= " . "'" . trim($berater) . "'" ;
        $patient = str_replace("'","''",$patient);
        if(strlen($patient)> 1) $where .= " AND patient= " . "\"" . trim($patient) . "\"" ;  
        
//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($where, "where"); 

        $from = '/' . preg_quote("WHERE AND", '/') . '/';
        $to   = "  ";
        $where = preg_replace($from, $to, $where, 1);
        $from = '/' . preg_quote("WHERE", '/') . '/';
        $to   = "  ";
        $where = trim(preg_replace($from, $to, $where, 1));
        //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "getWhere " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> '; 
        return $where;
}

function getPatientDataUID($year, $berater, $association, $patient)
{
        $where = getWhere($year, $berater, $association, $patient);
        $select = "uid as value, uid as caption";
        $group = "uid";
        $order = "uid  ASC";
        $aData = $GLOBALS["TYPO3_DB"]->exec_SELECTgetRows($select, "mytable", $where, $group, $order);
        //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "getPatientDataUID " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
        $extras[0] = array("value" => "0", "caption" => " ");                          // a blank field
        return  array_merge($extras, $aData);
}

function getPatientDataBerater($year, $berater, $association, $patient)
{
        $where = getWhere($year, $berater, $association, $patient);
        $select = "berater as value, berater as caption";
        $group = "berater";
        $order = "berater  ASC";
        $aData = $GLOBALS["TYPO3_DB"]->exec_SELECTgetRows($select, "mytable", $where, $group, $order);
        //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "getPatientDataUID " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
        $extras[0] = array("value" => " ", "caption" => " ");                          // a blank field
        return  array_merge($extras, $aData);
}

function getPatientDataPatient($year, $berater, $association, $patient)
{
        $where = getWhere($year, $berater, $association, $patient);
        $select = "patient as value, patient as caption";
        $group = "patient COLLATE utf8_bin";
        $order = "patient  ASC";
        $aData = $GLOBALS["TYPO3_DB"]->exec_SELECTgetRows($select, "mytable", $where, $group, $order);
        //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "getPatientDataUID " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
        $extras[0] = array("value" => " ", "caption" => " ");                          // a blank field
        return  array_merge($extras, $aData);
}

  function getBeraterData($uid)
{
    $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
    if (!is_null($uid)) {
        $temp = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("*", "mytable_berater", "uid=" . $uid);
    } else {
        return null;
    }
    //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "getuserdata " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
    
    if ($GLOBALS['TYPO3_DB']->sql_num_rows($temp)) {
        $row                         = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($temp);
        $output["uid"]               = $row['uid' . $field];
        $output["association"]       = $row['association' . $field];
        $output["berater"]           = $row['berater' . $field];
    }
    return $output;
}

function getUserData($uid)
{
    $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
    if (!is_null($uid)) {
        $temp = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("*", "fe_users", "uid=" . $uid);
    } else {
        return null;
    }
    //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "getuserdata " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
    
    if ($GLOBALS['TYPO3_DB']->sql_num_rows($temp)) {
        $row                         = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($temp);
        $output["uid"]               = $row['uid' . $field];
        $output["username"]          = $row['username' . $field];
        $output["password"]          = $row['password' . $field];
        $output["description"]       = $row['description' . $field];  
        $output["name"]              = $row['name' . $field];
        $output["first_name"]        = $row['first_name' . $field];
        $output["title"]             = $row['title' . $field];
        $output["address"]           = $row['address' . $field];
        $output["email"]             = $row['email' . $field];
        $output["city"]              = $row['city' . $field];
        $output["country"]           = $row['country' . $field];
        $output["zip"]               = $row['zip' . $field];
        $output["company"]           = $row['company' . $field];
        $output["usergroup"]         = $row['usergroup' . $field];
    }
    return $output;
}


function updatePatientRecord($uid, $recdata, $exist)
{
    if (empty($recdata)) { 
        return;
    }

     $fields = array(
        "userid"   => $recdata["userid"], 
        "year" => $recdata["year"],
        "association" => $recdata["association"],
        "berater" => $recdata["berater"],
        "patient" => $recdata["patient"],
        "beratungsdatum" => $recdata["beratungsdatum"],
        "creation_date" => $recdata["creation_date"],
        "modification_date" => $recdata["modification_date"],
        "zielerreichung" => $recdata["zielerreichung"],
        "Text" => $recdata["Text"]
    );   
        
    $where  = "uid=" . intval($uid);
    
    if (!$exist) {
        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
        $res = $GLOBALS['TYPO3_DB']->exec_INSERTquery("mytable", $fields);
        //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "insertmytable " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
        $lastID = $GLOBALS['TYPO3_DB']->sql_insert_id();
    } else {
        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
        $res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery("mytable", $where, $fields);
        //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "updatemytable " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
        $lastID = $uid;
    }
    return $lastID;
}

 function updateUserRecord($uid, $recdata, $exist)
{
    if (empty($recdata)) { 
        return;
    }
      /*
    echo var_dump($recdata['name'], "name before");
    $recdata['name'] = str_replace("&quot;", '"', $recdata['name']);
    $recdata['first_name'] = str_replace("&quot;", '"', $recdata['first_name']);  
    echo "<br/>";
    echo var_dump($recdata['name'], "name after");
     */
    $size = count($recdata);
    //echo var_dump($recdata, "array");
    
    foreach ($recdata as &$value) {
        $value = str_replace("&quot;", '"', $value);       
    }
    //echo var_dump($recdata, "array");
     
    $where  = "uid=" . intval($uid);
    
    $pid = "2";
    $company = "";
    
    if(!isset($recdata['first_name'])) $recdata['first_name'] ="";
    if(!isset($recdata['title'])) $recdata['title'] =""; 
    if(!isset($recdata['address'])) $recdata['address'] ="";  
    if(!isset($recdata['city'])) $recdata['city'] =""; 
    if(!isset($recdata['country'])) $recdata['country'] =""; 
    if(!isset($recdata['zip'])) $recdata['zip'] =""; 
    
    if (!$exist) {
        
      $fields = array( 
        "pid"               =>  $pid,
        "username"          => $recdata['username'],
        "password"          => $recdata['password'],
        "description"       => $recdata['description'],  
        "name"              => $recdata['name'],
        "first_name"        => $recdata['first_name'],
        "title"             => $recdata['title'],
        "address"           => $recdata['address'],
        "email"             => $recdata['email'],
        "city"              => $recdata['city'],
        "country"           => $recdata['country'],
        "zip"               => $recdata['zip'],
        "company"           => $company,
        "usergroup"         => $recdata['usergroup']   
        );         
        
        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
        $res = $GLOBALS['TYPO3_DB']->exec_INSERTquery("fe_users", $fields);
        //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "insertmytable " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
        $lastID = $GLOBALS['TYPO3_DB']->sql_insert_id();
    } else {
        
      $fields = array(
         "pid"               =>  $pid,    
        "uid"               => $recdata["uid"],   
        "username"          => $recdata['username'],
        "password"          => $recdata['password'],
        "description"       => $recdata['description'], 
        "name"              => $recdata['name'],
        "first_name"        => $recdata['first_name'],
        "title"             => $recdata['title'],
        "address"           => $recdata['address'],
        "email"             => $recdata['email'],
        "city"              => $recdata['city'],
        "country"           => $recdata['country'],
        "zip"               => $recdata['zip'],
        "company"           => $company,
        "usergroup"         => $recdata['usergroup']   
        );          
         
        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
        $res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery("fe_users", $where, $fields);
        //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "updatemytable " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
        $lastID = $uid;
    }
    return $lastID;
}

function getCodesFromTable($recCounter) 
{
    
    if(is_null($recCounter)) return "";
    
    switch ($GLOBALS['TSFE']->sys_language_uid) {
        case _en:
            $field = "_en";
            break;
        case _fr:
            $field = "_fr";
            break;
        case _de:
            $field = "_de";
            break;
        case _it:
            $field = "_it";
            break;
    }
    $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
    $temp = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("*", "mytable_theme", "recCounter=" . $recCounter); 
    
    $output = array();
    
    if ($GLOBALS['TYPO3_DB']->sql_num_rows($temp)) {
        $row       = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($temp);
        $output[0] = $row['explication' . $field];
        $output[1] = $row['sousexplication' . $field];
        $output[2] = $recCounter;
    }
    return $output;
}

// insert into the user table the theme and subtheme

function updateThemesTable($uid)
{
    // first delete themes in table
    $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
    $res = $GLOBALS['TYPO3_DB']->exec_DELETEquery("mytable_userthemes", "uid=" . intval($uid));
    //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "updatethemestable " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
    
    // get themes and subthemes from temporary table
    $temp = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("*", "mytable_temporarythemes", "");
    
    // output my themes to table user themes
   if ($GLOBALS['TYPO3_DB']->sql_num_rows($temp)) {
        while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($temp)) {    
             $insertArray = array(
                "uid" => $uid,
                "recCounter" => $row['recCounter'],
            ); 

            $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
            $res = $GLOBALS['TYPO3_DB']->exec_INSERTquery("mytable_userthemes", $insertArray);
            //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "updatethemestable " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> '; 
        }       
    } 
}

//  get from the user table the themes and subthemes into the array
function getThemesFromTable($uid)
{
    $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
    if (!is_null($uid)) {
        $temp = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("*", "mytable_userthemes", "uid=" . $uid);
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($temp, "temp");
    } else {
        
    }
 
    $output = "";
    if ($GLOBALS['TYPO3_DB']->sql_num_rows($temp)) {
        while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($temp)) {
            $themes = appendThemes($row['recCounter']);
            $output = $output . $themes . "<br/>";
        }
    }
    
    return $output; 
}

function getThemesFromTablePDF($uid)
{
    $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
    if (!is_null($uid)) {
        $temp = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("*", "mytable_userthemes", "uid=" . $uid);
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($temp, "temp");
    } else {  
    }
 
    $output = array();
    $i = 0;
    if ($GLOBALS['TYPO3_DB']->sql_num_rows($temp)) {
        while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($temp)) {
            $themes = appendThemesPDF($row['recCounter']);
            $output[$i]= $themes[0] . " " . $themes[1] . "<br/>";
            $i++;
        }
    }  
    
    return $output;  
}

//  get from the user table the themes and subthemes into the array
function getThemesFromTableX($code, $souscode)
{  
    $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;

    $temp = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("*", "mytable_theme", "code=" . $code . " and souscode=" . $souscode);

    if ($GLOBALS['TYPO3_DB']->sql_num_rows($temp)) {
        while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($temp)) {
            $themes = appendThemes($row['RecCounter']);
            return;
        }
    }   
    return;  
}

function truncateTemporaryThemesTable()
{
    $res = $GLOBALS['TYPO3_DB']->exec_TRUNCATEquery("mytable_temporarythemes"); 
} 

// append to the array the choosen theme and subtheme
function appendThemes($recCounter) 
{     
            if(is_null($recCounter)) return;
            $themes = array(); 
            $themes = getCodesFromTable($recCounter);  
            $fields = array(
                "`theme`"    => $themes[0], 
                "`subtheme`" => $themes[1],
                "`recCounter`" =>  $themes[2]               
           );    
           $res = $GLOBALS['TYPO3_DB']->exec_INSERTquery("mytable_temporarythemes", $fields); 
           return; 
}

function appendThemesPDF($recCounter) 
{     
            if(is_null($recCounter)) return;
            $themes = array(); 
            $themes = getCodesFromTable($recCounter);  
            return $themes; 
}

function appendThemesX($code, $souscode)
{  
            if($code < 0) return;
            if($souscode < 0) $souscode = $code * 100;
            getThemesFromTableX($code, $souscode);
            return;      
}

function currentHelpPDF($languageCode, $source)
{
      switch ($languageCode) {
        case _en:
            if($source === "listeredit_patient")      $userString = "fileadmin/pdf/sdgziel_patient_liste_de.pdf";
            else if($source === "listeredit_berater") $userString = "fileadmin/pdf/sdgziel_berater_de.pdf";
            else if($source === "saisie_patient")     $userString = "fileadmin/pdf/sdgziel_patient_saisie_de.pdf";
            else if($source === "saisie_berater")     $userString = "fileadmin/pdf/sdgziel_berater_de.pdf";
            else if($source === "saisie_berater")     $userString = "fileadmin/pdf/sdgziel_berater_de.pdf";
            break;
        case _de:
            if($source === "listeredit_patient")      $userString = "fileadmin/pdf/sdgziel_patient_liste_de.pdf";
            else if($source === "listeredit_berater") $userString = "fileadmin/pdf/sdgziel_berater_de.pdf"; 
            else if($source === "saisie_patient")     $userString = "fileadmin/pdf/sdgziel_patient_saisie_de.pdf";
            else if($source === "saisie_berater")     $userString = "fileadmin/pdf/sdgziel_berater_de.pdf"; 
            break;
        case _fr:
            if($source === "listeredit_patient")      $userString = "fileadmin/pdf/sdgziel_patient_liste_fr.pdf";
            else if($source === "listeredit_berater") $userString = "fileadmin/pdf/sdgziel_berater_fr.pdf"; 
            else if($source === "saisie_patient")     $userString = "fileadmin/pdf/sdgziel_patient_saisie_fr.pdf";
            else if($source === "saisie_berater")     $userString = "fileadmin/pdf/sdgziel_berater_fr.pdf"; 
            break;
        case _it:
            if($source === "listeredit_patient")      $userString  ="fileadmin/pdf/sdgziel_patient_liste_fr.pdf";
            else if($source === "listeredit_berater") $userString = "fileadmin/pdf/sdgziel_berater_fr.pdf"; 
            else if($source === "saisie_patient")     $userString = "fileadmin/pdf/sdgziel_patient_saisie_fr.pdf";
            else if($source === "saisie_berater")     $userString = "fileadmin/pdf/sdgziel_berater_fr.pdf"; 
            break;
       }
       return utf8_encode($userString);
}

function currentFilter($languageCode)
{
    switch ($GLOBALS['TSFE']->sys_language_uid) {
          case _en:
               $html = "Filter: " ;
               break;
          case _de:
               $html = "Abfrage: " ;
               break;
          case _fr:
               $html = "Filtre: " ;
               break;
          case _it:
               $html = "Filtro: " ;
               break;
          }
          return  $html;
}

function currentUser($languageCode)
{
    switch ($languageCode) {
        case _en:
            $userString = "Actual user: " .  $GLOBALS["TSFE"]->fe_user->user["username"];
            break;
        case _de:
            $userString = "Aktueller Benützer: " .  $GLOBALS["TSFE"]->fe_user->user["username"];
            break;
        case _fr:
            $userString = "Utilisateur actuel: " .  $GLOBALS["TSFE"]->fe_user->user["username"];
            break;
        case _it:
            $userString  = "Utente corrente: " .  $GLOBALS["TSFE"]->fe_user->user["username"];
            break;
       }
       return utf8_encode($userString);
}

function currentHelp($languageCode)
{
       switch ($languageCode) {                             
           case _en:
                $helpString = "Help";
                break;
           case _de:
                $helpString = "Hilfe";
                break;
           case _fr:
                $helpString = "Aide";
                break;
           case _it:
                $helpString = "Assistenza";
                break;
       }
       return utf8_encode($helpString);
}

function currentRemark($languageCode)
{
       switch ($languageCode) {                             
           case _en:
                $helpString = "if table contains wrong language, please change page";
                break;
           case _de:
                $helpString = "wenn Tabelle falsche Sprache enthält, bitte Seite blättern";
                break;
           case _fr:
                $helpString = "si la table contient la mauvaise langue, s.v.p. changer de page";
                break;
           case _it:
                $helpString = "se la tabella contiene la lingua sbagliata, si prega di spostare la pagina";
                break;
       }
       return utf8_encode($helpString);
}

function languageSelected($languageCode)
{
    switch ($languageCode) {
        case _en:
            $languageString = _en;
            break;
        case _de:
            $languageString = _de;
            break;
        case _fr:
            $languageString = _fr;
            break;
        case _it:
            $languageString = _it;
            break;
        }
        return $languageString;
}

function languageMessage($type, $store)
{     
    switch ($type) {
        
        case "modify":
            switch ($GLOBALS['TSFE']->sys_language_uid) {
                case _en:
                    $html = "Record modified";
                    break;
                case _de:
                    $html = "Datensatz geändert";
                    break;
                case _fr:
                    $html = "Enregistrement modifié";
                    break;
                case _it:
                    $html = "Record modificato";
                    break;
            }
            break;
        
        case "create":
            switch ($GLOBALS['TSFE']->sys_language_uid) {
                case _en:
                    $html = "Record created";
                    break;
                case _de:
                    $html = "Datensatz erstellt";
                    break;
                case _fr:
                    $html = "Enregistrement créé";
                    break;
                case _it:
                    $html = "Record creato";
                    break;
            }
            break;
        
        case "error":
            switch ($GLOBALS['TSFE']->sys_language_uid) {
                case _en:
                    $html = "The fields with a * are mandatory";
                    break;
                case _de:
                    $html = "Die Felder mit * müssen eingegeben werden";
                    break;
                case _fr:
                    $html = "Les champs avec une * sont obligatoires";
                    break;
                case _it:
                    $html = "I campi contrassegnati con un * sono obbligatori";
                    break;
            }
            break;
            
        case "checkpassword":
            $html = "error, the given passwords do not match";
            break;
            
    }
    $html = utf8_encode($html);
    if ($store) {
        $GLOBALS["TSFE"]->fe_user->setKey("ses", "confirmation_message", $html);
        $GLOBALS["TSFE"]->fe_user->storeSessionData();
    } 
    
    return $html;
}

function languageModalMessages($type, $patient, $similarPatient)
{     
    switch ($type) {
        case "warning": 
           switch ($GLOBALS['TSFE']->sys_language_uid) {  
                case _en:
                    $html = "<span class='modal-warning'>PatientIn '" . $patient . "' is very similar to '" . $similarPatient . "' !! do you really want to add it?</span>";
                    break;
                case _de:
                    $html = "<span class='modal-warning'>PatientIn '" . $patient . "' ist sehr ähnlich wie '" . $similarPatient . "' !! wollen Sie wirklich diesen Namen hinzufügen?</span>"; 
                    break;
                case _fr:
                    $html = "<span class='modal-warning'>PatientIn '" . $patient . "' est très similaire à '" . $similarPatient . "' !! voulez-vous vraiment l' ajouter?</span>";
                    break;
                case _it:
                    $html = "<span class='modal-warning'>PatientIn '" . $patient . "' è molto simile a '" . $similarPatient . "' !! Vuoi davvero aggiungerlo?</span>";  
                    break;
           }
            break;
            
        case "ok":
           switch ($GLOBALS['TSFE']->sys_language_uid) {  
                case _en:
                    $html = "<span class='modal-ok'>PatientIn '" . $patient . "' can be added to the patient list</span>"; 
                    break;
                case _de:
                    $html = "<span class='modal-ok'>PatientIn '" . $patient . "' kann zu PatientIn Liste hinzugefügt werden</span>"; 
                    break;
                case _fr:
                    $html = "<span class='modal-ok'>PatientIn '" . $patient . "' peut être ajouté à la liste</span>"; 
                    break;
                case _it:
                    $html = "<span class='modal-ok'>PatientIn '" . $patient . "' può essere aggiunto all'elenco dei pazienti</span>";
                    break;
            }
            break; 
            
        case "empty":
           switch ($GLOBALS['TSFE']->sys_language_uid) {  
                case _en:
                    $html = "<span class='modal-warning'>the field 'PatientIn' has not been filled out</span>";
                    break;
                case _de:
                    $html = "<span class='modal-warning'>das Feld 'PatientIn' ist leider nicht ausgefüllt</span>";  
                    break;
                case _fr:
                    $html = "<span class='modal-warning'>le champ 'PatientIn' n'a pas été entré</span>";
                    break;
                case _it:
                    $html = "<span class='modal-warning'>il campo 'PatientIn' non è stato compilato</span>";
                    break;
            }        
            break; 
            
        case "error":
           switch ($GLOBALS['TSFE']->sys_language_uid) {  
                case _en:
                    $html = "<span class='modal-error'>PatientIn '" . $patient . "' is already present in the list</span>";  
                    break;         
                case _de:
                    $html = "<span class='modal-error'>PatientIn '" . $patient . "' ist leider schon vorhanden</span>";   
                    break;
                case _fr:
                    $html = "<span class='modal-error'>PatientIn '" . $patient . "' est déjà présent dans la liste</span>";  
                    break;
                case _it:
                    $html = "<span class='modal-error'>PatientIn '" . $patient . "' è già presente nella lista</span>";  
                    break;  
            }       
            break; 
                          
        case "title":
            switch ($GLOBALS['TSFE']->sys_language_uid) {
                case _en:
                    $html = "Add a new patient";
                    break;
                case _de:
                    $html = "Eingabe PatientIn";
                    break;
                case _fr:
                    $html = "ajouter un nouveau patient";
                    break;
                case _it:
                    $html = "aggiungi nuovo paziente";
                    break;
            }
            break;
        
        case "cancel":
            switch ($GLOBALS['TSFE']->sys_language_uid) {
                case _en:
                    $html = "Cancel";
                    break;
                case _de:
                    $html = "abbrechen";
                    break;
                case _fr:
                    $html = "annuler";
                    break;
                case _it:
                    $html = "annulare";
                    break;
            }
            break;
        
        case "close":
            switch ($GLOBALS['TSFE']->sys_language_uid) {
                case _en:
                    $html = "Add and Close";
                    break;
                case _de:
                    $html = "hinzufügen und schliessen";
                    break;
                case _fr:
                    $html = "ajouter et fermer";
                    break;
                case _it:
                    $html = "aggiungi e chiudi";
                    break;
            }
            break;     
    }
    $html = utf8_encode($html);

    return $html;
}


// functions for composing the lists

function composeThemes()
{
    $aData = array();
    switch ($GLOBALS['TSFE']->sys_language_uid) {
        case _en:
            $field = "explication_en";
            break;
        case _de:
            $field = "explication_de";
            break;
        case _fr:
            $field = "explication_fr";
            break;
        case _it:
            $field = "explication_it";
            break;
    }
    
    $select = " code as value, " . $field . " as caption";
    $group = $field;
    $order = $group . " ASC";
    $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
    $aData = $GLOBALS["TYPO3_DB"]->exec_SELECTgetRows($select, "mytable_theme", "", $group, $order);
    //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "composeThemes " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
    return $aData;
}

function composeSubThemes($theme)
{
    $aData = array();
    switch ($GLOBALS['TSFE']->sys_language_uid) {
        case _en:
            $fieldwhere = "explication_en";
            $field      = "sousexplication_en";
            break;
        case _de:
            $fieldwhere = "explication_de";
            $field      = "sousexplication_de";
            break;
        case _fr:
            $fieldwhere = "explication_fr";
            $field      = "sousexplication_fr";
            break;
        case _it:
            $fieldwhere = "explication_it";
            $field      = "sousexplication_it";
            break;
    }
    $fieldwhere = "code";
    $select = "souscode as value, " . $field . " as caption";
    $where = $fieldwhere . "='" . $theme . "'";
    $group = $field;
    $order = $group . " ASC";
    $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
    $aData = $GLOBALS["TYPO3_DB"]->exec_SELECTgetRows($select, "mytable_theme", $where, $group, $order);
    //echo '<i style="color:red;font-size:15px;font-family:calibri ;">' . "composeSubThemes " . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery . '</i><br> ';
    return $aData;
}
?>

