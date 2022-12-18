<?php  

 
// class csv name in class.csv.php of mkform replaced by csv_acm 
// in class.tx_ameosformidable.php CSV to CSV_ACM
// due to the fact that class names and function names can not be the same

      
class exportxlscb   
{
    function clearFields() {
        $this->oForm->aORenderlets["mysearch"]->clearFilters();
        $this->oForm->aORenderlets["mysearch"]->aChilds["year"]->setValue(Date("Y"));
    }
    
    function findItem($array, $indx)   {
      $count = count($array);
      for ($i = 0; $i < $count; $i++) {
          $code = $array[$i]['1'];
          $item =  $array[$i]['2'];    
          if($indx == $code) return $item;
       }
       return "not found";
    }
    
    function composeQuery($arr, $sSql)
    {
        $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
        $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");  
        
        $arr         = $arr[mysearch];
        $year        = trim($arr["year"]);
        $association = trim($arr["association"]);
        $berater     = trim($arr["berater"]);
        $patient     = trim($arr["patient"]);
        $datefrom    = trim($arr["datefrom"]);
        $dateto      = trim($arr["dateto"]);
        $uid         = trim($arr["uid"]); 
        
        if (strlen($year) > 0) {
            $sSql = $sSql . " AND year='" . $year . "'";
        }
        if (strlen($association) > 0) {
            $sSql = $sSql . " AND association like '" . $association . "%'";
        }
        if (strlen($berater) > 0) {
            $sSql = $sSql . " AND berater like '" . $berater . "%'";
        }
        if (strlen($patient) > 0) {
            $sSql = $sSql . " AND patient like '" . $patient . "%'";
        }
        if ($uid > 0) {
            $sSql = $sSql . " AND mytable.uid='" . $uid . "'";
        }
        
        if (strlen($datefrom) > 0) {
            $datetime = DateTime::createFromFormat('d-m-Y', $datefrom); 
            if ($datetime != FALSE) {
                $var           = $datetime->getTimestamp();
                $var_to_string = str_replace($search, $replace, $var);
                $sSql          = $sSql . " AND beratungsdatum>{$var_to_string}";
            }
        }
        if (strlen($dateto) > 0) {
            $datetime = DateTime::createFromFormat('d-m-Y', $dateto);
            if ($datetime != FALSE) {
                $var           = $datetime->getTimestamp();
                $var_to_string =  str_replace($search, $replace, $var);
                $sSql          = $sSql . " AND beratungsdatum<{$var_to_string}"; 
            }
        } 
        
        $sSql = $sSql . " ORDER BY mytable.uid ASC";
        $from = '/' . preg_quote("WHERE AND", '/') . '/';
        $to   = " WHERE ";
        $sSql = preg_replace($from, $to, $sSql, 1);
            
        $from = '/' . preg_quote("mytable AND", '/') . '/';
        $to   = "mytable WHERE ";
        $sSql = preg_replace($from, $to, $sSql, 1);
        
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($sSql, "sql");     
       
        return $sSql;

    }
    
     function doExportUser()
    {   
        require_once('fileadmin/php/myfunctions.php');
        
        $langueCode = $GLOBALS['TSFE']->sys_language_uid;
        $zRows = array();
        $query      = "SELECT company, usergroup, username, description as password, name, first_name, city, zip, address, email FROM fe_users ORDER BY company ASC";  
        
        $zSql     = $GLOBALS["TYPO3_DB"]->sql_query($query);
        $aRow     = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($zSql);
        $fields   = array();
        foreach ($aRow as $title => $value) {
            $fields[] = $title;
        }
        $zRows[] = $fields;
       
        $zSql       = $GLOBALS["TYPO3_DB"]->sql_query($query); 
        while (($zRow = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($zSql)) !== FALSE) {
            $values = array();
            foreach ($zRow as $title => $value) {
                $values[] = $value;
            }
            $zRows[] = $values;
        }  
        
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($zRows, "proprietesTableau"); 

        $sTempFilePath = $this->oForm->div_arrayToCsvFile($zRows);

        date_default_timezone_set('Europe/Berlin');
        $today = strftime("%A %d %B %Y %H:%M:%S");
        
        $ord = "?buster=" . time();
        $file =  'typo3temp/var/transient/' . basename($sTempFilePath); 
        
            switch ($langueCode) {
            case _en:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Data (csv format) was generated on ";
                $html = utf8_encode($html);  
                $html = $html . $today . " : click here</a>";
                break;
            case _de:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Daten (csv Format) ist am ";
                $html = utf8_encode($html);  
                $html = $html . $today . "  generiert worden: klicken Sie hier</a>";
                break;
            case _fr:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Données (format csv)  a été généré le ";
                $html = utf8_encode($html);  
                $html = $html . $today . " :  clicker ici</a>";
                break;
            case _it:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Data (csv format) was generated on ";
                $html = utf8_encode($html);  
                $html = $html . $today . " : click here</a>";
                break;
            }
            
            $this->oForm->aORenderlets["boxMessage"]->setHtml($html); 
                
    }
    
