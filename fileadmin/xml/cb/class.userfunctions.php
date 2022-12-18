<?php
    class userFunctions {
        
        function btnDeleteTemporaryTheme_click() { 
           // requesting parameters from the event
                $aParams = $this->oForm->oMajixEvent->getParams();
                // keeping the current uid under the hand
                $this->oForm->oSandBox->iRecordUid = $aParams["uid"];
                // get record from DB
                $aRecord = $this->getRecord($aParams["uid"]);
                $GLOBALS["TYPO3_DB"]->exec_DELETEquery( "mytable_temporarythemes", "uid='" . $this->oForm->oSandBox->iRecordUid . "'" );
                // repaints the list
                //return $this->oForm->aORenderlets["themes"]->majixReplaceData(var_dump($aParams));
                
                $GLOBALS["TSFE"]->fe_user->setKey("ses", "manipulatetable", "1");
                $GLOBALS["TSFE"]->fe_user->storeSessionData();
                
                return array($this->oForm->aORenderlets["pages"]->majixRepaint()); 
        }
        
        function btnDelete_click() {
            	// requesting parameters from the event
            	$aParams = $this->oForm->oMajixEvent->getParams();
            	// keeping the current uid under the hand
            	$this->oForm->oSandBox->iRecordUid = $aParams["uid"];
            	// get record from DB
            	$aRecord = $this->getRecord($aParams["uid"]);
            	$GLOBALS["TYPO3_DB"]->exec_DELETEquery( "mytable", "uid='" . $this->oForm->oSandBox->iRecordUid . "'" );
            	// repaints the list
            	//return array($this->oForm->aORenderlets["pages"]->majixRepaint());
                return array($this->oForm->aORenderlets["pages"]->majixSubmitRefresh());    
        }
        
        function btnDeleteBerater_click() {

            	// requesting parameters from the event
            	$aParams = $this->oForm->oMajixEvent->getParams();
            	// keeping the current uid under the hand
            	$this->oForm->oSandBox->iRecordUid = $aParams["uid"];
            	// get record from DB
            	$aRecord = $this->getRecordBerater($aParams["uid"]);
            	$GLOBALS["TYPO3_DB"]->exec_DELETEquery( "mytable_berater", "uid='" . $this->oForm->oSandBox->iRecordUid . "'" );
            	// repaints the list
            	//return array($this->oForm->aORenderlets["pages"]->majixRepaint());
                return array($this->oForm->aORenderlets["pages"]->majixSubmitRefresh());
        }   
        
        function btnDeleteUser_click() {
            	// requesting parameters from the event
            	$aParams = $this->oForm->oMajixEvent->getParams();
            	// keeping the current uid under the hand
            	$this->oForm->oSandBox->iRecordUid = $aParams["uid"];
            	// get record from DB
            	$aRecord = $this->getRecordUser($aParams["uid"]);
            	$GLOBALS["TYPO3_DB"]->exec_DELETEquery( "fe_users", "uid='" . $this->oForm->oSandBox->iRecordUid . "'" );
            	// repaints the list
            	//return array($this->oForm->aORenderlets["pages"]->majixRepaint());
                return array($this->oForm->aORenderlets["pages"]->majixSubmitRefresh());
        } 
            
        /****** get/set methods *******/
        
        function getRecord($iUid) {
            	$rSql = $GLOBALS["TYPO3_DB"]->exec_SELECTquery(
                		"*",
                		"mytable",
                		"uid='" . $iUid . "'"
            	);
            
            	return $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($rSql);
        }
        
        function getRecordBerater($iUid) {
            	$rSql = $GLOBALS["TYPO3_DB"]->exec_SELECTquery(
                		"*",
                		"mytable_berater",
                		"uid='" . $iUid . "'"
            	);
            
            	return $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($rSql);
        }  
        
        function getRecordUser($iUid) {
                $rSql = $GLOBALS["TYPO3_DB"]->exec_SELECTquery(
                        "*",
                        "fe_users",
                        "uid='" . $iUid . "'"
                );
            
                return $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($rSql);
        }  
        
        function btnCancel_click() {
            // close the box
            return $this->oForm->aORenderlets["mymodal"]->majixCloseBox();
        }
        
        function btnSave_click() {  
            // search this patient in our database table
            $updatedArray = $GLOBALS["TSFE"]->fe_user->getKey("ses", "updatedArray");
            $patient = $GLOBALS["TSFE"]->fe_user->getKey("ses", "patientSelected");  
            return  array(
               $this->oForm->aORenderlets["patient"]->majixReplaceData($updatedArray),
               $this->oForm->aORenderlets["patient"]->majixSetValue($patient), 
               $this->oForm->aORenderlets["mymodal"]->majixCloseBox(),
            );
        }
        
        function validateThemesNumber() {                
            require_once ("fileadmin/php/myfunctions.php");
            $lang = languageSelected($GLOBALS['TSFE']->sys_language_uid);
            $row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('COUNT(*) as rowCount', 'mytable_temporarythemes', '1=1');
            $rowCount = $row['rowCount'];
            if($rowCount < 1) {
                switch ($lang) {
                      case _en:
                             $html = "At least one theme must be entered" ;
                             break;
                      case _de:
                             $html = "Es muss mindestens ein Thema eingegeben werden" ;
                             break;
                      case _fr:
                             $html = "Au moins un thème doit être donné" ;
                             break;
                      case _it:
                             $html = "Deve essere inserito almeno un tema" ;
                             break;
                             
               }                 
               return "<span class='error'>" .  utf8_encode($html) . "</span>";
            } else {
               return true;
            }
        }    
        
function validateDate() {                
            require_once ("fileadmin/php/myfunctions.php");
            $lang = languageSelected($GLOBALS['TSFE']->sys_language_uid);
            $actDate = $this->oForm->aORenderlets["beratungsdatum"]->getValue();
            $actDateString = $this->oForm->aORenderlets["beratungsdatum"]->getRawPostValue();
           // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->oForm->aORenderlets["beratungsdatum"]->getRawPostValue(), "actDate"); 

            list($d, $m, $y) = array_pad(explode('-', $actDateString, 3), 3, 0);
            
           // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump( "$d-$m-$y", "date"); 
           // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump( ctype_digit("$d$m$y"), "date"); 
           // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump("d=$d", "date"); 
           // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump( empty("$d"), "date"); 
            
            $timestamp =  strtotime("1 January 2010");
            
            if(empty($d) || empty($m) || empty($y) || !ctype_digit("$d$m$y")) {
                switch ($lang) {
                      case _en:
                             $html = "wrong date format "  . " (format jj-mm-yyyy)";
                             break;
                      case _de:
                             $html = "falsches format " .  " (format jj-mm-yyyy)";
                             break;
                      case _fr:
                             $html = "mauvais format " .  " (format jj-mm-yyyy)";
                             break;
                      case _it:
                             $html = "formata errata " .  " (format jj-mm-yyyy)";
                             break;  
               }   
               return "<span class='error'>" .  utf8_encode($html) . "</span>";             
            }
            
            if(!checkdate($m, $d, $y)) {
                switch ($lang) {
                      case _en:
                             $html = "wrong date "  . " (format jj-mm-yyyy)";
                             break;
                      case _de:
                             $html = "falsches Datum " .  " (format jj-mm-yyyy)";
                             break;
                      case _fr:
                             $html = "mauvaise date " .  " (format jj-mm-yyyy)";
                             break;
                      case _it:
                             $html = "data errata " .  " (format jj-mm-yyyy)";
                             break;  
               }   
               return "<span class='error'>" .  utf8_encode($html) . "</span>";             
            }
            
            if($actDate <  $timestamp) {
                switch ($lang) {
                      case _en:
                             $html = "wrong date " . date('d-m-Y', $actDate) . " (format jj-mm-yyyy)";
                             break;
                      case _de:
                             $html = "falsches Datum " . date('d-m-Y', $actDate). " (format jj-mm-yyyy)";
                             break;
                      case _fr:
                             $html = "mauvaise date " . date('d-m-Y', $actDate). " (format jj-mm-yyyy)";
                             break;
                      case _it:
                             $html = "data errata " . date('d-m-Y', $actDate). " (format jj-mm-yyyy)";
                             break;
                             
               }                  
               return "<span class='error'>" .  utf8_encode($html) . "</span>";
        } else {
            return true;
        }       
                     
    }
    }
?>