    function doPrintUser()
    {  
        require_once('fileadmin/php/myfunctions.php');
        include('fileadmin/php/phpToPDF.php');
        ini_set('memory_limit', '300M');
        
        $langueCode = $GLOBALS['TSFE']->sys_language_uid;
        $query      = "SELECT company, username, description as password, name, first_name, city, email FROM fe_users ORDER BY company ASC";  
        $zRows      = array();  
        $zSql       = $GLOBALS["TYPO3_DB"]->sql_query($query);
        
         $zRows = array();
        // values from mytable
        while (($zRow = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($zSql)) !== FALSE) {
            $i = 0;
            foreach ($zRow as $title => $value) {
                    $zRows[] = utf8_decode($value);
                }
        }

        $uSql      = $GLOBALS["TYPO3_DB"]->sql_query($query);
        $aRow     = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($uSql);
        $fields   = array();
        $colIndex = 0;
        foreach ($aRow as $title => $value) {
            $fields[] = $title;
        }
            
        date_default_timezone_set('Europe/Berlin');
        $todayE =  strftime("%A %d %B %Y %H:%M:%S");
     
        switch ($langueCode) {
            case _en:
                setlocale(LC_TIME, 'eng');
                $title = 'Document generated on ';
                break;
            case _de:
                $title = 'Dokument erzeugt am ';
                setlocale(LC_TIME, 'deu');
                break;
            case _fr:
                $title = 'Document généré le ';
                setlocale(LC_TIME, 'fra');
                break;
            case _it:
                $title = 'Documento generato il ';
                setlocale(LC_TIME, 'ita');
                break;
        }

        $today = strftime("%A %d %B %Y %H:%M:%S");      
        
        $PDF = new phpToPDF();
        $PDF->AddPage("l"); 
        $PDF->SetFont("Arial", "B", 12);
        $PDF->Cell(0, 5, $title . $today, 0, 1, 'C');
        $PDF->AddPage("l");
        
        // Définition des propriétés du tableau.
        $proprietesTableau = array(
            'TB_ALIGN' => 'L',
            'L_MARGIN' => 15,
            'BRD_COLOR' => array(
                0,
                92,
                177
            ),
            'BRD_SIZE' => '0.3'
        );
        
        // Définition des propriétés du header du tableau.    
        $proprieteHeader = array(
            'T_COLOR' => array(
                150,
                10,
                10
            ),
            'T_SIZE' => 10,
            'T_FONT' => 'Arial',
            'T_ALIGN' => 'C',
            'V_ALIGN' => 'T',
            'T_TYPE' => 'B',
            'LN_SIZE' => 7,
            'BG_COLOR_COL0' => array(
                170,
                240,
                230
            ),
            'BG_COLOR' => array(
                170,
                240,
                230
            ),
            'BRD_COLOR' => array(
                0,
                92,
                177
            ),
            'BRD_SIZE' => 0.2,
            'BRD_TYPE' => '1',
            'BRD_TYPE_NEW_PAGE' => ''
        );
               
        // Contenu du header du tableau.    
        $contenuHeader = array(
            25,
            40,
            40,
            50,
            30,
            30,
            45,
            $fields[0],
            $fields[1],
            $fields[2],
            $fields[3],
            $fields[4],
            $fields[5],
            $fields[6]
        );
        
        // Définition des propriétés du reste du contenu du tableau.    
        $proprieteContenu = array(
            'T_COLOR' => array(
                0,
                0,
                0
            ),
            'T_SIZE' => 10,
            'T_FONT' => 'Arial',
            'T_ALIGN_COL0' => 'L',
            'T_ALIGN' => 'R',
            'V_ALIGN' => 'M',
            'T_TYPE' => '',
            'LN_SIZE' => 6,
            'BG_COLOR_COL0' => array(
                245,
                245,
                150
            ),
            'BG_COLOR' => array(
                255,
                255,
                255
            ),
            'BRD_COLOR' => array(
                0,
                92,
                177
            ),
            'BRD_SIZE' => 0.1,
            'BRD_TYPE' => '1',
            'BRD_TYPE_NEW_PAGE' => ''
        );
        
        // Contenu du tableau.  
        $contenuTableau = $zRows; 
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($proprietesTableau, "proprietesTableau");
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($proprieteHeader, "proprieteHeader"); 
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($contenuTableau, "contenuTableau");  
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($contenuHeader, "contenuHeader");   
        $PDF->startPageNums();
        
        $PDF->drawTableau($PDF, $proprietesTableau, $proprieteHeader, $contenuHeader, $proprieteContenu, $contenuTableau);

        $file = "fileadmin/pdf/genpdf" . "_" . $GLOBALS["TSFE"]->fe_user->user["username"] . ".pdf";
        
        $PDF->Output($file, "F");
        $ord = "?buster=" . time();
        
            switch ($langueCode) {
            case _en:
                $html = "<a href='" . $file . $ord . "' target='_blank'>PDF was generated on ";
                $html = utf8_encode($html);  
                $html = $html . $todayE . " : click here</a>";
                break;
            case _de:
                $html = "<a href='" . $file . $ord . "' target='_blank'>PDF ist am ";
                $html = utf8_encode($html);  
                $html = $html . $todayE . "  generiert worden: klicken Sie hier</a>";
                break;
            case _fr:
                $html = "<a href='" . $file . $ord . "' target='_blank'>PDF a été généré le ";
                $html = utf8_encode($html);  
                $html = $html . $todayE . " :  clicker ici</a>";
                break;
            case _it:
                $html = "<a href='" . $file . $ord . "' target='_blank'>PDF was generated on ";
                $html = utf8_encode($html);  
                $html = $html . $todayE . " : click here</a>";
                break;
            }
        
        $this->oForm->aORenderlets["boxMessage"]->setHtml($html);
    } 
    
    function doExportBerater()
    {   
        require_once('fileadmin/php/myfunctions.php');
        
        $langueCode = $GLOBALS['TSFE']->sys_language_uid;
        $zRows = array();

        $query      = "SELECT association, berater FROM mytable_berater ORDER BY association ASC";  
        
        $zSql     = $GLOBALS["TYPO3_DB"]->sql_query($query);
        $aRow     = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($zSql);
        $fields   = array();
        foreach ($aRow as $title => $value) {
            $fields[] = $title;
        }
        $zRows[] = $fields;
       
        $zSql       = $GLOBALS["TYPO3_DB"]->sql_query($query); 
        while (($zRow = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($zSql)) !== FALSE) {
            $values = array();
            foreach ($zRow as $title => $value) {
                $values[] = $value;
            }
            $zRows[] = $values;
        }  
        
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($zRows, "proprietesTableau"); 

        $sTempFilePath = $this->oForm->div_arrayToCsvFile($zRows);

        date_default_timezone_set('Europe/Berlin');
        $today = strftime("%A %d %B %Y %H:%M:%S");
        
        $ord = "?buster=" . time();
        $file =  'typo3temp/var/transient/' . basename($sTempFilePath); 
        
            switch ($langueCode) {
            case _en:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Data (csv format) was generated on ";
                $html = utf8_encode($html);  
                $html = $html . $today . " : click here</a>";
                break;
            case _de:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Daten (csv Format) ist am ";
                $html = utf8_encode($html);  
                $html = $html . $today . "  generiert worden: klicken Sie hier</a>";
                break;
            case _fr:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Données (format csv)  a été généré le ";
                $html = utf8_encode($html);  
                $html = $html . $today . " :  clicker ici</a>";
                break;
            case _it:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Data (csv format) was generated on ";
                $html = utf8_encode($html);  
                $html = $html . $today . " : click here</a>";
                break;
            }
            
            $this->oForm->aORenderlets["boxMessage"]->setHtml($html); 
                
    }
    
    function doPrintBerater()
    {  
        require_once('fileadmin/php/myfunctions.php');
        include('fileadmin/php/phpToPDF.php');
        ini_set('memory_limit', '300M');
        
        $langueCode = $GLOBALS['TSFE']->sys_language_uid;
        $query      = "SELECT association, berater FROM mytable_berater ORDER BY association ASC";
        $zRows      = array();  
        $zSql       = $GLOBALS["TYPO3_DB"]->sql_query($query);
        
         $zRows = array();
        // values from mytable
        while (($zRow = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($zSql)) !== FALSE) {
            $i = 0;
            foreach ($zRow as $title => $value) {
                    $zRows[] = utf8_decode($value);
                }
        }

        $uSql      = $GLOBALS["TYPO3_DB"]->sql_query($query);
        $aRow     = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($uSql);
        $fields   = array();
        $colIndex = 0;
        foreach ($aRow as $title => $value) {
            $fields[] = $title;
        }
              
        date_default_timezone_set('Europe/Berlin');
        $todayE =  strftime("%A %d %B %Y %H:%M:%S"); 
        
        switch ($langueCode) {
            case _en:
                setlocale(LC_TIME, 'eng');
                $title = 'Document generated on ';
                break;
            case _de:
                $title = 'Dokument erzeugt am ';
                setlocale(LC_TIME, 'deu');
                break;
            case _fr:
                $title = 'Document généré le ';
                setlocale(LC_TIME, 'fra');
                break;
            case _it:
                $title = 'Documento generato il ';
                setlocale(LC_TIME, 'ita');
                break;
        }
        
        $today = strftime("%A %d %B %Y %H:%M:%S");      
        
        $PDF = new phpToPDF();
        $PDF->AddPage();
        $PDF->SetFont("Arial", "B", 16);
        $PDF->Cell(0, 5, $title . $today, 0, 1, 'C');
        $PDF->AddPage();
        
        // Définition des propriétés du tableau.
        $proprietesTableau = array(
            'TB_ALIGN' => 'L',
            'L_MARGIN' => 15,
            'BRD_COLOR' => array(
                0,
                92,
                177
            ),
            'BRD_SIZE' => '0.3'
        );
        
        // Définition des propriétés du header du tableau.    
        $proprieteHeader = array(
            'T_COLOR' => array(
                150,
                10,
                10
            ),
            'T_SIZE' => 12,
            'T_FONT' => 'Arial',
            'T_ALIGN' => 'C',
            'V_ALIGN' => 'T',
            'T_TYPE' => 'B',
            'LN_SIZE' => 7,
            'BG_COLOR_COL0' => array(
                170,
                240,
                230
            ),
            'BG_COLOR' => array(
                170,
                240,
                230
            ),
            'BRD_COLOR' => array(
                0,
                92,
                177
            ),
            'BRD_SIZE' => 0.2,
            'BRD_TYPE' => '1',
            'BRD_TYPE_NEW_PAGE' => ''
        );
               
        // Contenu du header du tableau.    
        $contenuHeader = array(
            50,
            100,
            $fields[0],
            $fields[1]
        );
        
        // Définition des propriétés du reste du contenu du tableau.    
        $proprieteContenu = array(
            'T_COLOR' => array(
                0,
                0,
                0
            ),
            'T_SIZE' => 10,
            'T_FONT' => 'Arial',
            'T_ALIGN_COL0' => 'L',
            'T_ALIGN' => 'R',
            'V_ALIGN' => 'M',
            'T_TYPE' => '',
            'LN_SIZE' => 6,
            'BG_COLOR_COL0' => array(
                245,
                245,
                150
            ),
            'BG_COLOR' => array(
                255,
                255,
                255
            ),
            'BRD_COLOR' => array(
                0,
                92,
                177
            ),
            'BRD_SIZE' => 0.1,
            'BRD_TYPE' => '1',
            'BRD_TYPE_NEW_PAGE' => ''
        );
        
        // Contenu du tableau.  
        $contenuTableau = $zRows; 
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($proprietesTableau, "proprietesTableau");
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($proprieteHeader, "proprieteHeader"); 
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($contenuTableau, "contenuTableau");  
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($contenuHeader, "contenuHeader");   
        $PDF->startPageNums();
        
        $PDF->drawTableau($PDF, $proprietesTableau, $proprieteHeader, $contenuHeader, $proprieteContenu, $contenuTableau);

        $file = "fileadmin/pdf/genpdf" . "_" . $GLOBALS["TSFE"]->fe_user->user["username"] . ".pdf";
        
        $PDF->Output($file, "F");
        $ord = "?buster=" . time();
        
         switch ($langueCode) {
            case _en:
                $html = "<a href='" . $file . $ord . "' target='_blank'>PDF was generated on ";
                $html = utf8_encode($html);
                $html = $html . $todayE . " : click here</a>";
                break;
            case _de:
                $html = "<a href='" . $file . $ord . "' target='_blank'>PDF ist am "; 
                $html = utf8_encode($html);  
                $html = $html . $todayE . "  generiert worden: klicken Sie hier</a>";
                break;
            case _fr:
                $html = "<a href='" . $file . $ord . "' target='_blank'>PDF a été généré le ";
                 $html = utf8_encode($html);  
                 $html = $html . $todayE . " :  clicker ici</a>";
                break;
            case _it:
                $html = "<a href='" . $file . $ord . "' target='_blank'>PDF was generated on ";
                $html = utf8_encode($html);  
                $html = $html . $todayE . " : click here</a>";
                break;
            }
        $this->oForm->aORenderlets["boxMessage"]->setHtml($html);
    } 
        
    
    function doExport()
    {       
        require_once('fileadmin/php/myfunctions.php');
        $type = $this->oForm->getParams();  
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(date("h:i:s"), "doexport 1");   
        
        // read first from mytable the array        
        // translate new language codes into the language code of table zielerreichung
        $langueCode = $GLOBALS['TSFE']->sys_language_uid;
        switch ($GLOBALS['TSFE']->sys_language_uid) {
        case _en:
            $langueCode1 = 0; // anglais
            break;
        case _de:
            $langueCode1 = 2; // allemand
            break;
        case _fr:
            $langueCode1 = 3; // français
            break;
        case _it:
            $langueCode1 = 5; // italien
            break;
        }        
        
        $query      = "select language, code, label from mytable_zielerreichung where language='" . $langueCode1 . "'";
        $zRows      = array();
        $zSql       = $GLOBALS["TYPO3_DB"]->sql_query($query);
        while (($zRow = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($zSql)) !== FALSE) {
            $values = array();
            foreach ($zRow as $title => $value) {
                $values[] = $value;
            }
            $zRows[] = $values;
        }  
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(date("h:i:s"), "doexport 2"); 
        
        // filters do not work, compose them ourselves
        if (empty($aFilters)) {
            $superuser = "Superuser";
            $user      = $GLOBALS['TSFE']->fe_user->groupData['title'];
            if (in_array($superuser, $user)) {
                $sSql = 'SELECT * FROM mytable';
            } else {
                $sSql = 'SELECT * FROM mytable WHERE association = ' . "'" . $GLOBALS["TSFE"]->fe_user->user["username"] . "'";
            }
            
            $arr  = $this->oForm->aRawPost[searchform];
            $sSql = $this->composeQuery($arr, $sSql);
            
            $tSql = "SELECT uid, mytable_userthemes.recCounter, mytable_userthemes.uid, code, souscode, ";
            $tSql .= "explication_de, sousexplication_de, explication_fr, sousexplication_fr FROM mytable_userthemes ";
            $tSql .= "INNER JOIN mytable_theme ON mytable_userthemes.recCounter = mytable_theme.RecCounter ";
        }
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(date("h:i:s"), "doexport 3"); 
        
        $aRows     = array();
        $zielIndex = -1;
        $rSql      = $GLOBALS["TYPO3_DB"]->sql_query($sSql);
        $uSql      = $GLOBALS["TYPO3_DB"]->sql_query($tSql);
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($sSql, "sSql"); 
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($tSql, "tSql");  
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(date("h:i:s"), "doexport 3a");      
        
        // header  from mytable
        $aRow     = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($rSql);
        $fields   = array();
        $colIndex = 0;
        foreach ($aRow as $title => $value) {
            $fields[] = $title;
            if ($title == "zielerreichung")
                $zielIndex = $colIndex;
            $colIndex = $colIndex + 1;
            if ($colIndex == 11)
                break;
        }
        $aRows[] = $fields;
        
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($sSql, "sSql");
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($fields, "fields"); 
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($aRows[0], "aRows[0]");  
        
        // reset to record 0
        $GLOBALS["TYPO3_DB"]->sql_data_seek($rSql, 0);
        
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(date("h:i:s"), "doexport 4"); 
        
        // values from mytable
        while (($aRow = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($rSql)) !== FALSE) {
            $values   = array();
            $colIndex = 0;
            foreach ($aRow as $title => $value) {
                if ($colIndex == 5 || $colIndex == 6 || $colIndex == 10) {
                    $values[] = date("d-m-y", $value);
                } else if ($colIndex == $zielIndex) {
                    $values[] = utf8_decode($this->findItem($zRows, $value));
                } else if ($colindex < 10) {
                    $values[] = utf8_decode($value);
                }
                $colIndex = $colIndex + 1;
            }
            $aRows[] = $values;
            //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($values, "values");
        }
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(date("h:i:s"), "doexport 5"); 

        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($aRows[0], "arows[0]"); 
        
        //  fields and values from userthemes 
        $bRows    = array();
        $aRow     = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($uSql);
        $fields   = array();
        $colIndex = 0;
        foreach ($aRow as $title => $value) {
            $fields[] = $title;
        }
        $bRows[] = $fields;
        
        // reset to record 0
        $GLOBALS["TYPO3_DB"]->sql_data_seek($uSql, 0);
        
        while (($aRow = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($uSql)) !== FALSE) {
            $values = array();
            foreach ($aRow as $title => $value) {
                $values[] = $value;
            }
            $bRows[] = $values;
        }

        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($aRows[0], "arows");
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($aRows[1], "arows");
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($bRows[0], "bRows");   
        //echo \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(date("h:i:s"), "doexport 6");        
        
        // writing PHP array to CSV file   
        if ($type["0"] == 1) {
            $sTempFilePath = $this->oForm->div_arrayToCsvFile($aRows);
        } else {
            $sTempFilePath = $this->oForm->div_arrayToCsvFile($bRows);
        }
        
        date_default_timezone_set('Europe/Berlin');
        $today = strftime("%A %d %B %Y %H:%M:%S");
        
        $ord = "?buster=" . time();
        $file =  'typo3temp/var/transient/' . basename($sTempFilePath);
        
        if ($type["0"] == 1) {  
        
            switch ($langueCode) {
            case _en:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Data (csv format) was generated on ";
                $html = utf8_encode($html);  
                $html = $html . $today . " : click here</a>";
                break;
            case _de:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Daten (csv Format) ist am ";
                $html = utf8_encode($html);  
                $html = $html . $today . "  generiert worden: klicken Sie hier</a>";
                break;
            case _fr:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Données (format csv)  a été généré le ";
                $html = utf8_encode($html);  
                $html = $html . $today . " :  clicker ici</a>";
                break;
            case _it:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Data (csv format) was generated on ";
                $html = utf8_encode($html);  
                $html = $html . $today . " : click here</a>";
                break;
                }
        }  else {
            
            switch ($langueCode) {
            case _en:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Themes (csv format) was generated on ";
                $html = utf8_encode($html);
                $html = $html . $today . " : click here</a>";
                break;
            case _de:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Themen (csv Format) ist am "; 
                $html = utf8_encode($html);  
                $html = $html . $today . "  generiert worden: klicken Sie hier</a>";
                break;
            case _fr:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Thèmes (format csv) a été généré le ";
                 $html = utf8_encode($html);  
                 $html = $html . $today . " :  clicker ici</a>";
                break;
            case _it:
                $html = "<a href='" . $file . $ord . "' target='_blank'>Themes (csv format) was generated on ";
                $html = utf8_encode($html);  
                $html = $html . $today . " : click here</a>";
                break;
            }
        }
  
        $this->oForm->aORenderlets["boxMessage"]->setHtml($html);
        $this->clearFields();
                                             
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////
    
    function doPrint()
    {
        require_once('fileadmin/php/myfunctions.php');
        include('fileadmin/php/phpToPDF.php');
        ini_set('memory_limit', '300M');
        
        // read first from mytable_zielerreichung the array        
        $langueCode = $GLOBALS['TSFE']->sys_language_uid;
        switch ($GLOBALS['TSFE']->sys_language_uid) {
        case _en:
            $langueCode1 = 0; // anglais
            break;
        case _de:
            $langueCode1 = 2; // allemand
            break;
        case _fr:
            $langueCode1 = 3; // français
            break;
        case _it:
            $langueCode1 = 5; // italien
            break;
        }               
        $query      = "select language, code, label from mytable_zielerreichung where language='" . $langueCode1 . "'";
        $zRows      = array();
        $zSql       = $GLOBALS["TYPO3_DB"]->sql_query($query);
        while (($zRow = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($zSql)) !== FALSE) {
            $values = array();
            $i      = 0;
            foreach ($zRow as $title => $value) {
                $values[] = $value;
                $i        = $i + 1;
            }
            $zRows[] = $values;
        }
        //echo t3lib_utility_Debug::debug($zRows); 
        
        // read data using the filter
        $aFilters = $this->oForm->rdt("mysearch")->_getFilters();
        
        // filters do not work, compose them ourselves
        if (empty($aFilters)) {
            $superuser = "Superuser";
            $user      = $GLOBALS['TSFE']->fe_user->groupData['title'];
            if (in_array($superuser, $user)) {
                $sSql = 'SELECT * FROM mytable';
            } else {
                $sSql = 'SELECT * FROM mytable  WHERE association = ' . "'" . $GLOBALS["TSFE"]->fe_user->user["username"] . "'";
            }
            
            $arr  = $this->oForm->aRawPost[searchform];
            $sSql = $this->composeQuery($arr, $sSql);
        }
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($sSql, "sql doPrint"); 
        
        $tRows     = array();
        $zielIndex = -1;
        $rSql      = $GLOBALS["TYPO3_DB"]->sql_query($sSql);
        
        // header  from mytable
        $aRow = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($rSql);
        $i    = 0;
        foreach ($aRow as $title => $value) {
            if ($i != 1 && $i != 5 && $i != 6 && $i < 10)
                $tRows[] = $title;
            if ($title == "zielerreichung")
                $zielIndex = $i;
            $i = $i + 1;
        }
        
        // reset to record 0
        $GLOBALS["TYPO3_DB"]->sql_data_seek($rSql, 0);
        
        $aRows = array();
        // values from mytable
        while (($aRow = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($rSql)) !== FALSE) {
            $i = 0;
            foreach ($aRow as $title => $value) {
                if ($i == $zielIndex) {
                    $aRows[] = "[Z]" . utf8_decode($this->findItem($zRows, $value));
                } else if ($i != 1 && $i != 5 && $i != 6 && $i < 10) {
                    $aRows[] = utf8_decode($value);
                }
                $i = $i + 1;
            }
            
            $i = 0;
            foreach ($aRow as $title => $value) {
                // get themes
                if ($i == 0) {     
                    $themes = getThemesFromTablePDF($value); 
                    $size   = count($themes);
                    if ($size > 0) {
                        for ($i = 0; $i < $size; $i++) {
                            for ($j = 0; $j < 5; $j++) {
                                if ($j == 4)
                                    $aRows[] = "[Z] ";
                                else
                                    $aRows[] = " "; // empty columns
                            }
                            $theme  = str_replace("<br/>", "\r\n", $themes[$i]);
                            $aRows[] = "[I]" . utf8_decode($theme);
                            $aRows[] = "COLSPAN2";
                        }
                    }
                    break;
                }
            }
            
        }
        
        date_default_timezone_set('Europe/Berlin');
        $todayE =  strftime("%A %d %B %Y %H:%M:%S"); 
        
        switch ($langueCode) {
            case _en:
                setlocale(LC_TIME, 'eng');
                $title = 'Document generated on ';
                break;
            case _de:
                $title = 'Dokument erzeugt am ';
                setlocale(LC_TIME, 'deu');
                break;
            case _fr:
                $title = 'Document généré le ';
                setlocale(LC_TIME, 'fra');
                break;
            case _it:
                $title = 'Documento generato il ';
                setlocale(LC_TIME, 'ita');
                break;
        }
        
        $today = strftime("%A %d %B %Y %H:%M:%S");  
        
        $this->clearFields();      
        
        $PDF = new phpToPDF();
        $PDF->AddPage("l");
        $PDF->SetFont("Arial", "B", 16);
        $PDF->Cell(0, 5, $title . $today, 0, 1, 'C');
        $PDF->AddPage("l");
        
        // Définition des propriétés du tableau.
        $proprietesTableau = array(
            'TB_ALIGN' => 'L',
            'L_MARGIN' => 15,
            'BRD_COLOR' => array(
                0,
                92,
                177
            ),
            'BRD_SIZE' => '0.3'
        );
        
        // Définition des propriétés du header du tableau.    
        $proprieteHeader = array(
            'T_COLOR' => array(
                150,
                10,
                10
            ),
            'T_SIZE' => 12,
            'T_FONT' => 'Arial',
            'T_ALIGN' => 'C',
            'V_ALIGN' => 'T',
            'T_TYPE' => 'B',
            'LN_SIZE' => 7,
            'BG_COLOR_COL0' => array(
                170,
                240,
                230
            ),
            'BG_COLOR' => array(
                170,
                240,
                230
            ),
            'BRD_COLOR' => array(
                0,
                92,
                177
            ),
            'BRD_SIZE' => 0.2,
            'BRD_TYPE' => '1',
            'BRD_TYPE_NEW_PAGE' => ''
        );
        
        //echo t3lib_utility_Debug::debug($aRows[0]);        
        // Contenu du header du tableau.    
        $contenuHeader = array(
            15,
            25,
            20,
            20,
            40,
            110,
            20,
            $tRows[0],
            $tRows[1],
            $tRows[2],
            $tRows[3],
            $tRows[4],
            $tRows[5],
            $tRows[6]
        );
        
        // DÃ©finition des propriÃ©tÃ©s du reste du contenu du tableau.    
        $proprieteContenu = array(
            'T_COLOR' => array(
                0,
                0,
                0
            ),
            'T_SIZE' => 10,
            'T_FONT' => 'Arial',
            'T_ALIGN_COL0' => 'L',
            'T_ALIGN' => 'R',
            'V_ALIGN' => 'M',
            'T_TYPE' => '',
            'LN_SIZE' => 6,
            'BG_COLOR_COL0' => array(
                245,
                245,
                150
            ),
            'BG_COLOR' => array(
                255,
                255,
                255
            ),
            'BRD_COLOR' => array(
                0,
                92,
                177
            ),
            'BRD_SIZE' => 0.1,
            'BRD_TYPE' => '1',
            'BRD_TYPE_NEW_PAGE' => ''
        );
        
        
        // Contenu du tableau.  
        $contenuTableau = $aRows; 
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($contenuTableau, "$contenuTableau");     
        $PDF->startPageNums();
        
        $PDF->drawTableau($PDF, $proprietesTableau, $proprieteHeader, $contenuHeader, $proprieteContenu, $contenuTableau);
        
        $file = "fileadmin/pdf/genpdf" . "_" . $GLOBALS["TSFE"]->fe_user->user["username"] . ".pdf";
        
        $PDF->Output($file, "F");
        $ord = "?buster=" . time();
        
         switch ($langueCode) {
            case _en:
                $html = "<a href='" . $file . $ord . "' target='_blank'>PDF was generated on ";
                $html = utf8_encode($html);
                $html = $html . $todayE . " : click here</a>";
                break;
            case _de:
                $html = "<a href='" . $file . $ord . "' target='_blank'>PDF ist am "; 
                $html = utf8_encode($html);  
                $html = $html . $todayE . "  generiert worden: klicken Sie hier</a>";
                break;
            case _fr:
                $html = "<a href='" . $file . $ord . "' target='_blank'>PDF a été généré le ";
                 $html = utf8_encode($html);  
                 $html = $html . $todayE . " :  clicker ici</a>";
                break;
            case _it:
                $html = "<a href='" . $file . $ord . "' target='_blank'>PDF was generated on ";
                $html = utf8_encode($html);  
                $html = $html . $todayE . " : click here</a>";
                break;
            }
        
        $this->oForm->aORenderlets["boxMessage"]->setHtml($html);
    } 
}

?>
