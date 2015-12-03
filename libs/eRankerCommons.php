<?PHP

//Avoid re-include this file
if (class_exists("eRankerCommons")) {
    return;
}

class eRankerCommons {

    const NAME = "eRankerCommons";
    const MISSING = "MISSING";
    const NEUTRAL = "NEUTRAL";
    const GREEN = "GREEN";
    const ORANGE = "ORANGE";
    const RED = "RED";
    const BIG = "BIG";
    const BASE_ER = "fad6kh9uo3xltjn48erw5qc20gm7szbi1vpy";
    const BASE_10 = "0123456789";

    public static $imgfolder = "/content/themes/eranker/img/";
    public static $factorCreateImageFolder = "/content/themes/eranker/libs/";
    public static $folderLibs = "/content/themes/eranker/libs/";
    public static $urlLeadGenerator = "/content/themes/eranker/libs/leadgenerator.php";
    public static $useleadgenerator = FALSE;
    public static $layoutLeadGenerator = "POPUP";
    public static $howshowthemodal = "report20";
    public static $agent = array('image' => '/content/themes/eranker/img/lead-generator-pop-up-user-default-man-bg.png', 'text' => 'Fill in the data to get the full report', 'name' => 'eRanker Support', 'position' => '', 'logo' => '/content/themes/eranker/img/logo-blue.png', 'referer' => '', 'text_button' => 'Unlock Report Data');

    /**
     * Decode a report id from eranker base
     * @param string $id The report id
     */
    public static function decodeReportId($id) {
        return self::convBase($id, self::BASE_10, self::BASE_10); //disabled for now...
    }

    /**
     * translate the text on eranker
     * @param string $key The text
     */
    public static function translate($key, $factor = null, $default = null) {
        $arrayF = (array) $factor;

        if (empty($arrayF) || !isset($arrayF['text']) || empty($arrayF['text'])) {
            return ($default !== null) ? $default : $key;
        } else {
            $arrayT = (array) $arrayF['text'];

            if (isset($arrayT[$key])) {
                return $arrayT[$key];
            }
        }
        return ($default !== null) ? $default : $key;
    }

    /**
     * Decode a report id from eranker base
     * @param string $id The report id
     */
    public static function encodeReportId($id) {
        return self::convBase($id, self::BASE_10, self::BASE_10);
    }

    /**
     * Based on a string. we add the data using the model inside the string. 
     * Normally we use sprintf but if the data is an array or object, we replace using %keyname
     * @param Any $data The data from the factor
     * @param String $string The base string
     * @return The new string with the value data values in it (if needed)
     */
    public static function replaceValue($data, $string) {
        if (empty($string)) {
            return "";
        }
        if (is_array($data) || is_object($data)) {
            $data = (array) $data;
            $out = $string;
            foreach ($data as $key => $value) {
                if (is_string($value) || is_int($value) || is_float($value) || is_numeric($value)) {
                    $out = str_replace("%" . $key, $value, $out);
                } else {
                    $out = str_replace("%" . $key, is_object($value) ? "[OBJECT]" : "[ARRAY]", $out);
                }
            }
            return $out;
        } else {
            try {
                return @sprintf($string, $data);
            } catch (Exception $ex) {
                return "XXX";
            }
        }
    }

    /**
     * Based on a status, get the rigth model array (status, model and description) from a factor
     * @param Any $data The data Object
     * @param String $status The factor Status Text. Ex: RED, MISSING, ORANGE, etc
     * @param Object $fullFactor The full factor Object. Must contain the texts
     * @return Array The array with the right text models for the status.
     */
    public static function getFactorStatusText($data, $status, $fullFactor) {
        $out = array();
        switch ($status) {
            case self::RED:
                $out['model'] = self::replaceValue($data, self::translate("model_red", $fullFactor));
                $out['description'] = self::replaceValue($data, self::translate("description_red", $fullFactor));
                break;
            case self::ORANGE:
                $out['model'] = self::replaceValue($data, self::translate("model_orange", $fullFactor));
                $out['description'] = self::replaceValue($data, self::translate("description_orange", $fullFactor));
                break;
            case self::GREEN:
                $out['model'] = self::replaceValue($data, self::translate("model_green", $fullFactor));
                $out['description'] = self::replaceValue($data, self::translate("description_green", $fullFactor));
                break;
            case self::NEUTRAL:
                $out['model'] = self::replaceValue($data, self::translate("model_neutral", $fullFactor));
                $out['description'] = self::replaceValue($data, self::translate("description_neutral", $fullFactor));
                break;
            case self::MISSING;
            default;
                $out['model'] = self::replaceValue($data, self::translate("model_missing", $fullFactor));
                $out['description'] = self::replaceValue($data, self::translate("description_missing", $fullFactor));
        }


        return $out;
    }

    /**
     * Based on the factor value, return the rigth factor status based on the limits and the function
     * @param Any $value The value of the factor.
     * @param Object $fullFactor The full factor Object. Must contain the texts
     * @return String The factor status
     */
    public static function getFactorStatus($value, $fullFactor) {
        if ($value === NULL) {
            return self::MISSING;
        }
        switch ($fullFactor->function) {
            case ">":
                if ($value > $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if ($value > $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case ">=":
                if ($value >= $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if ($value >= $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "<":
                if ($value < $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if ($value < $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "<=":
                if ($value <= $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if ($value <= $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "<>":
                if ($value != $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if ($value != $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "==":
                if ($value == $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if ($value == $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "strlen()>=":
                if (strlen($value) >= $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (strlen($value) >= $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "strlen()>":
                if (strlen($value) > $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (strlen($value) > $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "strlen()<=":
                if (strlen($value) <= $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (strlen($value) <= $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "strlen()<":
                if (strlen($value) < $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (strlen($value) < $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "strlen()==":
                if (strlen($value) == $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (strlen($value) == $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "strlen()<>":
                if (strlen($value) != $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (strlen($value) != $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "count()>=":
                if (count($value) >= $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (count($value) >= $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "count()>":
                if (count($value) > $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (count($value) > $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "count()<=":
                if (count($value) <= $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (count($value) <= $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "count()<":
                if (count($value) < $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (count($value) < $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "count()==":
                if (count($value) == $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (count($value) == $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "count()<>":
                if (count($value) != $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (count($value) != $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "red":
            case "RED":
                return self::RED;
            case "orange":
            case "ORANGE":
                return self::ORANGE;
            case "green":
            case "GREEN":
                return self::GREEN;
            case "missing":
            case "MISSING":
                return self::MISSING;
            case "neutral":
            case "NEUTRAL":
            default:
                return self::NEUTRAL;
        }
        return self::NEUTRAL;
    }

    /**
     * Get the scores array for the report
     * @param Object $report The report object
     * @param Object $reportData The report data
     * @param Array $reportFactors The list of factors
     * @param boolean $debug The debug flag.
     * @return Array The scores generated
     */
    public static function getScores($report, $reportData, $reportFactors, $thumb = NULL, $debug = false) {
        $out = array();

        $maxScore = 0;
        $currentScore = 0;
        $totalRed = 0;
        $totalGreen = 0;
        $totalOrange = 0;
        $totalMissing = 0;
        $totalNeutral = 0;


        $out["score"] = array(); //Init the score. **See code after the foeach

        $out["url"] = isset($report->url) ? $report->url : NULL;

        if (!empty($reportFactors)) {
            foreach ($reportFactors as $factor) {

                $valueToUse = isset($reportData[$factor->id]) ? $reportData[$factor->id] : NULL;
                if (!empty($factor->path)) {
                    $pathArr = explode("->", trim($factor->path));
                    if (!empty($pathArr)) {
                        foreach ($pathArr as $currentPath) {
                            $valueToUse = (array) $valueToUse;
                            $valueToUse = isset($valueToUse[$currentPath]) ? $valueToUse[$currentPath] : NULL;
                        }
                    }
                }
                $statusCode = self::getFactorStatus($valueToUse, $factor);
                $status = self::getFactorStatusText(isset($reportData[$factor->id]) ? $reportData[$factor->id] : NULL, $statusCode, $factor);

                $out[$factor->id] = array();
                $out[$factor->id]['data'] = isset($reportData[$factor->id]) ? $reportData[$factor->id] : NULL;
                $out[$factor->id]['model'] = array();
                //$out[$factor->id]['model']['name'] = isset($factor->id) ? $factor->id : NULL;
                $out[$factor->id]['model']['friendly_name'] = isset($factor->text["friendly_name"]) ? $factor->text["friendly_name"] : NULL;
                $out[$factor->id]['model']['type'] = isset($factor->type) ? $factor->type : NULL;
                $out[$factor->id]['model']['status'] = $statusCode;
                $out[$factor->id]['model']['model'] = $status['model'];
                $out[$factor->id]['model']['description'] = $status['description'];
                $out[$factor->id]['model']['path'] = isset($factor->path) ? $factor->path : NULL;
                //$out[$factor->id]['model']['pro_only'] = isset($factor->pro_only) && !empty($factor->pro_only) ? TRUE : FALSE;
                //$out[$factor->id]['model']['free'] = isset($factor->free) && !empty($factor->free) ? TRUE : FALSE;
                $out[$factor->id]['model']['order'] = !isset($factor->order) ? 0 : $factor->order;
                $out[$factor->id]['model']['correlation'] = !isset($factor->correlation) ? null : $factor->correlation;
                $out[$factor->id]['model']['difficulty_level'] = !isset($factor->difficulty_level) ? null : $factor->difficulty_level;
                $out[$factor->id]['model']['article'] = isset($factor->article) ? $factor->article : NULL;
                $out[$factor->id]['model']['solution'] = isset($factor->solution) ? $factor->solution : NULL;
                //Add the factor to the score system
                if ($statusCode === self::RED) {
                    $maxScore += $factor->correlation; //Receive score 0 for this factor
                    $totalRed++;
                }
                if ($statusCode === self::MISSING) {
                    $maxScore += $factor->correlation; //Receive score 0 for this factor
                    $totalMissing++;
                }
                if ($statusCode === self::ORANGE) {
                    $maxScore += $factor->correlation;
                    $currentScore += $factor->correlation * 0.5; //Receive 50% of total score for this factor
                    $totalOrange++;
                }
                if ($statusCode === self::GREEN) {
                    $maxScore += $factor->correlation;
                    $currentScore += $factor->correlation; //Receive 100% of total score for this factor
                    $totalGreen++;
                }
                if ($statusCode === self::NEUTRAL) {
                    $totalNeutral++; //Neutral are ignored on the score
                }

                $out[$factor->id]['model']['category'] = array();
                $out[$factor->id]['model']['category']['order'] = isset($factor->category_order) ? $factor->category_order : NULL;
                $out[$factor->id]['model']['category']['friendly_name'] = isset($factor->category_friendly_name) ? $factor->category_friendly_name : NULL;
                $out[$factor->id]['model']['category']['description'] = isset($factor->category_description) ? $factor->category_description : NULL;
                $out[$factor->id]['model']['category']['bg_color'] = isset($factor->category_bg_color) ? strtoupper($factor->category_bg_color) : NULL;
                $out[$factor->id]['model']['category']['hover_color'] = isset($factor->category_hover_color) ? strtoupper($factor->category_hover_color) : NULL;
                $out[$factor->id]['model']['category']['group'] = array();
                $out[$factor->id]['model']['category']['group']['friendly_name'] = isset($factor->group_friendly_name) ? $factor->group_friendly_name : NULL;
                $out[$factor->id]['model']['category']['group']['description'] = isset($factor->group_description) ? $factor->group_description : NULL;
                $out[$factor->id]['model']['category']['group']['order'] = isset($factor->group_order) ? $factor->group_order : NULL;

                if ($debug) {
                    $out[$factor->id]['model']['debug'] = array(
                        'limit_red' => $factor->limit_red,
                        'limit_orange' => $factor->limit_orange,
                        'limit_green' => $factor->limit_green,
                        'limit_neutral' => $factor->limit_neutral,
                        'function' => $factor->function
                    );
                }
            }
        }
        //Merge the scode data
        $out["score"]["percentage"] = (double) number_format(($currentScore / max(1, $maxScore)) * 100, 1);
        $out["score"]["raw"] = (double) number_format($currentScore, 1);
        $out["score"]["raw_total"] = (double) number_format(max(1, $maxScore), 1);
        $out["score"]["factors"] = (object) array("red" => $totalRed, "orange" => $totalOrange, "green" => $totalGreen, "missing" => $totalMissing, "neutral" => $totalNeutral);
        $out["score"]["thumbnail"] = $thumb;

        return (object) $out;
    }

    /**
     * Convert a database full factor to a object in the API format
     * @param Object $factor The full factor object that came from database
     * @return Object the factor formated to be shown on the API
     */
    public static function getFactorExternalObj($factor) {

        if (empty($factor)) {
            return array();
        }
        $tmpitem = array();
        if ($factor->is_active) {
            $tmpitem['id'] = $factor->id;
            $tmpitem['order'] = $factor->order;
            $tmpitem['type'] = $factor->type;
            $tmpitem['gui_type'] = $factor->gui_type;
            $tmpitem['limit_red'] = $factor->limit_red;
            $tmpitem['limit_orange'] = $factor->limit_orange;
            $tmpitem['limit_green'] = $factor->limit_green;
            $tmpitem['limit_neutral'] = $factor->limit_neutral;

            //Compatibility only
            $tmpitem['friendly_name'] = self::translate("friendly_name", $factor, "");
            $tmpitem['model_red'] = self::translate("model_red", $factor, "");
            $tmpitem['model_orange'] = self::translate("model_orange", $factor, "");
            $tmpitem['model_green'] = self::translate("model_green", $factor, "");
            $tmpitem['model_neutral'] = self::translate("model_neutral", $factor, "");
            $tmpitem['model_missing'] = self::translate("model_missing", $factor, "");
            $tmpitem['description_red'] = self::translate("description_red", $factor, "");
            $tmpitem['description_orange'] = self::translate("description_orange", $factor, "");
            $tmpitem['description_green'] = self::translate("description_green", $factor, "");
            $tmpitem['description_neutral'] = self::translate("description_neutral", $factor, "");
            $tmpitem['description_missing'] = self::translate("description_missing", $factor, "");
            $tmpitem['article'] = self::translate("article", $factor, "");
            $tmpitem['solution'] = self::translate("solution", $factor, "");
            //End of compatibility functions

            $tmpitem['correlation'] = $factor->correlation;
            $tmpitem['path'] = $factor->path;
            $tmpitem['pro_only'] = isset($factor->pro_only) && !empty($factor->pro_only) ? TRUE : FALSE;
            $tmpitem['free'] = isset($factor->free) && !empty($factor->free) ? TRUE : FALSE;

            $tmpitem['difficulty_level'] = $factor->difficulty_level;
            $tmpitem['category_id'] = $factor->category_id;
            $tmpitem['category_order'] = $factor->category_order;
            $tmpitem['category_icon'] = $factor->category_icon;
            $tmpitem['category_friendly_name'] = isset($factor->category_friendly_name) ? $factor->category_friendly_name : NULL;
            $tmpitem['category_description'] = isset($factor->category_description) ? $factor->category_description : NULL;
            $tmpitem['category_bg_color'] = isset($factor->category_bg_color) ? strtoupper($factor->category_bg_color) : NULL;
            $tmpitem['category_hover_color'] = isset($factor->category_hover_color) ? strtoupper($factor->category_hover_color) : NULL;
            $tmpitem['group_id'] = isset($factor->group_id) ? $factor->group_id : NULL;
            $tmpitem['group_friendly_name'] = isset($factor->group_friendly_name) ? $factor->group_friendly_name : NULL;
            $tmpitem['group_description'] = isset($factor->group_description) ? $factor->group_description : NULL;
            $tmpitem['group_order'] = $factor->group_order;
            $tmpitem['text'] = $factor->text;
        }
        return $tmpitem;
    }

    /**
     * Filter the factor name to make sure it does not have suspicius characters
     * @param String $s The factor name to be filtred
     * @return String the filtred factor name (with only letters, numbers and underline
     */
    public static function sanitizeFactorName($s) {
        return preg_replace("/[^a-zA-Z0-9-_]+/", "", $s);
    }

    /**
     * Convert a type from the factor table to the sql type that will be used to create the factor table
     * @param String $type Type from the factors table. Values: 'BOOLEAN', 'INTEGER', 'FLOAT', 'JSON', 'TEXT', 'BIGINT', 'DATETIME', 'STRING'
     * @return String The SQL type string. By default it returns a varchar.
     */
    public static function getFactorSQLType($type) {
        switch (trim(strtoupper((string) $type))) {
            case 'BOOLEAN':
                return "BOOLEAN";
            case 'INTEGER':
                return "INT";
            case 'FLOAT':
                return "FLOAT";
            case 'JSON':
                return "TEXT";
            case 'BIGINT':
                return "BIGINT";
            case 'DATETIME':
                return "DATETIME";
            case 'TEXT':
                return "TEXT";
            case 'STRING':
            default:
                return "VARCHAR(255) COLLATE utf8_bin";
        }
    }

    /**
     * Convert a factor value to a given Type
     * @param Any $value The factor value
     * @param String $type The type of the variable
     * @return Any The variable casted to the right type
     */
    public static function convertFactor($value, $type) {
        if (is_null($value)) {
            return NULL;
        }
        switch ($type) {
            case 'INTEGER':
                return (int) $value;
            case 'STRING':
                return (string) $value;
            case 'BOOLEAN':
                return (boolean) $value;
            case 'FLOAT':
                return (float) $value;
            case 'JSON':
                return json_decode($value);
            default:
                return $value;
        }
    }

    /**
     * FIx a URL by adding the protocol and lowercase the hostname and replace the spaces
     * @param String $url The original url to be fixed
     * @return boolean|string The fixed URL. False if the url is invalid
     */
    public static function fixURL($url) {

        if (strpos($url, "//") === 0) {
            $url = "http:" . $url;
        } else {
            if (strpos(strtolower($url), "http") !== 0) {
                $url = "http://" . $url;
            }
        }
        $parsed = parse_url($url);

        if (!isset($parsed['scheme']) || empty($parsed['scheme'])) {
            return false;
        }
        if (!isset($parsed['host']) || empty($parsed['host'])) {
            return false;
        }

        $url = strtolower($parsed['scheme']) . "://" . strtolower($parsed['host'])
                . ( (isset($parsed['path']) && !empty($parsed['path'])) ? $parsed['path'] : "/" )
                . ( (isset($parsed['query']) && !empty($parsed['query'])) ? "?" . $parsed['query'] : "" );

        if (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED)) {
            return str_replace(' ', '%20', $url);
        } else {
            return false;
        }
    }

    /**
     * Prepare a factor array, remove duplicates, sort,  strtolower and trim for each item
     * @param array $arr The input array
     * @return array The filtred array
     */
    public static function prepareFactorArray($arr) {
        $out = array();
        if (empty($arr)) {
            return $out;
        }
        foreach ($arr as $value) {
            $newvalue = strtolower(trim($value));
            if (!empty($newvalue)) {
                $out[] = trim($newvalue);
            }
        }
        sort($out);
        return array_unique($out);
    }

    /**
     * Get a ORDERED TREE of full factors
     * This function 
     * The tree look likes this:
     *  [category_id] 
     *      [group_id1] 
     *              factor1
     *              factor2
     *              factor3
     *      [group_id2]
     *              factor4
     *              factor5
     *              factor6
     * @param array $fullfactors The full factors from the database
     * @return array The Tree as an array
     */
    public static function getFactorTree($fullfactors) {

        //Create an order for each thing
        $categories_order = array();
        $groups_order = array();
        $factors_order = array();

        //Create the unordered tree
        $unorderedTree = array();

        if (!empty($fullfactors)) {
            foreach ($fullfactors as $factor) {
                $factor = (object) $factor;
                if (!isset($categories_order[$factor->category_id])) {
                    $categories_order[$factor->category_id] = $factor->category_order;
                }

                if (!isset($groups_order[$factor->group_id])) {
                    $groups_order[$factor->group_id] = $factor->group_order;
                }

                if (!isset($factors_order[$factor->id])) {
                    $factors_order[$factor->id] = $factor->order;
                }

                if (!isset($unorderedTree[$factor->category_id])) {
                    $unorderedTree[$factor->category_id] = array();
                }

                if (!isset($unorderedTree[$factor->category_id][$factor->group_id])) {
                    $unorderedTree[$factor->category_id][$factor->group_id] = array();
                }
                $unorderedTree[$factor->category_id][$factor->group_id][] = $factor->id;
            }
        }
        //Sort the individual items
        asort($categories_order);
        asort($groups_order);
        asort($factors_order);


        $orderedTree = array();

        //Create the ordered tree by navigate each item in order and comparing with the unordered tree.
        if (!empty($categories_order)) {
            foreach ($categories_order as $category_id => $category_order) {
                $orderedTree[$category_id] = array();
                if (!empty($groups_order)) {
                    foreach ($groups_order as $group_id => $group_order) {
                        if (in_array($group_id, array_keys($unorderedTree[$category_id]))) {
                            $orderedTree[$category_id][$group_id] = array();
                            if (!empty($factors_order)) {
                                foreach ($factors_order as $factor_id => $factor_order) {
                                    if (in_array($factor_id, $unorderedTree[$category_id][$group_id])) {
                                        $orderedTree[$category_id][$group_id][] = $factor_id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        //Debug!
        //echo "<pre>" . print_r($unorderedTree, true) . "</pre>";
        //echo "<pre>" . print_r($orderedTree, true) . "</pre>";
        return $orderedTree;
    }

    /**
     * Convert a object to an array using recurssion
     * @param obejct $obj The object to be converted
     * @return array The array
     */
    public static function objectToArray($obj) {
        if (is_object($obj)) {
            $obj = (array) $obj;
        }
        if (is_array($obj)) {
            $new = array();
            foreach ($obj as $key => $val) {
                $new[$key] = self::objectToArray($val);
            }
        } else {
            $new = $obj;
        }
        return $new;
    }

    /**
     * Convert an arbitrarily large number from any base to any base.
     * examples for $fromBaseInput and $toBaseInput
     * '0123456789ABCDEF' for Hexadecimal (Base16)
     * '0123456789' for Decimal (Base10)
     * '01234567' for Octal (Base8)
     * '01' for Binary (Base2) 
     * You can really put in whatever you want and the first character is the 0.
     * @param string $numberInput number to convert as a string
     * @param string $fromBaseInput base of the number to convert as a string
     * @param string $toBaseInput base the number should be converted to as a string
     * @return string The output on the new base
     */
    public static function convBase($numberInput, $fromBaseInput, $toBaseInput) {
        if ($fromBaseInput == $toBaseInput) {
            return $numberInput;
        }
        $fromBase = str_split($fromBaseInput, 1);
        $toBase = str_split($toBaseInput, 1);
        $number = str_split($numberInput, 1);
        $fromLen = strlen($fromBaseInput);
        $toLen = strlen($toBaseInput);
        $numberLen = strlen($numberInput);
        $retval = '';
        if ($toBaseInput == '0123456789') {
            $retval = 0;
            for ($i = 1; $i <= $numberLen; $i++) {
                $retval = bcadd($retval, bcmul(array_search($number[$i - 1], $fromBase), bcpow($fromLen, $numberLen - $i)));
            }
            return $retval;
        }
        if ($fromBaseInput != '0123456789') {
            $base10 = self::convBase($numberInput, $fromBaseInput, '0123456789');
        } else {
            $base10 = $numberInput;
        }
        if ($base10 < strlen($toBaseInput)) {
            return $toBase[$base10];
        }
        while ($base10 != '0') {
            $retval = $toBase[bcmod($base10, $toLen)] . $retval;
            $base10 = bcdiv($base10, $toLen, 0);
        }
        return $retval;
    }

    /**
     * Generate the report HTML code
     * @param object $report the report row. Factors cols shall be already converted to array
     * @param object $reportScores The array with the report data and scores
     * @param array $fullfactors The full factors from the database
     * @param boolean $logged_in Tell if the user is logged in or not
     * @param boolean $is_pdf Tell if we are generating a pdf or not
     * @return string The html of the report. 
     */
    public static function getReportHTML($report, $reportScores, $fullfactors, $logged_in = false, $is_pdf = false, $disable_pdf = false, $show_header = TRUE, $show_title = TRUE, $show_category = TRUE) {
        //Make sure that the factors is on the array format
        $fullfactors = self::objectToArray($fullfactors);

        //Make sure that the scores is on the array format
        $reportScores = self::objectToArray($reportScores);

        $out = "<div class='superreport-seo'>";
        $out .= "<div id='erreport'>";
        $out .= self::getReportScoreHTML($report, $reportScores['score'], self::BIG, $disable_pdf, $show_header, $show_title, $show_category);

        $categories = array();
        $groups = array();

        //Remove the factors that are not used on the report:
        foreach ($fullfactors as $factor_id => $factor) {
            if (!in_array($factor_id, $report->factors)) {
                unset($fullfactors[$factor_id]);
            }
            $categories[$factor['category_id']] = array(
                "friendly_name" => $factor['category_friendly_name'],
                "description" => $factor['category_description'],
                "order" => $factor['category_order'],
                "hover_color" => $factor['category_hover_color'],
                "bg_color" => $factor['category_bg_color'],
                "icon" => $factor['category_icon']
            );

            $groups[$factor['group_id']] = array(
                "friendly_name" => $factor['group_friendly_name'],
                "description" => $factor['group_description'],
                "order" => $factor['group_order']
            );
        }
        $factorTree = self::getFactorTree($fullfactors);

        //Navigate down the factor tree to this report
        if (!empty($factorTree)) {
            foreach ($factorTree as $category_id => $category_array) {
                if (!empty($category_array)) {

                    $out .= "\r\n";
                    $out .= '<div class="ercategory" data-category_id="' . $category_id . '" >';
                    $out .= '<div class="ercategoryheadline">';
                    if ($show_category) {
                        $out .= '<h2 onclick="jQuery(\'.ercategorydescription[data-category_id=' . $category_id . ']\').slideToggle();" class="ercategoryname" style="border-color: #' . $categories[$category_id]['bg_color'] . '">';
                        $out .= '<img src="' . $categories[$category_id]['icon'] . '" class="ercategoryicon" alt="{icon}" /> ';
                        $out .= $categories[$category_id]['friendly_name'];
                        $out .= '</h2>';
                    }


                    $out .= '<div class="ercategoryprogressbar"></div>';
                    $out .= '</div><!-- .ercategoryheadline -->';
                    $out .= '<div class="ercategorydescription" data-category_id="' . $category_id . '" style="display:block">' . $categories[$category_id]['description'] . '</div>';

                    $is_odd_row = true;
                    foreach ($category_array as $group_id => $group_array) {
                        if (!empty($group_array)) {
                            $out .= "\r\n";
                            $out .= '<div class="ergroup row ' . ($show_title ? 'append-title-margin' : '') . '" data-group_id="' . $group_id . '" >';
                            $title = null;
                            if ($show_title) {
                                $title = '<h3 class="ergroupname ' . (($is_odd_row) ? 'eroddrow' : '') . '">' . $groups[$group_id]['friendly_name'] . '</h3>';
                            }
                            $is_even = false;
                            foreach ($group_array as $factor_id) {
                                $out .= self::getFactorHTML($report, $fullfactors[$factor_id], $reportScores[$factor_id], $is_even, $logged_in, $show_header, $show_title, $show_category, $title);
                                $is_even = !$is_even;
                                $title = null;
                            }
                            $out .= "</div><!-- .ergroup -->\r\n";
                        }
                    }

                    $out .= "</div><!-- .ercategory -->\r\n";
                }
            }
        }

        //DEBUG!
        //$out .= "<pre>ORDERED FACTOR TREE: " . print_r($factorTree, true) . "</pre>";
        if ((!isset($_COOKIE['leadgenerated']) || empty($_COOKIE['leadgenerated'])) && self::$useleadgenerator !== FALSE && strcasecmp(self::$layoutLeadGenerator, 'POPUP') !== 0) {


            $out .= '<div class="row" id="leadGeneratorFooter">';
            $out .= '<div class="row" id="msgleadgenerator">';
            $out .= '</div>';
            $out .= '<div class="row">';
            $out .= '<div class="col-md-3" style="margin-top: 40px;">';
            $out .= '<div class="toprrightimgemptymodalFooter">';
            $out .= '<div class="toprrightimgmodalFooter"></div>';
            $out .= '</div>';
            if (!empty(self::$agent['logo']) || !empty(self::$agent['name']) || !empty(self::$agent['position'])) {
                $out .= '<div style="">';
                $out .= '<div style="text-align: center; font-size: 16px;">' . self::$agent['name'] . ' <br />' . self::$agent['position'] . '<br /><img src="' . self::$agent['logo'] . '" style="max-height: 50px; max-width: 175px;" /> </div>';
                $out .= '</div>';
            }
            $out .= '</div>';
            $out .= '<div class="col-md-9">';
            $out .= '<form id="formLeadGenerator" method="post" action="' . self::$folderLibs . 'leadgenerator.php">';
            $out .= '<div class="">';
            if (!empty(self::$agent['text'])) {
                $out .= ' <h5>' . self::$agent['text'] . ' </h5>';
            }
            $out .= '<div class="form-group">';
            $out .= '<label for="name_leadgenerator">Full Name</label>';
            $out .= '<input id="name_leadgenerator" type="text" class="form-control" name="name_leadgenerator" placeholder="Full Name">';
            $out .= '</div>';
            $out .= '<div class="form-group">';
            $out .= '<label for="companyname_leadgenerator">Company Name</label>';
            $out .= '<input id="companyname_leadgenerator" type="text" class="form-control" name="companyname_leadgenerator" placeholder="Company Name">';
            $out .= '</div>';
            $out .= '<div class="form-group">';
            $out .= '<label for="email_leadgenerator">Email</label>';
            $out .= '<input id="email_leadgenerator" type="text" class="form-control" name="email_leadgenerator" placeholder="E-Mail Address">';
            $out .= '</div>';
            $out .= '<div class="form-group">';
            $out .= '<label for="phone_leadgenerator">Phone</label>';
            $out .= '<input id="phone_leadgenerator" type="text" class="form-control" name="phone_leadgenerator" placeholder="Phone Number">';
            $out .= '<input type="hidden" name="reporturl" value="' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '">';
            $out .= '</div>';
            $out .= ' <button id="saveleadgenerator" type="submit" class="btn btn-primary" style="width: 100%;">' . self::$agent['text_button'] . '</button>';
            $out .= ' </div>';

            $out .= '</form>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';

            $out .= '<style>';
            if (!empty(self::$agent['image'])) {
                $out .= '#leadGeneratorFooter .toprrightimgmodalFooter { background: transparent url("' . self::$agent['image'] . '") center no-repeat; height: 160px; border-radius: 150px; width: 160px;}';
                $out .= '#leadGeneratorFooter .toprrightimgemptymodalFooter { background: transparent url("' . self::$imgfolder . 'lead-generator-pop-up-user-bg.png") center no-repeat; height: 160px; margin-right: auto; width: 160px; margin-left: auto;}';
            } else {
                $out .= '#leadGeneratorFooter .toprrightimgmodalFooter { background: transparent url("' . self::$imgfolder . 'lead-generator-pop-up-user-default-man-bg.png") center no-repeat; height: 160px; border-radius: 150px; width: 160px;}';
            }

            $out .= '</style>';
            $out .= '<script>jQuery(document).ready(function () {urlLeadGenerate = "' . self::$urlLeadGenerator . '";});</script>';
        }

        $out .= "</div><!-- #erreport -->";
        $out .= "</div><!-- .superreport-seo -->";
        if ((!isset($_COOKIE['leadgenerated']) || empty($_COOKIE['leadgenerated'])) && self::$useleadgenerator !== FALSE && strcasecmp(self::$layoutLeadGenerator, 'POPUP') === 0) {
            $out .= '<div id="howshowthemodal" data-howshowthemodal="' . self::$howshowthemodal . '"></div>';
            $out .= '<div class="modal fade" id="leadGenerator" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">';
            $out .= '<div class="modal-dialog" role="document" style="padding: 0px !important; width: 512px !important; top:190px;  !important;">';
            $out .= '<form id="formLeadGenerator" method="post" action="' . self::$folderLibs . 'leadgenerator.php">';
            $out .= '<div class="modal-content">';
            $out .= '<div class="modal-body" style="height: 250px; padding-right: 0px;">';
            $out .= '<div class="form-left-positon" style="width: 280px;padding: -20px;float: left;">';
            if (!empty(self::$agent['text'])) {
                $out .= ' <h5>' . self::$agent['text'] . ' </h5>';
            }
            $out .= '<div id="msgleadgenerator">';
            $out .= '</div>';
            $out .= '<div class="form-group">';
//            $out .= '<label for="name_leadgenerator">Full Name</label>';
            $out .= '<input id="name_leadgenerator" type="text" class="form-control" name="name_leadgenerator" placeholder="Full Name">';
            $out .= '</div>';
//            $out .= '<div class="form-group">';
////            $out .= '<label for="companyname_leadgenerator">Company Name</label>';
//            $out .= '<input id="companyname_leadgenerator" type="text" class="form-control" name="companyname_leadgenerator" placeholder="Company Name">';
//            $out .= '</div>';
            $out .= '<div class="form-group">';
//            $out .= '<label for="email_leadgenerator">Email</label>';
            $out .= '<input id="email_leadgenerator" type="text" class="form-control" name="email_leadgenerator" placeholder="E-Mail Address">';
            $out .= '</div>';
            $out .= '<div class="form-group">';
//            $out .= '<label for="phone_leadgenerator">Phone</label>';
            $out .= '<input id="phone_leadgenerator" type="text" class="form-control" name="phone_leadgenerator" placeholder="Phone Number">';
            $out .= '<input type="hidden" name="reporturl" value="' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '">';
            $out .= '</div>';
            $out .= ' <button id="saveleadgenerator" type="submit" class="btn btn-primary" style="width: 100%;"> ' . self::$agent['text_button'] . '</button>';
            $out .= ' </div>';
            if (!empty(self::$agent['logo']) || !empty(self::$agent['name']) || !empty(self::$agent['position'])) {
                $out .= '<div style="float: right;height: 200px;width: 205px;">';
                $out .= '<div style="float: left;height: 100px;width: 205px;"></div>';
                $out .= '<div style="float: left;height: 69px;width: 205px; text-align: center; font-size: 16px;">' . self::$agent['name'] . ' <br />' . self::$agent['position'] . '<br /><img src="' . self::$agent['logo'] . '" style="max-height: 50px; max-width: 175px;" /> </div>';
                $out .= '</div>';
            }

            $out .= '<div class="toprrightimgemptymodal"></div>';
            $out .= '<div class="toprrightimgmodal"></div>';


            $out .= '</div>';
//            $out .= '<div class="modal-footer">';
//            
//            $out .= ' </div>';
            $out .= ' </div>';
            $out .= '</form>';
            $out .= ' </div>';
            $out .= '</div>';
            $out .= '<style>';
            $out .= '#leadGenerator .modal-content { background: transparent url("' . self::$imgfolder . 'lead-generator-pop-up-main-bg.png") center no-repeat;}';
            if (!empty(self::$agent['image'])) {
                $out .= '#leadGenerator .toprrightimgmodal { background: transparent url("' . self::$agent['image'] . '") center no-repeat; position: absolute; top: -62px; right: 24px; height: 160px; border-radius: 150px; width: 160px}';
                $out .= '#leadGenerator .toprrightimgemptymodal { background: transparent url("' . self::$imgfolder . 'lead-generator-pop-up-user-bg.png") center no-repeat; position: absolute; top: -62px; right: 24px; height: 160px; border-radius: 150px; width: 160px}';
            } else {
                $out .= '#leadGenerator .toprrightimgmodal { background: transparent url("' . self::$imgfolder . 'lead-generator-pop-up-user-default-man-bg.png") center no-repeat; position: absolute; top: -62px; right: 24px; height: 160px; border-radius: 150px; width: 160px}';
            }

            $out .= '</style>';
            $out .= '<script>jQuery(document).ready(function () {urlLeadGenerate = "' . self::$urlLeadGenerator . '";});</script>';
        }

        return $out;
    }

    /**
     * Return the time using the user timezone.
     * @param string $timezone the timezone from the user
     * @param date $date_time the time
     * @return String with the time
     */
    public static function convertDateTime($date_time, $timezone) {
        if (empty($timezone)) {
            $timezone = 'UTC';
        }
        $newtimezone = new DateTimeZone($timezone);
        $newdate = new DateTime($date_time);
        $newdate->setTimezone($newtimezone);
        $generate_time = $newdate->format('H:i');
        $generate_date = $newdate->format('d/m/y');
        return $generate_date . ',' . $generate_time;
    }

    /**
     * Generate the report score row HTML
     * @param object $report the report row. Factors cols shall be already converted to array
     * @param object $generalscore The array with the report generic score
     * @param string $format The format/theme we shall output. Default: BIG
     * @return string The html of the report score (the top part of a report). 
     */
    public static function getReportScoreHTML($report, $generalscore, $format = self::BIG, $disable_pdf = false, $show_header = TRUE, $show_title = TRUE, $show_category = TRUE) {
        $out = "";
        if ($show_header) {
            $report_url = trim(str_replace("http://", "", str_replace("https://", "", $report->url)), " /\\");

            $score_raw_total = $generalscore['factors']['missing'] + $generalscore['factors']['green'] + $generalscore['factors']['orange'] + $generalscore['factors']['red'];

            if (isset($_GET['pdf']) && !empty($_GET['pdf'])) {
                $classResponsiveFactorsPercent = 'width: 25% !important; float: left !important;';
                $classResponsiveScores = 'width: 41.66666667% !important; float: left !important;';
                $classResponsiveFactorSite = 'width: 33.33333333% !important; float: left !important;';
            } else {
                $classResponsiveFactorsPercent = '';
                $classResponsiveScores = '';
                $classResponsiveFactorSite = '';
            }
            //improve that?!?!?!?!?!?!?!?!?!?            
            $translatedwords = array();

            if ((isset($_GET["pdf"]) && isset($_GET["detectedLanguage"])) || isset($_COOKIE['detectedLanguage'])) {
                $detectedLanguage = @$_GET["detectedLanguage"];
                if (!$detectedLanguage) {
                    $detectedLanguage = $_COOKIE['detectedLanguage'];
                }
                switch ($detectedLanguage) {
                    case 'en':
                        $translatedwords['overall'] = 'Overall';
                        $translatedwords['outof'] = 'out of';
                        $translatedwords['updatenow'] = 'Update now';
                        $translatedwords['downloadpdfreport'] = 'Download PDF Report';
                        $translatedwords['successfullypased'] = 'Successfully passed';
                        $translatedwords['reportforurl'] = 'Report for URL';
                        $translatedwords['roomforimprovement'] = 'Room for improvement';
                        $translatedwords['errors'] = 'Errors';
                        $translatedwords['generatedon'] = 'Generated on';
                        break;
                    case 'ro':
                        $translatedwords['overall'] = 'Total';
                        $translatedwords['outof'] = 'din';
                        $translatedwords['updatenow'] = 'Actualizati acum';
                        $translatedwords['downloadpdfreport'] = 'Descarcati raportul ca PDF';
                        $translatedwords['successfullypased'] = 'Trecut cu succes';
                        $translatedwords['reportforurl'] = 'Raport pentru URL-ul';
                        $translatedwords['roomforimprovement'] = 'De imbunatatit';
                        $translatedwords['errors'] = 'Erori';
                        $translatedwords['generatedon'] = 'Generat la';
                        break;
                    case 'de':
                        $translatedwords['overall'] = 'Über alle';
                        $translatedwords['outof'] = 'aus';
                        $translatedwords['updatenow'] = 'Jetzt aktualisieren';
                        $translatedwords['downloadpdfreport'] = 'Download PDF Bericht';
                        $translatedwords['successfullypased'] = 'Erfolgreich bestanden';
                        $translatedwords['reportforurl'] = 'Bericht für URL';
                        $translatedwords['roomforimprovement'] = 'Raum für Verbesserung';
                        $translatedwords['errors'] = 'Fehler';
                        $translatedwords['generatedon'] = 'Generiert am';
                        break;
                    case 'fr':
                        $translatedwords['overall'] = 'Globale';
                        $translatedwords['outof'] = 'sur';
                        $translatedwords['updatenow'] = 'Mettre à jour maintenant';
                        $translatedwords['downloadpdfreport'] = 'Télécharger le PDF Rapport';
                        $translatedwords['successfullypased'] = 'Passé avec succès';
                        $translatedwords['reportforurl'] = 'Rapport pour URL';
                        $translatedwords['roomforimprovement'] = 'Marge d\'amélioration';
                        $translatedwords['errors'] = 'Les erreurs';
                        $translatedwords['generatedon'] = 'Généré le';
                        break;
                    default:
                        $translatedwords['overall'] = 'Overall';
                        $translatedwords['outof'] = 'out of';
                        $translatedwords['updatenow'] = 'Update now';
                        $translatedwords['downloadpdfreport'] = 'Download PDF Report';
                        $translatedwords['successfullypased'] = 'Successfully passed';
                        $translatedwords['reportforurl'] = 'Report for URL';
                        $translatedwords['roomforimprovement'] = 'Room for improvement';
                        $translatedwords['errors'] = 'Errors';
                        $translatedwords['generatedon'] = 'Generated on';
                }
            } else {
                $translatedwords['overall'] = 'Overall';
                $translatedwords['outof'] = 'out of';
                $translatedwords['updatenow'] = 'Update now';
                $translatedwords['downloadpdfreport'] = 'Download PDF Report';
                $translatedwords['successfullypased'] = 'Successfully passed';
                $translatedwords['reportforurl'] = 'Report for URL';
                $translatedwords['roomforimprovement'] = 'Room for improvement';
                $translatedwords['errors'] = 'Errors';
                $translatedwords['generatedon'] = 'Generated on';
            }

            $classPercent = (isset($_GET['pdf']) && !empty($_GET['pdf'])) ? '' : 'col-sm-4 col-md-2';
            $divLoadingCircle = '<div class="loadingCircle"></div>';
            $out .= '<div class="row score-table">';
            $score = (int) $generalscore['percentage'] > 1 ? round($generalscore['percentage']) : "&nbsp;";
            $out .= '<div class="' . $classPercent . ' col-lg-3 col-lg-3 factors-percent" style="padding:0 ' . $classResponsiveFactorsPercent . '">' // factors-percent
                    . '<aside>'
                    . '<div class="overall-score" id="overall-score">'
                    . '<p style="padding-bottom: 0px">' . $translatedwords['overall'] . '</p>'
                    . '<h5 class="reportfinalscore" style="padding-bottom: 0px">' . $score . '</h5>'
                    . '<p style="padding-bottom: 0px">' . $translatedwords['outof'] . ' 100</p>'
                    . '<div class="circle" id="circles" data-percent="' . $generalscore['percentage'] . '" ></div>' // percentage chart
                    . '<div class="loadingCircleExternal"><div class="loadingCircle" style="display:none;"></div>'
                    . '</div>'
                    . '</div><!-- #overall-score -->' // overall
                    . '<div class="additional-ratings">'
                    . '<span>' . $translatedwords['generatedon'] . ' ' . self::convertDateTime($report->date_created, 'UTC') . '</span>';
            if ($disable_pdf == FALSE) {
                $out .= '<a id="update-now" onclick="hasSupport()">' . $translatedwords['updatenow'] . '</a></span>';
            }
            $out .= '<ul id="rating-stars">';
            $ratings = array('starsbg' => 'star-o', 'stars' => 'star'); // store rating stars
            foreach ($ratings as $position => $stars):
                $out .= '<li class="rating-' . $position . '" style="' . ( $position == 'stars' ? 'width:' . round($generalscore['percentage']) / 10 * 10.6 . 'px' : '' ) . '"><div>';
                for ($i = 0; $i < 5; $i++): // 5 stars
                    $out .= '<i class="fa fa-' . $stars . '"></i>';
                endfor;
                $out .= '</div></li>';
            endforeach;
            $out .= '</ul>'
                    . '</div>' // additional ratings
                    . '</aside>';

            if ($disable_pdf) {
                if (!isset($_GET['pdf']) && empty($_GET['pdf'])) {
                    $out .= '<div><a data-enabled="true" data-href="/export?id=' . $report->id . '&amp;type=pdf" id="download-pdf">Download PDF Report</a></div>';
//                    $out .= '<div><a href="https://www.eranker.com/export?id=' . $report->id . '&amp;type=pdf" id="download-pdf">'. $translatedwords['downloadpdfreport'] .'</a></div>';
                }
            } else {
//                $out .= '<div><a href="/' . $report->domain . '/' . $report->id .
//                        $out .= '<div><a download id="download-pdf" onclick="hasSupport()">'. $translatedwords['downloadpdfreport'] .'</a></div>';
            }

            $thumb_URL = "https://www.eranker.com/IMAGE"; //FIXTHIS

            if (!isset($generalscore['thumbnail']) || empty($generalscore['thumbnail'])) {
                $thumb_URL = self::$imgfolder . "loading-page-preview.gif";
            } else {
                $thumb_URL = $generalscore['thumbnail'];
            }
            $classfactorSite = (isset($_GET['pdf']) && !empty($_GET['pdf'])) ? ' ' : 'col-md-5 hidden-xs hidden-sm';
            $classScores = (isset($_GET['pdf']) && !empty($_GET['pdf'])) ? ' ' : 'col-sm-8 col-md-5';

            if ($report->status == "WAITING") {
                $generalscore['factors']['green'] = 0;
                $generalscore['factors']['missing'] = 0;
                $generalscore['factors']['orange'] = 0;
                $generalscore['factors']['red'] = 0;
            }

            $out .= '</div>' // end factors-percent
                    . '<div class="' . $classScores . ' col-lg-5 factors-score" style="' . $classResponsiveScores . '">' // factors score
                    . '<p>' . $translatedwords['reportforurl'] . ':</p>'
                    . '<h1>' . $report_url . '</h1>'
                    . '<ul>'
                    . '<li class="col green"><i class="fa fa-check"></i><b class="factor-score">' . $translatedwords['successfullypased'] . '<span>' . $generalscore['factors']['green'] . '</span></b><div class="factorbar" style="width:' . ($generalscore['factors']['green'] * 100 / $score_raw_total) . '%"></div></li>'
                    . '<li class="col orange"><i class="fa fa-minus"></i><b class="factor-score">' . $translatedwords['roomforimprovement'] . '<span>' . $generalscore['factors']['orange'] . '</span></b><div class="factorbar" style="width:' . ($generalscore['factors']['orange'] * 100 / $score_raw_total) . '%"></div></li>'
                    . '<li class="col red"><i class="fa fa-times"></i><b class="factor-score">' . $translatedwords['errors'] . '<span>' . ( $generalscore['factors']['red'] + $generalscore['factors']['missing'] ) . '</span></b><div class="factorbar" style="width:' . ($generalscore['factors']['red'] * 100 / $score_raw_total) . '%"></div></li>'
                    . '</ul>'
                    . '<div class="clearfix"></div>'
                    . '</div>' // end factors-score
                    . '<div class="' . $classfactorSite . ' col-lg-4 factors-site" style="' . $classResponsiveFactorSite . '">' // site screen
                    . '<div class="printscreen">'
                    . '<img id="sitescreen" alt="Website Screenshot: ' . $report_url . '" src="' . $thumb_URL . '">' // actual site screen
                    . '</div>'
                    . '</div>'; // end factors-site
            $out .= '</div><div class="clearfix"></div>'; // end score-table            
        }

        return $out;
    }

    /**
     * Render a single factor to html
     * @param object $report The report object
     * @param object $factor The full factor object
     * @param array $score The score for this factor for this report
     * @param boolean $is_even If the row is even or not
     * @param boolean $is_loggedin If the user is logged in
     * @return string the HTML of the rendered factor
     */
    public static function getFactorHTML($report, $factor, $score, $is_even = false, $is_loggedin = false, $show_header, $show_title, $show_category, $title = null) {
        //For this to aways be tru for now
        $is_loggedin = true;

        $factor = (object) $factor;

        $out = "";
        switch ($score['model']['status']) {
            case 'MISSING':
            case 'RED': {
                    $status = 'times';
                    break;
                }
            case 'ORANGE': {
                    $status = 'minus';
                    break;
                }
            case 'GREEN': {
                    $status = 'check';
                    break;
                }
            case 'NEUTRAL': {
                    $status = 'info-circle';
                    break;
                }
            default: {
                    $status = 'question-circle';
                    break;
                }
        }
        $available = in_array($factor->id, $report->factors_available);

        $status = $is_loggedin ? $status : 'question-circle';
        $statuscolor = $is_loggedin ? strtolower($score['model']['status']) : '';

        $out .= '<div data-id="' . $factor->id . '" data-factorready="' . ($available ? '1' : '0') . '" class="noselect erfactor ' . ($is_even ? 'even' : 'odd') . '" id="factor-' . $factor->id . '" data-status="' . $score['model']['status'] . '" '
                . 'onclick="' . ( $is_loggedin ? 'niceToggle(jQuery(this).attr(\'id\'))' : '' ) . '">';
        $out .=!is_null($title) ? $title : "";
        $out .= '<div class="row">';
        $out .= '<div class="factor-name col-sm-12 col-md-4 col-lg-3">';
        $out .= '<div class="factor-name-inside">';

        if ($available) {
            $out .= ( $status ? '<i class="erankerreporticonspacer fa fa-' . $status . ' ' . $statuscolor . '"></i>' : '' ) . (isset($factor->text["friendly_name"]) ? $factor->text["friendly_name"] : '');
        } else {
            $out .= '<i class="erankerreporticonspacer fa fa-cog fa-spin"></i>' . (isset($factor->text["friendly_name"]) ? $factor->text["friendly_name"] : '');
        }
        $out .= '</div><!-- .factor-name-inside -->';


        $out .= '<div class="ericonsrow">';

        $totalIcons = 3;


        $impactTitle = "High Impact";
        $impact = 3;
        if ($factor->correlation < 0.1) {
            $impactTitle = "Low Impact";
            $impact = 1;
        } else {
            if ($factor->correlation < 0.25) {
                $impactTitle = "Medium Impact";
                $impact = 2;
            }
        }


        $out .= '<div title="' . $impactTitle . '" class="erankertooltip errankerreportficons errankerreportficons-red">';
        for ($i = 0; $i < $totalIcons; $i++) {
            if ($i < $impact) {
                $out .= '<i class="fa fa-heart"></i>';
            } else {
                $out .= '<i class="fa fa-heart-o"></i>';
            }
        }
        $out .= '</div><!-- .erankertooltip.errankerreportficons.errankerreportficons-red -->';


        $dificulty = 1;
        $dificultTitle = "Easy to Solve";
        if (strcasecmp($factor->difficulty_level, "MEDIUM") === 0) {
            $dificultTitle = "Moderate difficulty";
            $dificulty = 2;
        }
        if (strcasecmp($factor->difficulty_level, "HARD") === 0) {
            $dificultTitle = "Hard to Solve";
            $dificulty = 3;
        }

        $out .= '<div title="' . $dificultTitle . '" class="erankertooltip errankerreportficons errankerreportficons-yellow" >';
        for ($i = 0; $i < $totalIcons; $i++) {
            if ($i < $dificulty) {
                $out .= '<i class="fa fa-star"></i>'; //fa-star-half-o
            } else {
                $out .= '<i class="fa fa-star-o"></i>';
            }
        }

        $out .= '</div><!-- .erankertooltip.errankerreportficons.errankerreportficons-yellow -->';

        $out .= '</div><!-- .ericonsrow -->';

        $out .= '</div><!-- .factor-name -->';

        $out .= '<div class="factor-data col-sm-12 col-md-8 col-lg-9 noselect">';

        if ($available) {
            $out .= self::getFactorHTMLHelper($report, $factor, $score['model']['model'], $score['data'], $score['model']['status'], $is_loggedin);
        } else {
            $out .= '<i class="fa fa-cog fa-spin"></i> Loading...';
        }

        //if not anchors-text or responsiveness or page in links
        //close div else div is closed in guiAnchorstext function and responsiveness                   
        if ((strcasecmp($factor->id, 'anchors-text') !== 0 && strcasecmp($factor->id, 'responsiveness') !== 0) || (strcasecmp($factor->id, 'anchors-text') !== 0 && strcasecmp($factor->id, 'responsiveness') !== 0 && $available) || !$available || (isset($_SESSION['nullDisplay']) && $_SESSION['nullDisplay'] === "nullDisplay" && strcasecmp($factor->id, 'anchors-text') === 0)) {

            $out .= '</div><!-- .factor-data -->';
        }

        if (strcasecmp($factor->id, 'backlinks') == 0) {
            $out .= '<div class="col can-float factor-data-backlinks">';
            $out .= '</div>';
        }

        $out .= $is_loggedin ? '<div class="clearfix col factor-info"><p>' . stripslashes(html_entity_decode(stripslashes($score['model']['description']))) . '</p></div>' : '';

        $out .= '<div class="clearfix"></div>'
                . '</div><!-- .row -->'
                . '<i class="fa fa-minus expandtoggle show-details"></i>'
                . '</div><!-- .erfactor -->';

        return $out;
    }

    /**
     * Get the ajax report object
     * @param object $report the report object
     * @param array $reportFactors all factors object from report
     * @param object $score the report scores
     * @param string $factorsList string list the factors (comma)
     * @param boolean $is_userloggedin if the user is loggedin;
     */
    public static function ajaxReport($report, $reportFactors, $score, $factorsList, $is_userloggedin) {
        $ajax_factors = explode(',', trim($factorsList));
        $reportFactors = self::objectToArray($reportFactors);
        $output = array();
        //Add the base report score
        $output['score'] = $score->score;
        $output['status'] = $report->status;

        //Add on the output data, if the factor is avaiable, factor name, status and the HTML
        if (!empty($ajax_factors)) {
            foreach ($ajax_factors as $ajax_factor_id) {
                if (!in_array($ajax_factor_id, $report->factors_available)) {
                    continue;
                }
                $output[$ajax_factor_id] = array();
                $scoreobj = self::objectToArray($score->$ajax_factor_id);
                $output[$ajax_factor_id]['friendly_name'] = $scoreobj['model']['friendly_name'];
                $output[$ajax_factor_id]['status'] = $scoreobj['model']['status'];
                $output[$ajax_factor_id]['html'] = self::getFactorHTMLHelper($report, $reportFactors[$ajax_factor_id], $scoreobj["model"]["model"], $scoreobj["data"], $scoreobj["model"]["status"], $is_userloggedin);
            }
        }
        return $output;
    }

    public static function getFactorHTMLHelper($report, $factor, $endModel, $data, $status, $is_loggedin) {
        $html = "";

        $factor = self::objectToArray($factor);

        $endModel = html_entity_decode(stripslashes($endModel));

        if ($is_loggedin || (!$is_loggedin && $factor['free'])) {
            $html .= forward_static_call(array(self::NAME, 'gui' . ucfirst($factor['gui_type'])), html_entity_decode($endModel), $data, $report, $factor);
        } else {
            $html .= '<div class="has-blur"></div>';
        }

        return $html;
    }

    public static function guiDefault($endModel, $data, $report, $factor) {
        return is_null($endModel) ? $data : $endModel;
    }

    public static function guiInstagram($endModel, $data, $report, $factor) {
        $out = '<div class="row guiinstagram">';

        if (!empty($data)) {
            ((isset($data['name']) && $data['name'] != '') && (isset($data['profile_icon']) && $data['profile_icon'] != '')) ? ($out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("name", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">'
                            . '<img src="' . (eRankerCommons::fixURL($data["profile_pic"]) !== false ? eRankerCommons::fixURL($data["profile_pic"]) : $data["profile_pic"]) . '" style="width:18px;height:18px;cursor:pointer;margin-right:6px;margin-top:-2px;">' . ucfirst($data['name'])
                            . '</div>') : ((isset($data['name']) && $data['name'] != '') ? ($out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("name", $factor) . ':</b></div>'
                                    . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . ucfirst($data['name']) . '</div>') : $out .= '');

            (isset($data['instagram']) && $data['instagram'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><img src="' . self::$imgfolder . 'technologies/instagram-icon.png" style="margin-right:6px;margin-top:-2px;"></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft"><a href="' . (eRankerCommons::fixURL($data["instagram"]) !== false ? eRankerCommons::fixURL($data["instagram"]) : $data["instagram"]) . '" target="_blank">' . $data['instagram'] . '</a></div>' : $out .= '';

            (isset($data['biography']) && $data['biography'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("biography", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . ucfirst($data['biography']) . '</div>' : $out .= '';

            (isset($data['followedby']) && $data['followedby'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("followedby", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['followed_by'] . '</div>' : $out .= '';

            (isset($data['following']) && $data['following'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("following", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['following'] . '</div>' : $out .= '';
        } else {
            $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft">' . self::translate("model_red", $factor) . '</div>';
        }

        $out .= '</div>';

        return $out;
    }

    public static function guiTwitter($endModel, $data, $report, $factor) {
        $out = '<div class="row guitwitter">';

        if (!empty($data)) {
            (isset($data['img_background']) && $data['img_background'] != '') ? $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft"><a href="' . (eRankerCommons::fixURL($data['img_background']) !== false ? eRankerCommons::fixURL($data['img_background']) : $data['img_background']) . '" target="_blank">'
                            . '<img src="' . (eRankerCommons::fixURL($data['img_background']) !== false ? eRankerCommons::fixURL($data['img_background']) : $data['img_background']) . '" style="width:100%;margin-bottom:25px;margin-top:5px">'
                            . '</a>'
                            . '</div>' : $out .= '';

            (isset($data['twitter']) && $data['twitter'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><img src="' . self::$imgfolder . 'technologies/Twitter Follow Button.png" style="margin-right:6px;margin-top:-2px;"></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">'
                            . '<a href="' . (eRankerCommons::fixURL($data["twitter"]) !== false ? eRankerCommons::fixURL($data["twitter"]) : $data["twitter"]) . '" target="_blank">' . $data['twitter']
                            . '</a>'
                            . '</div>' : $out .= '';

            ((isset($data['name']) && $data['name'] != '') && (isset($data['img_profile']) && $data['img_profile'] != '')) ? ($out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("name", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">'
                            . '<img src="' . (eRankerCommons::fixURL($data["img_profile"]) !== false ? eRankerCommons::fixURL($data["img_profile"]) : $data["img_profile"]) . '" style="width:18px;height:18px;cursor:pointer;margin-right:6px;margin-top:-2px;">' . ucfirst($data['name'])
                            . '</div>') : ((isset($data['name']) && $data['name'] != '') ? ($out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("name", $factor) . ':</b></div>'
                                    . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . ucfirst($data['name']) . '</div>') : $out .= '');

            (isset($data['followers']) && $data['followers'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("followers", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['followers'] . '</div>' : $out .= '';

            (isset($data['bio']) && $data['bio'] != "") ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("bio", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['bio'] . '</div>' : $out .= '';

            (isset($data['location']) && $data['location'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("location", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['location'] . '</div>' : $out .= '';

            (isset($data['tweets']) && $data['tweets'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("tweets", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['tweets'] . '</div>' : $out .= '';
        } else {
            $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft">' . self::translate("model_red", $factor) . '</div>';
        }

        $out .= '</div>';

        return $out;
    }

    public static function guiLinkedin($endModel, $data, $report, $factor) {
        $out = '<div class="row guilinkedin">';

        if (!empty($data)) {
            ((isset($data['name']) && $data['name'] != '') && (isset($data['profile_img']) && $data['profile_img'] != '')) ? ($out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("name", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">'
                            . '<img src="' . (eRankerCommons::fixURL($data["profile_img"]) !== false ? eRankerCommons::fixURL($data["profile_img"]) : $data["profile_img"]) . '" style="width:18px;height:18px;cursor:pointer;margin-right:6px;margin-top:-2px;">' . ucfirst($data['name'])
                            . '</div>') : ((isset($data['name']) && $data['name'] != '') ? ($out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("name", $factor) . ':</b></div>'
                                    . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . ucfirst($data['name']) . '</div>') : $out .= '');

            (isset($data['linkedin']) && $data['linkedin'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><img src="' . self::$imgfolder . 'technologies/LinkedIn Platform API.png" style="margin-right:6px;margin-top:-2px;"></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">'
                            . '<a href="' . (eRankerCommons::fixURL($data["linkedin"]) !== false ? eRankerCommons::fixURL($data["linkedin"]) : $data["linkedin"]) . '" target="_blank">' . $data['linkedin'] . '</a>'
                            . '</div>' : $out .= '';

            (isset($data['type']) && $data['type'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("type", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . ucfirst($data['type']) . '</div>' : $out .= '';

            (isset($data['description']) && $data['description'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("description", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['description'] . '</div>' : $out .= '';

            (isset($data['specialties']) && $data['specialties'] != '') ?
                            $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("specialties", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['specialties'] . '</div>' : $out .= '';

            (isset($data['industry']) && $data['industry'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("industry", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['industry'] . '</div>' : $out .= '';

            (isset($data['size']) && $data['size'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("size", $factor) . ':</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['size'] . '</div>' :
                            $out .= '';
        } else {
            $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft">' . self::translate("model_red", $factor) . '</div>';
        }

        $out .= '</div>';

        return $out;
    }

    public static function guiGoogleplussocial($endModel, $data, $report, $factor) {
        $out = '<div class="row guigoogleplussocial">';

        if (!empty($data)) {
            (isset($data['image_background']) && $data['image_background'] != '') ? $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft googleplusfactor">'
                            . '<a href="' . (eRankerCommons::fixURL($data['image_background']) !== false ? eRankerCommons::fixURL($data['image_background']) : $data['image_background']) . '" target="_blank">'
                            . '<img src="' . (eRankerCommons::fixURL($data['image_background']) !== false ? eRankerCommons::fixURL($data['image_background']) : $data['image_background']) . '" class="imgbgrdgoogleplus">'
                            . '</a>'
                            . '</div>' : $out .= '';

            (isset($data['google_plus']) && $data['google_plus'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><img src="' . self::$imgfolder . 'technologies/Google Plus One Button.png" style="margin-right:6px;margin-top:-2px;"></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">'
                            . '<a href="' . (eRankerCommons::fixURL($data["google_plus"]) !== false ? eRankerCommons::fixURL($data["google_plus"]) : $data["google_plus"]) . '" target="_blank">' . $data['google_plus']
                            . '</a>'
                            . '</div>' : $out .= '';

            ((isset($data['name']) && $data['name'] != '') && (isset($data['image_profile']) && $data['image_profile'] != '')) ? ($out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("name", $factor) . '</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">'
                            . '<img src="' . (eRankerCommons::fixURL($data["image_profile"]) !== false ? eRankerCommons::fixURL($data["image_profile"]) : $data["image_profile"]) . '" style="width:18px;height:18px;cursor:pointer;margin-right:6px;margin-top:-2px;">' . ucfirst($data['name'])
                            . '</div>') : ((isset($data['name']) && $data['name'] != '') ? ($out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("name", $factor) . '</b></div>'
                                    . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . ucfirst($data['name']) . '</div>') : $out .= '');

            (isset($data['tagline']) && $data['tagline'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("tagline", $factor) . '</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . ucfirst($data['tagline']) . '</div>' : $out .= '';

            (isset($data['introduction']) && $data['introduction'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("introduction", $factor) . '</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . ucfirst($data['introduction']) . '</div>' : $out .= '';

            (isset($data['email']) && $data['email'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("email", $factor) . '</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . ucfirst($data['email']) . '</div>' : $out .= '';

            (isset($data['followers']) && $data['followers'] != '') ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("followers", $factor) . '</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['followers'] . '</div>' : $out .= '';

            (isset($data['views']) && $data['views'] != "") ? $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("views", $factor) . '</b></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['views'] . '</div>' : $out .= '';
        } else {
            $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft">' . self::translate("model_red", $factor) . '</div>';
        }

        $out .= '</div>';

        return $out;
    }

    public static function guiFacebooksocial($endModel, $data, $report, $factor) {
        $out = '<div class="row guifacebooksocial">';

        if (!empty($data) && count($data) > 1) {
            if (isset($data['img_background']) && !empty($data['img_background'])) {
                $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft facebooksocialfactor">'
                        . '<a href="' . (eRankerCommons::fixURL($data['img_background']) !== false ? eRankerCommons::fixURL($data['img_background']) : $data['img_background']) . '" target="_blank">'
                        . '<img src="' . (eRankerCommons::fixURL($data['img_background']) !== false ? eRankerCommons::fixURL($data['img_background']) : $data['img_background']) . '" class=".imgbgrdfacebooksocial">'
                        . '</a>'
                        . '</div>';
            }

            if (isset($data['img_profile']) && !empty($data['img_profile'])) {
                if (isset($data['facebook']) && !empty($data['facebook'])) {
                    $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><img src="' . self::$imgfolder . 'technologies/Facebook Like Button.png" style="margin-right:6px;margin-top:-2px;"></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">'
                            . '<img src="' . (eRankerCommons::fixURL($data["img_profile"]) !== false ? eRankerCommons::fixURL($data["img_profile"]) : $data["img_profile"]) . '" style="width:18px;height:18px;cursor:pointer;margin-right:6px;margin-top:-2px;">'
                            . '<a href="' . (eRankerCommons::fixURL($data["facebook"]) !== false ? eRankerCommons::fixURL($data["facebook"]) : $data["facebook"]) . '" target="_blank">' . $data['facebook']
                            . '</a>'
                            . '</div>';
                }
            } else {
                if (isset($data['facebook']) && !empty($data['facebook'])) {
                    $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><img src="' . self::$imgfolder . 'technologies/Facebook Like Button.png" style="margin-right:6px;margin-top:-2px;"></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">'
                            . '<a href="' . (eRankerCommons::fixURL($data["facebook"]) !== false ? eRankerCommons::fixURL($data["facebook"]) : $data["facebook"]) . '" target="_blank">' . $data['facebook']
                            . '</a>'
                            . '</div>';
                }
            }

            if (isset($data['company_type']) && !empty($data['company_type'])) {
                $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("company_type", $factor) . ':</b></div>'
                        . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['company_type'] . '</div>';
            }

            if (isset($data['short_description']) && !empty($data['short_description'])) {
                $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("short_description", $factor) . ':</b></div>'
                        . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['short_description'] . '</div>';
            }

            if (isset($data['website']) && !empty($data['website'])) {
                $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("website", $factor) . ':</b></div>'
                        . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['website'] . '</div>';
            }

            if (isset($data['phone']) && !empty($data['phone'])) {
                $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("phone", $factor) . ':</b></div>'
                        . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft"><img src="' . self::$factorCreateImageFolder . 'createimage.php?size=11&amp;transparent=1&amp;padding=0&amp;bgcolor=250&amp;textcolor=50&amp;text=' . urlencode(strrev(base64_encode($data['phone']))) . '" alt="Website Phone Number"></div>';
            }

            if (isset($data['review_number']) && !empty($data['review_number'])) {
                $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("reviews", $factor) . ':</b></div>'
                        . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['review_number'] . '</div>';
            }

            if (isset($data['review_stars']) && !empty($data['review_stars'])) {
                $stars = '';

                for ($i = 0; $i < round((int) $data['review_stars']); $i++) {
                    $stars .= '<i class="fa fa-star" style="color: #8DBE56"></i>';
                }

                $out .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><b>' . self::translate("rating", $factor) . ':</b></div>'
                        . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $stars . '</div>';
            }
        } else {
            $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">' . self::translate("model_red", $factor) . '</div>';
        }

        $out .= '</div>';

        return $out;
    }

    public static function guiInpagelinks($endModel, $data, $report, $factor) {

        $out = '<div class="row">';

        if (!empty($data)) {
            $dataforchart = $data[count($data) - 1];
            if (isset($dataforchart['total']) && $dataforchart['total'] !== 0) {
                $attr = '';

                $total = 0;

                foreach ($dataforchart as $key => $value) {
                    $attr .= 'data-' . $key . '=' . $value . ' ';
                    if ($key !== "total" && $key !== "highcharts-chart") {
                        $total += (int) $value;
                    }
                }

                $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft"><b>' . self::translate('total', $factor) . ':</b> ' . $total . '</div>';
                $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 chartinpagelinks nopaddingleft paddingupdown" data-chartready="false" ' . $attr . '></div>';

                //legend
                //table html
//                $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft" style="width: 40%;margin-top: 6px;">'
//                            .'<table class="table table-condensed table-bordered">'
//                                .'<thead>'
//                                    . '<tr style="background-color: #C2C0C0;"><th><b>'. self::translate('legend', $factor) .'</b></th></tr>'
//                                . '</thead>'
//                                . '<tbody>'
//                                    . '<tr><td><p><i class="fa fa-check" style="color:green;"></i>: '. self::translate('legend_underscore', $factor) .'</p></td></tr>'
//                                    . '<tr><td><p><i class="fa fa-link" style="font-size:15px;"></i>: '. self::translate('internal-link', $factor) .'</p></td></tr>'
//                                    . '<tr><td><p><i class="fa fa-external-link" style="font-size:15px;"></i>: '. self::translate('external-link', $factor) .'</p></td></tr>'
//                                    . '<tr><td><p><i class="fa fa-expand" style="font-size:15px; color:#EF7622;padding: 5px;border: 1px solid #EF7622;"></i>,'
//                                                . '<i class="fa fa-compress" style="font-size:15px; color:#EF7622;padding: 5px;border: 1px solid #EF7622;margin-left:3px;"></i>: '. self::translate('legend_table', $factor) .'</p></td></tr>'
//                                . '</tbody>'
//                            . '</table>'                     
//                        . '</div>'; 
                //legend
                //bootstrap html
                $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft" style="margin-top: 8px;font-size: 12px;">'
                        . '<div class="row">'
                        . '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft"><p style="background-color: #C2C0C0;"><b>' . self::translate('legend', $factor) . '</b></p></div>'
                        . '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft"><p><i class="fa fa-check" style="color:green;"></i>: ' . self::translate('legend_underscore', $factor) . '</p></div>'
                        . '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft"><p><i class="fa fa-link" style="font-size:15px;"></i>: ' . self::translate('internal-link', $factor) . '</p></div>'
                        . '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft"><p><i class="fa fa-external-link" style="font-size:15px;"></i>: ' . self::translate('external-link', $factor) . '</p></div>'
                        . '</div>'
                        . '</div>';

                $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft paddingupdown">';
                $out .= "<table class='table table-condensed table-bordered tabletocollapse'>"
                        . "<thead>"
                        . '<tr style="background-color: #C2C0C0;"><th><strong>' . self::translate('anchor', $factor) . '</strong></th>
                                <th class="hidden-xs hidden-sm"><strong>' . self::translate('type', $factor) . '</strong></th>
                                <th class="hidden-xs"><strong>' . self::translate('follow', $factor) . '</strong></th>
                         </tr>'
                        . "</thead>"
                        . "<tbody>";
                $howmuchtoshow = 0;

                for ($i = 0; $i < count($data) - 1; $i++) {
                    $howmuchtoshow++;

                    $out .= '<tr' . ($data[$i]['type'] === 1 ? ' style="background-color: #ECECEC;"' : '')
                            . ' '
                            . ($howmuchtoshow > 10 ? 'class="hiderows"' : '')
                            . '>';

                    if ($data[$i]['type'] === 1) {
                        $icon = '<i class="fa fa-link" style="font-size:15px;margin-right:3px;padding: 11px;"></i>';
                    } else if ($data[$i]['type'] === 2) {
                        $icon = '<i class="fa fa-external-link" style="font-size:15px;margin-right:3px;padding: 11px;"></i>';
                    }

                    if (strlen($data[$i]['anchor']) > 37) {
                        $urlanchor = substr($data[$i]['anchor'], 0, 37) . '...';
                    } else {
                        $urlanchor = $data[$i]['anchor'];
                    }

                    if ($data[$i]['underscore'] === true) {
                        $yet = '<i class="fa fa-check" style="color:green; margin-left:3px;"></i>';
                    } else {
                        $yet = '';
                    }

                    if (isset($data[$i]['text']) && strlen($data[$i]['text']) > 35) {
                        $textanchor = substr($data[$i]['text'], 0, 35) . '...' . $yet;
                    } else if (isset($data[$i]['text'])) {
                        $textanchor = $data[$i]['text'] . $yet;
                    } else {
                        $textanchor = '';
                    }

                    $out .= '<td><strong>' . $icon . '</strong>'
                            . '<a href="' . (eRankerCommons::fixURL($data[$i]['anchor']) !== false ? eRankerCommons::fixURL($data[$i]['anchor']) : $data[$i]['anchor']) . '" target="_blank">'
                            . (isset($data[$i]['text']) ? $textanchor : $urlanchor)
                            . '</a>'
                            . '</td>';

                    if ($data[$i]['type'] === 1) {
                        $out .= '<td class="hidden-xs hidden-sm">' . self::translate('internal-link', $factor) . '</td>';
                    } else if ($data[$i]['type'] === 2) {
                        $out .= '<td class="hidden-xs hidden-sm">' . self::translate('external-link', $factor) . '</td>';
                    }
                    if (isset($data[$i]['follow'])) {
                        if ($data[$i]['follow'] === 1) {
                            $out .= '<td class="hidden-xs hidden-sm">' . self::translate('no-follow', $factor) . '</td>';
                        } else if ($data[$i]['follow'] === 2) {
                            $out .= '<td class="hidden-xs hidden-sm">' . self::translate('follow', $factor) . '</td>';
                        }
                    } else {
                        $out .= '<td class="hidden-xs hidden-sm">' . self::translate('no-follow', $factor) . '</td>';
                    }

                    $out .= '</tr>';
                }

                $out .= '</tbody>'
                        . '</table>'
                        . '</div>';

                if ($howmuchtoshow > 10) {
                    $out .= '<div class="despicableme col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft"><a class="expandtable" href="javascript:void(0);"><i class="fa fa-expand"></i></a></div>';
                }
            } else {
                $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">' . self::translate("model_red", $factor) . '</div>';
            }
        } else {
            $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">' . self::translate("model_red", $factor) . '</div>';
        }

        $out .= '</div>';

        return $out;
    }

    public static function guiRobotstxt($endModel, $data, $report, $factor) {
        $html = eRankerCommons::guiDefault(html_entity_decode($endModel), $data, $report, $factor);
        if (!is_null($data)) {
            $content = '';
            if ($data['valid'] && $data['robotstxt']) {
                if (gettype($data['content']) == "string" && !empty($data['content'])) {
                    if (strstr($data['content'], PHP_EOL) !== false) {
                        $a = explode(PHP_EOL, $data['content']);
                        $content .= implode("<br>", $a);
                    } else {
                        $content .= $data['content'];
                    }
                } else if (gettype($data['content']) == "array") {
                    foreach ($data['content'] as $cont) {
                        $content .= $cont . '<br>';
                    }
                }

                if (!is_null($data['url']) && !empty($data['url'])) {
                    $html .= '<br><a href="' . (eRankerCommons::fixURL($data['url']) !== false ? eRankerCommons::fixURL($data['url']) : $data['url']) . '" style="color:#555" target="_blank">' . $data['url'] . '</a><br><div class="trickydiv"><div class="robotstxtcontainer robotstoggle rttoggledown">' . $content . '</div></div>';
                } else {
                    $html .= '<br><div class="trickydiv"><div class="robotstxtcontainer robotstoggle rttoggledown">' . $content . '</div></div>';
                }

                if (strlen($content) > 250) {
                    $html .= '<a class="robotstxt" href="javascript:void(0);" onclick="'
                            . 'if(jQuery(\'.robotstoggle\').hasClass(\'rttoggledown\')){'
                            . 'robotsTxtToggle(\' ' . __('Show less', 'er') . '\');}'
                            . 'else if(jQuery(\'.robotstoggle\').hasClass(\'rttoggleup\')){'
                            . 'robotsTxtToggle(\' ' . __('Show more', 'er') . '\')}">'
                            . __('Show more', 'er')
                            . '</a>';
                }
            }
        }

        return $html;
    }

    public static function guiBlogpage($endModel, $data, $report, $factor) {
        if (!is_null($data)) {
            $html = '<a href="' . (eRankerCommons::fixURL($data) !== false ? eRankerCommons::fixURL($data) : $data) . '" rel="nofollow" style="color:#555" target="_blank">' . (is_null($endModel) ? $data : $endModel) . '</a>';
        } else {
            $html = is_null($endModel) ? $factor[id] : $endModel;
        }
        return $html;
    }

    public static function guiFavicon($endModel, $data, $report, $factor) {
        $html = '';
        if ($data != null) {
            //add in factor description this part. 
            //<img src="'. (eRankerCommons::fixURL($data) !== false ? eRankerCommons::fixURL($data) : $data) .'" style="width:18px;height:18px;cursor:pointer;margin-right:6px;margin-top:-2px;">
            $html .= '<a href="' . (eRankerCommons::fixURL($data) !== false ? eRankerCommons::fixURL($data) : $data) . '" style="color:#555" target="_blank">' . (is_null($endModel) ? $data : $endModel) . '</a>';
        } else {
            $html .= $endModel;
        }

        return $html;
    }

    public static function guiHeadings($endModel, $data, $report, $factor) {
        $out = '';
        if (!is_null($data)) {
            $obj = (object) $data;
            $out .= '<table class="report_headingtable">';
            for ($i = 1; $i <= 6; $i++) {
                $out .= $i == 1 ? '<tr class="report_headingtable_firstrow">' : '';
                $out .= '<th>&lt;H' . $i . '&gt;</th>';
                $out .= $i == 6 ? '<th>' . self::translate("total", $factor) . '</th></tr>' : '';
            }
            for ($i = 1; $i <= 6; $i++) {
                $out .= $i == 1 ? '<tr>' : '';
                $hd_i = 'h' . $i;
                $out .= '<td>' . (isset($obj->$hd_i) ? $obj->$hd_i : "?") . '</td>';
                $out .= $i == 6 ? '<td>' . (isset($obj->total) ? $obj->total : "?") . '</td></tr>' : '';
            }
            $out .= '</table>';
            if (isset($obj->tags) && !empty($obj->tags)) {
                $out .="<div class='headings_taglist'>";
                $count = 1;
                foreach ($obj->tags as $aTag) {
                    $aTag = (object) $aTag;
                    $out .="<div class='headings_tagitem headings_taglist_" . $aTag->type . "'>";
                    $out .="<div class='headingtype'>&lt;" . strtoupper($aTag->type) . "&gt;</div>";
                    $out .="<div class='headingspacer'></div>";
                    $out .=strip_tags($aTag->text);
                    $out .="</div>";
                    if ($count++ == 10) {
                        $out .="<div class='headings_taglist_more' style='display:none'>"; // > 10 wrapper
                    }
                }
                if ($count >= 10) {
                    $out .="</div>"; // > 10 wrapper close
                    $out .="<a href='javascript:jQuery(\"#erreport .headings_taglist_more\").slideDown();jQuery(\"#erreport .headings_taglist_showmore\").hide();jQuery(\"#erreport .headings_taglist_showless\").show();' class='headings_taglist_showmore' style='display:block'><i class=\"fa fa-angle-down\"></i>  " . self::translate('showmore', $factor) . "</a>";
                    $out .="<a href='javascript:jQuery(\"#erreport .headings_taglist_more\").slideUp();jQuery(\"#erreport .headings_taglist_showmore\").show();jQuery(\"#erreport .headings_taglist_showless\").hide();' class='headings_taglist_showless' style='display:none'><i class=\"fa fa-angle-up\"></i> " . self::translate('showless', $factor) . "</a>";
                }
                $out .="</div>";
            }
        }

        return empty($out) ? false : '<div class="headings-style">' . $out . '</div>';
    }

    public static function guiStructureddata($endModel, $data, $report, $factor) {
        $out = $endModel;
        if (!empty($data) && is_array($data)) {
            if (!empty($out)) {
                $out .= "<br />";
            }
            $out = implode(", ", $data);
        }
        return (!empty($data)) ? $out : self::translate("uneedimplement", $factor);
    }

    public static function guiEmails($endModel, $data, $report, $factor) {
        $out = '';
        if (!empty($data)) {
            foreach ($data as $singleEmail) {
                $out .= '<img src="' . self::$factorCreateImageFolder . 'createimage.php?size=11&amp;transparent=1&amp;padding=0&amp;bgcolor=250&amp;textcolor=50&amp;text=' . urlencode(strrev(base64_encode($singleEmail))) . '" alt="' . self::translate('sitecontactmail', $factor) . '"><br />';
            }
        }

        return empty($out) ? '<div class="emails-style">' . self::translate("emailnotfound", $factor) . '.</div>' : $out;
    }

    public static function guiLogo($endModel, $data, $report, $factor) {
        if (!is_null($data)) {
            return "<a href='" . (eRankerCommons::fixURL(str_replace("'", "", strip_tags($data))) !== false ? eRankerCommons::fixURL(str_replace("'", "", strip_tags($data))) : str_replace("'", "", strip_tags($data))) . "' target='_blank'>"
                    . "<img style='background: url(" . self::$imgfolder . "transparent-canvas-background-tile.jpg) center center; display: block; margin-bottom: 10px;' src='" . (eRankerCommons::fixURL(str_replace("'", "", strip_tags($data))) !== false ? eRankerCommons::fixURL(str_replace("'", "", strip_tags($data))) : str_replace("'", "", strip_tags($data))) . "' alt='Website Logo' style='max-width:100%;'>"
                    . "</a>";
        } else {
            return $endModel;
        }
    }

    public static function guiAlexarank($endModel, $data, $report, $factor) {
        $out = '';
        if (!is_null($data)) {
            $obj = (object) $data;
            if (!empty($obj->rank)) {
                $out = $obj->rank;
            }
        }
        return empty($out) ? '<div class="alexarank-style">' . self::translate("notlistedalexa", $factor) . '</div>' : $endModel;
    }

    public static function guiPhone($endModel, $data, $report, $factor) {

        if (empty($data)) {
            return self::translate("notfoundphones", $factor);
        }

        $out = '';
        if (isset($data) && !empty($data)) {
            foreach ($data as $singlePhone) {
                $country_code = $singlePhone['region'];
                $out .= "<img src='" . self::$imgfolder . "/flags/24/$country_code.png' style='height:24px;vertical-align:bottom;' alt='$country_code' title='$country_code' /> ";
                $type = ucfirst(strtolower(str_replace("_", " ", $singlePhone['type'])));
                $out .= '<img title="Type: ' . $type . '" src="' . self::$factorCreateImageFolder . 'createimage.php?size=11&amp;transparent=1&amp;padding=0&amp;bgcolor=250&amp;textcolor=50&amp;text=' . urlencode(strrev(base64_encode($singlePhone['phone']))) . '" alt="Website Phone Number"> <br />';
            }
        }

        return $out;
    }

    public static function guiBacklinks($endModel, $data, $report, $factor) {
        $out = '';
        $chartsData = array();

        $chartsData[] = array(array("id" => "image", "title" => "Images"), array("id" => "text", "title" => "Text"),);
//        $chartsData[] = array(array("id" => "pages", "title" => "Unique Pages"), array("id" => "refpages", "title" => "Referal Pages"));
        $chartsData[] = array(array("id" => "nofollow", "title" => "NoFollow"), array("id" => "dofollow", "title" => "DoFollow"));
        $chartsData[] = array(array("id" => "sitewide", "title" => "Site Wide"), array("id" => "not_sitewide", "title" => "Not Site Wide"));
        $chartsData[] = array(array("id" => "links_external", "title" => "Outbound links"), array("id" => "links_internal", "title" => "Internal links"));
        $chartsData[] = array(array("id" => "redirect", "title" => "Redirect"), array("id" => "canonical", "title" => "Canonical"));
        $chartsData[] = array(array("id" => "alternate", "title" => "Alternate"), array("id" => "html_pages", "title" => "HTML Pages"));

        //array('gov' => 'Gov', 'edu' => 'Edu', 'rss' => 'Rss'),

        $charts = "";
        foreach ($chartsData as $chartNumber => $singleChart) {
            if (isset($data[$singleChart[0]["id"]]) && isset($data[$singleChart[1]["id"]]) && ($data[$singleChart[0]["id"]] + $data[$singleChart[1]["id"]]) > 0) {
                $charts .= "<div class='backlinkchartwrapper'>"
                        . "<div style='width: 100%;margin: 0 auto' class='backlinkchart' data-chartready='false' "
                        . "data-id1='" . $singleChart[0]["id"] . "' data-id2='" . $singleChart[1]["id"] . "' "
                        . "data-title1='" . $singleChart[0]["title"] . "' data-title2='" . $singleChart[1]["title"] . "' "
                        . "data-value1='" . $data[$singleChart[0]["id"]] . "'  data-value2='" . $data[$singleChart[1]["id"]] . "'></div>"
                        . "</div><!-- .backlinkchartwrapper -->";
            }
        }

//        if (is_array($data)) {
//            foreach ($pairs as $pair) {
//                $chart = '<div class="hidden piechart" data-labels="true" data-donut="false" data-pos-values="true">';
//                foreach ($pair as $key => $label) {
//                    $chart .= '<div class="data-chart" id="' . $key . '" data-label="' . $label . '" data-value="' . $data[$key] . '"></div>';
//                }
//                $chart .= '</div>';
//                $out .= $chart;
//            }
//        }

        $translate1 = self::translate("totalbacklinks", $factor);

        $translate2 = self::translate("totalhefpage", $factor);
        $totalBacklinks = (isset($data['total']) && !empty($data['total'])) ? $data['total'] :0 ;
        $totalRefPages = (isset($data['refpages']) && !empty($data['refpages'])) ? $data['refpages'] :0 ;
        $top = "<h4 class='marginbottom0'>" . html_entity_decode(sprintf(stripslashes($translate1), stripslashes($totalBacklinks))) . "</h4>"
                . html_entity_decode(sprintf(stripslashes($translate2), stripslashes($totalRefPages)))
                . "</div><div class='clearfix col factor-special'>"; // trick div

        $domain = $report->url;


        return $top
                . '<div class="row" id="backlinkscharts">' . $out . '</div>'
                . '<div id="backlinkspie" class="row">' . $charts . '</div><!-- #backlinkspie -->'
                . '<div class="poweredbyout" onclick="window.open(\'https://ahrefs.com/site-explorer/overview/subdomains?target=' . urlencode($domain) . '\')"  style="display:block;text-align:center;" > '
                . '<span>Check deep link analysis on ahrefs</span><br /><img src="' . self::$imgfolder . 'ahrefs_logoSmall.png" alt="ahrefs">'
                . '</div>';
    }

    public static function guiAnchorstext($endModel, $data, $report, $factor) {

        $html = "";
        $count = 0;
        $attr = '';
        $displayAtNull = '';
        if (!empty($data['anchors'])) {
            $count = count($data['anchors']);
            foreach ($data['anchors'] as $key => $value) {
                $attr .= "data-anchor-" . $key . "='" . $value['anchor'] . "' data-backlinks-" . $key . "='" . $value['backlinks'] . "' ";
            }
        } else {
            $displayAtNull .= self::translate("cannotfindanchors", $factor);
            $_SESSION['nullDisplay'] = "nullDisplay";
        }

        if (isset($_SESSION['nullDisplay']) && $_SESSION['nullDisplay'] === "nullDisplay") {
            return $html . "<div class='clearfix row noselect anchorsconstruct'><div class='anchorschart col-xs-12 col-sm-12' id='anchorschart' data-chartready='false' data-totali=" . $count . " " . $attr . ">" . $displayAtNull . "</div></div>";
        }

        //close factor-data div
        return $html . "</div>"
                . "<div class='clearfix row noselect anchorsconstruct'>"
                . "<div class='anchorschart col-xs-12 col-sm-12' data-chartready='false' data-totali=" . $count . " " . $attr . " id='anchorschart'>"
                . $displayAtNull
                . "</div><!-- #anchorschart -->"
                . "</div>";
    }

    public static function guiMobileusability($endModel, $data, $report, $factor) {
        $totalFail = 0;
        $totalWarn = 0;
        $html = '';
        $htmlContentTable = "";
//
//        foreach ($data as $valueArray) {
//            if (isset($valueArray["fail-count"])) {
//                $totalFail = $valueArray["fail-count"];
//            }
//            if (isset($valueArray['warn-count'])) {
//                $totalWarn = $valueArray["warn-count"];
//            }
//            if (!isset($valueArray["warn-count"]) || !isset($valueArray["fail-count"])) {
//                $htmlContentTable .= "<tr>";
//                $first = TRUE;
//                foreach ($valueArray as $key => $value) {
//                    if ($first) {
//                        $icon = (strcasecmp($value, 'FAIL') === 0) ? "<i class='fa fa-times'></i>" : "<i class='fa fa-exclamation-triangle'></i>";
//                        if (strcasecmp($key, "warn-count") !== 0 || strcasecmp($key, "fail-count") !== 0) {
//                            $htmlContentTable .= "<td>" . $icon . "</td>";
//                        }
//                    } else {
//                        if (strcasecmp($key, "warn-count") !== 0 || strcasecmp($key, "fail-count") !== 0) {
//                            $htmlContentTable .= "<td>" . $value . "</td>";
//                        }
//                    }
//                    $first = FALSE;
//                }
//                $htmlContentTable .= "</tr>";
//            }
//        }
//
//        $html .= "<div class='row'>";
//        $html .= "<div class='col-lg-6'>";
//        $html .= "<h4 class='marginbottom0'><i class='fa fa-times'></i> Fails " . $totalFail . "</h4>";
//        $html .= "</div>";
//        $html .= "<div class='col-lg-6'>";
//        $html .= "<h4 class='marginbottom0'><i class='fa fa-exclamation-triangle'></i> Warns " . $totalWarn . "</h4>";
//        $html .= "</div>";
//        $html .= "</div>";
//
//        $html .= "<table class='table'>";
//        $html .= "<thead>";
//        $html .= "<tr>";
//        $html .= "<th>Severety</th>";
//        $html .= "<th>Description</th>";
//        $html .= "<th>Best Pratice</th>";
//        $html .= "</tr>";
//        $html .= "</thead>";
//        $html .= "<tbody>";
//        $html .= $htmlContentTable;
//        $html .= "</tbody>";
//
//        $html .= "</table>";
//
        return $endModel;
    }

    public static function guiOngooglemaps($endModel, $data, $report, $factor) {
        $html = "";

        if (!empty($data)) {
            $html .= "<div class='external-onmaps' >";
            if (isset($data['latitude']) && isset($data['longitude'])) {
                if (isset($_GET['pdf'])) {
                    $html .= '<img src="https://maps.googleapis.com/maps/api/staticmap?zoom=15&size=950x250&maptype=roadmap'
                            . '&markers=color:red%7Clabel:G%7C' . $data['latitude'] . ',' . $data['longitude'] . '" id="map-googlemaps" width="100%">';
                } else {
                    $html .= "<div style='height: 250px;width:100%' id='map-googlemaps' data-gmapsmapready='false' data-googlemaps-latitude='"
                            . $data['latitude'] . "' data-googlemaps-longitude='" . $data['longitude']
                            . "' data-googlemaps-accuracy='' data-googlemaps-title='" . $data['name'] . "' ></div>";
                }
                //$html .= "<h5 style='margin-bottom: 0;'><strong>" . ucfirst($data['name']) . "</strong></h5>";
            }

            if (isset($data['photo']) && !empty($data['photo'])) {
                $htmlphoto = '
                <div style="background-color: #fafafa; background-image: url(\'' . $data['photo'] . '\'), url(\'' . self::$imgfolder . 'establishment-no-thumbnail-80px.png\'); border: 3px solid #DA4336;  position: absolute; top: -38px;  right: 0px; border-radius: 5px!important; width: 80px; height: 80px; background-repeat: no-repeat; background-size: cover; border-top-left-radius: 20px!important; background-position: center; border-bottom-right-radius: 20px !important;"></div>';
            } else {
                $htmlphoto = "";
            }
            if (isset($data['place_url']) && !empty($data['place_url'])) {
                $onclick = "onclick='window.open(\"" . $data['place_url'] . "\")'";
            } else {
                $onclick = "onclick='window.open(\"" . $data['website'] . "\")'";
                ;
            }
            $html .= "<div $onclick style='cursor:pointer; position:relative; border-bottom: 1px solid #EEE;  background-color: #DA4336; color: white; padding: 5px; font-family: arial,sans-serif-light,sans-serif; font-size: 20px;'>"
                    . $htmlphoto . $data['name'] .
                    "</div>";

            $html .= "<div class='footer-map-onmaps row'>";
            if (isset($data['address']) && !empty($data['address'])) {
                $html .= "<div class='col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft'><strong>" . self::translate('address', $factor) . ":</strong></div>"
                        . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['address'] . "<br/></div>";
            }

            //i'm not sure if is phones
            if (isset($data['phones']) && !empty($data['phones'])) {
                foreach ($data['phones'] as $value) {
                    $html .= '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft"><strong>' . self::translate("phone", $factor) . ':</strong></div>'
                            . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft"><img title="Phone" src="' . self::$factorCreateImageFolder . 'createimage.php?size=11&amp;transparent=1&amp;padding=0&amp;bgcolor=250&amp;textcolor=50&amp;text=' . urlencode(strrev(base64_encode($value))) . '" alt="Phone Number"> <br /></div>';
                }
            }

            if (isset($data['phone'])) {
                $html .= "<div class='col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft'><strong>" . self::translate('phone', $factor) . ":</strong></div>"
                        . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft"><img title="Phone" src="' . self::$factorCreateImageFolder . 'createimage.php?size=11&amp;transparent=1&amp;padding=0&amp;bgcolor=250&amp;textcolor=50&amp;text=' . urlencode(strrev(base64_encode($data['phone']))) . '" alt="Phone Number"></br></div>';
            }

            if (isset($data['reviews'])) {
                $html .= "<div class='col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft'><strong>" . self::translate('reviews', $factor) . ":</strong></div>"
                        . '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft">' . $data['reviews'] . "<br/></div>";
            }

            if (isset($data['rating'])) {
                $html .= "<div class='col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft'><strong>" . self::translate('rating', $factor) . ":</strong></div>";
                $html .= "<div class='col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft'><span class='errankerreportficons-yellow'>";
                if ($data['rating'] !== 0) {
                    $html .= '<span>' . round($data['rating'], 1) . '</span> ';
                }
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= round($data['rating'])) {
                        $html .= '<i class="fa fa-star"></i>'; //fa-star-half-o
                    } else {
                        $html .= '<i class="fa fa-star-o"></i>';
                    }
                }
                $html .= "</span><br/></div>";
            }
            if (isset($data['website'])) {
                $html .= "<div class='col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft'><strong>" . self::translate('website', $factor) . ":</strong></div>"
                        . "<div class='col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft'><a href='" . $data['website'] . "' rel='nofollow' TARGET='_blank'>" . $data['website'] . "</a><br/></div>";
            }
            if (isset($data['description'])) {
                $html .= "<div class='col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft'><strong>" . self::translate('description', $factor) . ":</strong></div>"
                        . "<div class='col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft'>" . $data['description'] . "<br/></div>";
            }
            if (isset($data['industry'])) {
                $html .= "<div class='col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft'><strong>" . self::translate('industry', $factor) . ":</strong></div>"
                        . "<div class='col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft'>" . $data['industry'] . "<br/></div>";
            }

            if (isset($data['opened'])) {
                $html .= "<div class='col-xs-12 col-sm-2 col-md-2 col-lg-2 nopaddingleft'><strong>" . self::translate('opened', $factor) . ":</strong></div>"
                        . "<div class='col-xs-12 col-sm-10 col-md-10 col-lg-10 nopaddingleft'>" . $data['opened'] . "<br/></div>";
            }

            $html .= "</div>";
            $html .= "</div>";
        } else {
            $html .= $endModel;
        }

        return $html;
    }

    public static function guiServerlocation($endModel, $data, $report, $factor) {

        $out = '';

        $latitude = !empty($data) && isset($data['latitude']) ? $data['latitude'] : null;
        $longitude = !empty($data) && isset($data['longitude']) ? $data['longitude'] : null;
        $ip = !empty($data) && isset($data['ip']) ? $data['ip'] : null;

        $host = !empty($data) && isset($data['host']) ? $data['host'] : null;
        $city = !empty($data) && isset($data['city']) ? $data['city'] : null;
        $state = !empty($data) && isset($data['state']) ? $data['state'] : null;
        $country_name = !empty($data) && isset($data['country_name']) ? $data['country_name'] : null;
        $zip = !empty($data) && isset($data['zip']) ? $data['zip'] : null;
        $country_code = !empty($data) && isset($data['country_code']) ? $data['country_code'] : null;
        $accuracy_radius = !empty($data) && isset($data['accuracy_radius']) ? $data['accuracy_radius'] : null;
        $timezone = !empty($data) && isset($data['timezone']) ? $data['timezone'] : null;


        $content = "";
        if (!empty($host)) {
            $content .= "<h4 style='margin-bottom: 0;'><strong>" . ucfirst($host) . "</strong></h4>";
        }

        if (!empty($ip)) {
            $content .= "<strong>" . self::translate('serverip', $factor) . ":</strong> " . $ip . "<br />";
        }
        if (!empty($city)) {
            $content .= "<strong>" . self::translate('city', $factor) . ":</strong> " . $city . "<br />";
        }
        if (!empty($state)) {
            $content .= "<strong>" . self::translate('stateserverlocation', $factor) . ":</strong> " . $state . "<br />";
        }
        if (!empty($zip)) {
            $content .= "<strong>" . self::translate('zipcode', $factor) . ":</strong> " . $zip . "<br />";
        }
        if (!empty($country_code)) {
            $content .= "<strong>" . self::translate('countryserverlocatio', $factor) . ":</strong> <img src='" . self::$imgfolder . "/flags/24/$country_code.png' style='height: 16px;vertical-align: sub;' alt='$country_code' /> " . $country_name . "<br />";
        }
        if (!empty($timezone)) {
            $content .= "<strong>" . self::translate('timezone', $factor) . ":</strong> " . $timezone;
        }
//        if (!isset($_GET['pdf']) && empty($_GET['pdf'])) {
//            $idMap = "mapserverlocation";
//        } else {
//            $idMap = 'emptymap';
//        }
        if (isset($_GET['pdf'])) {
            $out .= '<img src="https://maps.googleapis.com/maps/api/staticmap?zoom=9&size=950x450&maptype=roadmap'
                    . '&markers=color:red%7Clabel:G%7C' . str_replace(",", ".", $latitude) . ',' . str_replace(",", ".", $longitude) . '" id="mapserverlocation">'
                    . $content;
        } else {
            $out .= '<div id="mapserverlocation" data-mapready="false" style="height: 450px;width: 100%;" data-serverlocation-title="' . $host
                    . '" data-serverlocation-accuracy="' . $accuracy_radius . '" data-serverlocation-latitude="' . str_replace(",", ".", $latitude)
                    . '" data-serverlocation-longitude="' . str_replace(",", ".", $longitude) . '" >' . $content . '</div>';
        }

        return !empty($data) ? $out : self::translate('servernotfound', $factor);
    }

    public static function guiGooglepreview($endModel, $data, $report, $factor) {
        $outString = '';
        if (isset($data) && !empty($data)) {
            foreach ($data as $key => $value) {
                if (strcasecmp($key, 'title') === 0) {
                    $title = $value;
                }

                if (strcasecmp($key, 'meta_description') === 0) {
                    $meta_description = $value;
                }
                if (strcasecmp($key, 'url_href') === 0) {
                    $url_href = parse_url($value);

                    if (strcasecmp($url_href['scheme'], 'http') === 0) {
                        $url_href = $url_href['host'];
                    } else {
                        $url_href = $value;
                    }
                }
            }
        }

        if (!empty($url_href) && !empty($title)) {
            $outString .= "<div class='outgooglepreview'>";
            $outString .= "<h3 class='title-googlepriview'>";
            $outString .= "<a rel='nofollow' href='" . (eRankerCommons::fixURL($url_href) !== false ? eRankerCommons::fixURL($url_href) : "http://$url_href") . "' target='_blank'> $title </a>";
            $outString .= "</h3>";
            $outString .= "<div class='insidegooglepreview'>";
            $outString .= "<div class='url-googlepreview'>";
            $outString .= "<a href='" . (eRankerCommons::fixURL($url_href) !== false ? eRankerCommons::fixURL($url_href) : "http://$url_href") . "' target='_blank' style='cursor:pointer;color: #006621;'>" . $url_href . "</a>";
            $outString .= "</div>";
            if (!empty($meta_description)) {
                $outString .= "<div class='description-googlepreview'>";
                $outString .= "$meta_description";
                $outString .= "</div>";
            }
            $outString .= "</div>";
            $outString .= "</div>";
        } else if ((!empty($url_href) && empty($title)) || (empty($url_href) && !empty($title))) {
            //for some sites $data not contain title
            //show partials data
            $outString .= "<div class='announcement'>" . self::translate('notcompletedata', $factor) . "</div>";
            $outString .= "<div class='outgooglepreview'>";

            if (!empty($url_href) && !empty($title)) {
                $outString .= "<h3 class='title-googlepriview'>";
                $outString .= "<a rel='nofollow' href='" . (eRankerCommons::fixURL($url_href) !== false ? eRankerCommons::fixURL($url_href) : "http://$url_href") . "' target='_blank'> $title </a>";
                $outString .= "</h3>";
            }

            $outString .= "<div class='insidegooglepreview'>";

            if (!empty($url_href)) {
                $outString .= "<div class='url-googlepreview'>";
                $outString .= "<a href='" . (eRankerCommons::fixURL($url_href) !== false ? eRankerCommons::fixURL($url_href) : "http://$url_href") . "' target='_blank' style='cursor:pointer;color: #006621;'>" . $url_href . "</a>";
                $outString .= "</div>";
            }

            if (!empty($meta_description)) {
                $outString .= "<div class='description-googlepreview'>";
                $outString .= "$meta_description";
                $outString .= "</div>";
            }
            $outString .= "</div>";
            $outString .= "</div>";
        }

        return !empty($outString) ? $outString : self::translate('notfoundgoogleprevie', $factor);
    }

    private static function helperResponsiveness($key, $data, $factor) {
        $out = '';
        if (isset($data[$key]) && !empty($data[$key])) {
            if (isset($data[$key]['preview']) && !empty($data[$key]['preview'])) {

                $color = (isset($data[$key]['pass']) && $data[$key]['pass']) ? "#04B974" : "#F00101";
                $icon = (isset($data[$key]['pass']) && $data[$key]['pass']) ? "fa-check" : "fa-times";

                //var_dump($data[$key]['preview']);
                $out .= "  <div class='responsivenesswrapper col-xs-12 col-sm-12 col-md-6 col-lg-6'>"
                        . "     <div class='responsivenesstop responsiveness$key'>"
                        . "         <img src='" . $data[$key]['preview'] . "' alt='Website Preview: $key' />"
                        . "         <i class='fa $icon' style='background-color: $color'></i>"
                        . "     </div>"
                        . "     <div class='responsivenessdetails'>"
                        . "         <div class='responsivenesslabel'>Browser:</div><div class='responsivenesslabelcontent'><img src='" . self::$imgfolder . "/icons/" . (strtolower(str_replace(' ', '', $data[$key]['browser']))) . ".png' alt='Browser Icon' /> " . (isset($data[$key]['browser']) ? $data[$key]['browser'] : "") . "</div>"
                        . "         <div class='responsivenesslabel'>OS:</div><div class='responsivenesslabelcontent'><img src='" . self::$imgfolder . "/icons/" . (strtolower(str_replace(' ', '', $data[$key]['os']))) . ".png' alt='OS Icon' /> " . (isset($data[$key]['os']) ? $data[$key]['os'] : "") . "</div>"
                        . "         <div class='responsivenesslabel'>Resolution:</div><div class='responsivenesslabelcontent'>" . $data[$key]['screen']["width"] . "x" . $data[$key]['screen']["height"] . "</div>"
                        . "         <div class='responsivenesslabel' title='Vertical Scrollbar'>V. Scrollbar:</div><div class='responsivenesslabelcontent'>" . ($data[$key]['scrollbar']["vertical"] ? "Yes" : "No") . "</div>"
                        . "         <div class='responsivenesslabel' title='Horizontal Scrollbar'>H. Scrollbar:</div><div class='responsivenesslabelcontent' style='" . ($data[$key]['scrollbar']["horizontal"] ? "color:" . $color : "") . "' >" . ($data[$key]['scrollbar']["horizontal"] ? "Yes" : "No") . "</div>"
                        . "         <div class='responsivenesslabel'>User Redirected:</div><div class='responsivenesslabelcontent'>" . ($data[$key]['redirected'] ? ("Yes - " . (isset($data[$key]['url']) ? $data[$key]['url'] : "")) : "No") . "</div>";
//                if (TRUE || (isset($data[$key]['redirected']) && $data[$key]['redirected'])) {
//                    $out .= "         <div class='responsivenesslabel'>Dst. URL:</div><div class='responsivenesslabelcontent' title='" . (isset($data[$key]['url']) ? $data[$key]['url'] : "") . "'>" . (isset($data[$key]['url']) ? $data[$key]['url'] : "") . "</div>";
//                }
                $out .= "      </div>"
                        . "</div>";
            }
        }
        return $out;
    }

    public static function guiResponsiveness($endModel, $data, $report, $factor) {
        if (empty($data)) {
            return eRankerCommons::guiDefault($endModel, $data, $report, $factor);
        }

        $out = '</div><div class="responsivenessfactor clearfix row" style="margin-top: 10px;">';
        $out .= eRankerCommons::helperResponsiveness("phone", $data, $factor);
        $out .= eRankerCommons::helperResponsiveness("tablet", $data, $factor);
        $out .= eRankerCommons::helperResponsiveness("notebook", $data, $factor);
        $out .= eRankerCommons::helperResponsiveness("desktop", $data, $factor);

        if (empty($out)) {
            return eRankerCommons::guiDefault($endModel, $data, $report, $factor);
        }

        $out .= '</div>';

        return $out;
    }

    public static function guiDuplicatecontent($endModel, $data, $report, $factor) {

        $outString = '';
        $urlsString = '';
        if (isset($data) && !empty($data)) {
            $outString = '' . self::translate("wefound", $factor) . ' <strong>' . count($data) . ' </strong> ' . self::translate("websitecontent", $factor) . '<hr></hr>';
            foreach ($data as $value) {
                $title = $value;
                if (!empty($title)) {
                    $urlsString .= "<li> $title <br /> </li>";
                }
            }
            $outString .= "<ul>$urlsString</ul>";
        }

        return !empty($outString) ? $outString : self::translate('notfindcontent', $factor);
    }

    public static function guitechnologies($endModel, $data, $report, $factor) {

        //technologies
        $technologies1 = array('Google Postini Services', 'Time Warner', 'Yahoo Web Analytics', 'Network Solutions DNS', 'Network Solutions SSL Wildcard', 'Adobe Dreamweaver',
            'Google Adsense Asynchronous', 'Reg.ru DNS', 'qTranslate', 'Explorer Canvas', 'JBoss', 'ATInternet', 'Lunar Pages', 'Amazon Elastic Load Balancing', 'GlobalSign', 'Verizon DNS',
            'Oracle Application Server', 'Symantec.cloud', 'Akamai DNS', 'Akamai SSL', 'jQuery Autocomplete', 'Joomla!', 'Level 3 Communications', 'Websense', 'ATT DNS', 'Comodo EliteSSL',
            'GeoTrust QuickSSL Premium', 'Google App Engine', 'Hostway', 'Mailgun', 'Return Path', 'Proofpoint', 'Netscape Enterprise Server', 'Namecheap', 'Namecheap DNS', 'Linode', 'Adap.TV',
            'Adblade', 'Add to Any', 'AddThis', 'Adobe ColdFusion', 'Adobe CQ', 'Adobe Dynamic Tag Management', 'Adobe Target Standard', 'Adobe', 'Adometry', 'AdRoll', 'Aggregate Knowledge',
            'AJAX Libraries API', 'Akamai Edge', 'Akamai Hosted', 'Akamai', 'Alexa Certified Site Metrics', 'Alexa Metrics', 'Amazon Ad System', 'Amazon Associates', 'Amazon Elastic Beanstalk',
            'Amazon Oregon Region', 'Amazon Route 53', 'Amazon S3', 'Amazon SES', 'Amazon Virginia Region', 'Amazon', 'Angular JS', 'Apache 2.2', 'Apache 2.4', 'Apache Tomcat Coyote', 'Apache',
            'Apple Mobile Web App Capable', 'Apple Mobile Web App Status Bar Style', 'Apple Mobile Web Clips Icon', 'Apple Mobile Web Clips Startup', 'AppNexus Segment Pixel', 'AppNexus',
            'ASP.NET', 'AT Internet', 'Atlas', 'aWeber', 'Backbone.js', 'BBC Glow', 'BIG-IP', 'Bing Conversion Tracking', 'Bing Universal Event Tracking', 'BloomReach', 'Blue Box Group', 'Blue State Digital',
            'BlueKai', 'Bootstrap Sortable', 'Braintree Mail', 'Burst Media', 'Campaign Monitor', 'Canada Post', 'carouFredSel', 'Casale Media', 'CDN JS', 'Cedexis', 'Certona', 'Chango', 'ChannelAdvisor',
            'Choopa', 'Classic ASP', 'CloudFlare DNS', 'CloudFlare Hosting', 'CloudFlare SSL', 'CloudFront', 'Commission Junction', 'Comodo Essential SSL WildCard', 'Comodo PositiveSSL Wildcard',
            'Comodo PositiveSSL', 'Comodo SSL', 'comScore', 'Constant Contact', 'Contact Form 7', 'ContextWeb', 'Conversant', 'Convertro', 'CrazyEgg', 'Criteo', 'cufón', 'Datalogix', 'DataXu', 'Dedicated Media',
            'Device Height', 'Device Width', 'Didit', 'Digg', 'DigiCert SSL', 'DNS Made Easy DNS', 'DNS Prefetch', 'DOSarrest', 'dotCMS', 'Dotomi', 'DoubleClick Floodlight', 'DoubleClick.Net',
            'DoubleVerify', 'Dreamhost DNS', 'DreamHost Hosting', 'Drupal 7', 'Drupal Version 7.3x', 'Drupal', 'Dyn DNS', 'Dyn', 'Dynatrace', 'Efficient Frontier', 'Eloqua', 'Emarsys', 'EPiServer',
            'Equal Heights', 'eranker', 'EssentialSSL', 'Everest Technologies', 'Evidon', 'ExactTarget Email', 'ExpressionEngine', 'Facebook Custom Audiences', 'Facebook Domain Insights', 'Facebook Exchange FBX',
            'Facebook for Websites', 'Facebook Like Box', 'Facebook Like Button', 'Facebook Like', 'Facebook Page Administration', 'Facebook SDK', 'Facebook', 'Fancybox', 'FastClick', 'Fastly',
            'FedEx', 'Fingerprint', 'Flashtalking', 'Flattr', 'FlexSlider', 'Font Awesome', 'ForeSee Results', 'Friends Network', 'GeoTrust QuickSSL', 'GeoTrust SSL', 'GetResponse', 'GitHub Hosting', 'GoDaddy DNS',
            'GoDaddy Email', 'GoDaddy SSL', 'GoDaddy', 'Google Analytics Ecommerce', 'Google Analytics Multiple Trackers', 'Google Analytics', 'Google API', 'Google Apps for Business', 'Google Chrome IE Frame', 'Google Chrome Webstore Application',
            'Google Conversion Tracking', 'Google DNS', 'Google Font API', 'Google Hosted jQuery UI'
        );

        $technologies2 = array('Google Hosted jQuery', 'Google Hosted Libraries', 'Google JS Api', 'Google Maps API', 'Google Maps',
            'Google Plus One Button', 'Google Plus One Platform', 'Google Plus One Publisher', 'Google Remarketing', 'Google SSL', 'Google Universal Analytics', 'Google Website Optimizer', 'Google', 'GSAP',
            'Handheld Friendly', 'Hetzner', 'Highcharts', 'HostEurope DNS', 'Hostgator Mail', 'HREF Lang', 'HTML5 Boilerplate', 'html5shiv', 'Humans TXT', 'IBM HTTP Server', 'IE Pinning', 'IIS 6', 'IIS 7',
            'IIS 8', 'IIS', 'Imgur', 'Impact Radius', 'Incapsula CDN', 'Incapsula', 'InsightExpress', 'Integral Ad Science', 'Intercom Mail', 'Internap', 'IPhone  Mobile Compatible', 'IponWeb BidSwitch', 'Isotope',
            'J2EE', 'jQuery 1.3.2', 'jQuery CDN', 'jQuery Cookie', 'jQuery Cycle', 'jQuery Form', 'jQuery Mousewheel', 'jQuery prettyPhoto', 'jQuery UI', 'jQuery Watermark', 'jQuery', 'Kenshoo', 'KISSmetrics',
            'KnockoutJS', 'Level3', 'Lijit Widget', 'LinkedIn Platform API', 'Liquid Web', 'LiteSpeed', 'Live Writer Support', 'Livestream', 'LocaWeb DNS', 'Locaweb Mail', 'Locaweb SSL', 'Lotame Crowd Control', 'MailChimp SPF',
            'MailChimp', 'MailJet', 'Mandrill', 'matchMedia', 'Maxymiser', 'McAfee SaaS Email', 'MediaMind', 'Mediaplex', 'Message Bus', 'Microdata for Google Shopping', 'Microsoft Ajax Content Delivery Network', 'Microsoft Azure CDN',
            'Microsoft Azure DNS', 'Microsoft Exchange Online', 'Microsoft Personal Web Server', 'Microsoft SharePoint Server 2013', 'Microsoft', 'MidPhase', 'Mixpanel', 'Moat', 'Mobify', 'Mobile Non Scaleable Content',
            'Mobile Optimized', 'Modernizr', 'Moment JS', 'Monetate', 'Mustache', 'Netmining', 'Network Solutions Email Hosting', 'New Relic', 'nginx 1.1', 'nginx', 'Ning', 'NTT America', 'Omniture Adobe Test and Target',
            'Omniture SiteCatalyst', 'One.com', 'OneAll', 'Open Graph Protocol', 'Openads OpenX', 'OpenSSL', 'Optimize Press', 'Orientation', 'OVH', 'OwnerIQ', 'Perl', 'PHP', 'Phusion Passenger', 'Pinterest',
            'pjax', 'Post Affiliate Pro', 'PowWeb', 'Prototype', 'Pubmatic', 'Qualtrics Site Intercept', 'Quantcast Measurement', 'RapidSSL', 'Rapleaf', 'Really Simple Discovery', 'reCAPTCHA', 'Register.com DNS',
            'RequireJS', 'Resolution', 'Retina JS', 'Rubicon Project', 'Ruby on Rails Token', 'Ruby on Rails', 'Safe Count', 'Salesforce SPF', 'Satellite', 'Savvis', 'script.aculo.us', 'Search Everything', 'Sendgrid', 'Shareaholic',
            'ShareASale', 'ShareThis', 'Shockwave Flash Embed', 'ShopTab', 'Sidecar', 'Sitelinks Search Box', 'Smart App Banner', 'SPF', 'Spotify Play Button', 'SpotXchange', 'Starfield Technologies', 'StatCounter',
            'Symantec VeriSign', 'TeaLeaf', 'Tealium', 'Thawte Seal', 'Thawte SSL Certificate', 'Thawte SSL', 'The Trade Desk', 'TownNews.com', 'TRUSTe', 'Trustwave Seal', 'Trustwave SSL', 'Tumblr Buttons', 'Turn', 'Twemoji',
            'Twenty Twelve', 'Twitter Bootstrap', 'Twitter Cards', 'Twitter Follow Button', 'Twitter Platform', 'Twitter Timeline', 'Typekit', 'Ubuntu', 'UltraDNS neustar', 'UPS', 'USPS', 'Varnish', 'VideoJS', 'Viewport Meta', 'Vimeo',
            'VINDICO', 'Visual Revenue', 'VoiceFive', 'W3 Total Cache', 'WebTrends', 'Windows 8 Pinning', 'Wistia', 'WordPress 4.0', 'Wordpress 4.2', 'Wordpress Daily Activity', 'WordPress DNS', 'Wordpress Monthly Activity', 'Wordpress SSL',
            'WordPress Weekly Activity', 'WordPress', 'World Now', 'WP Retina 2x', 'X-Frame-Options', 'XiTi', 'X-UA-Compatible', 'X-XSS-Protection', 'Yahoo Buzz', 'Yahoo Dot', 'Yahoo Image CDN', 'Yahoo Small Business', 'Yahoo User Interface', 'Yahoo',
            'yepnope', 'Yield Manager', 'Yoast Plugins', 'Yoast WordPress SEO Plugin', 'YouTube', 'YUI3', 'Zendesk', 'ZeroClipboard', 'Zerolag'
        );
        //all technologies with image in technologies folder

        $html = '<p>' . self::translate("foundtechnologies", $factor) . '</p>';
        $html .='<br />'
                . '<div class="row">';

        if (!empty($data)) {
            foreach ($data as $singleTec) {
                $imglink = '';

                if (in_array($singleTec, $technologies1) || in_array($singleTec, $technologies2)) {
                    $imglink .= $singleTec . '.png';
                } else {
                    $imglink .= 'eranker.png';
                }

                $tagimg = '';

                if (!empty($imglink)) {
                    $tagimg .= '<img src="' . self::$imgfolder . 'technologies/' . $imglink . '"  height="24" width="24">';
                }

                $html .='<div class=" col-xs-12 col-sm-6 col-md-4 col-lg-4 nopaddingleft paddingupdown"> ' . $tagimg . ' ' . $singleTec . '</div>';

                $tagimg = '';
            }
        } else {
            $html .= '<div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft"> ' . $endModel . '</div>';
        }

        $html .= '</div>';

        return !empty($html) ? $html : self::translate('notfoundduplicatecontent', $factor);
    }

    public static function guiSslcheck($endModel, $data, $report, $factor) {
        $html = "";

        if ((isset($data) && !empty($data['trusted']) && $data['trusted']) || (isset($data['return_error']) && $data['return_error'] === "ok")) {
            $html .= "<h4><i class='fa fa-check-circle green'></i> " . self::translate('validssl', $factor) . "</h4>";
        } else if (($data != null && (isset($data['return_error']) && $data['return_error'] !== "ok"))) {
            $html .= "<h4><i class='fa fa-info-circle missing'></i> " . self::translate('invalidsll', $factor) . "</h4>";
            $html .= "<span>" . ucfirst($data['return_error']) . "</span>";
        }

        if (isset($data['common_name']) && !empty($data['common_name'])) {
            $html .= "<div class='row'>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('commonname', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['common_name'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if (isset($data['organizational_unit']) && !empty($data['organizational_unit'])) {
            $html .= "<div class='row'>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('organizational', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['organizational_unit'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if (isset($data['country']) && !empty($data['country'])) {
            $html .= "<div class='row'>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('countrysslcheck', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['country'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if (isset($data['state']) && !empty($data['state'])) {
            $html .= "<div class='row'>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('statesslcheck', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['state'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if (isset($data['locality']) && !empty($data['locality'])) {
            $html .= "<div class='row'>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('locality', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['locality'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if (isset($data['issuer_name']) && !empty($data['issuer_name'])) {
            $html .= "<div class='row'>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('issuername', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['issuer_name'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if (isset($data['issuer_url']) && !empty($data['issuer_url'])) {
            $html .= "<div class='row'>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('issuerurl', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['issuer_url'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if (isset($data['key_strength']) && !empty($data['key_strength'])) {
            $html .= "<div class='row'>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('keystrength', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['key_strength'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if (isset($data['protocol']) && !empty($data['protocol'])) {
            $html .= "<div class='row'>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('protocol', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['protocol'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if ($data === null) {
            $html .= "<h4><i class='fa fa-times missing'></i> " . self::translate('missingssl', $factor) . "</h4>";
        }

        return $html;
    }

    public static function guiHttp2check($endModel, $data, $report, $factor) {
        $html = "";

        if (isset($data) && isset($data['valid']) && !empty($data['valid'])) {
            $html .= "<h4><i class='fa fa-check-circle green'></i> " . self::translate('valid', $factor) . "</h4>";
        } else if (isset($data) && isset($data['valid']) && empty($data['valid'])) {
            $html .= "<h4><i style='color:#ED1111 !important;' class='fa fa-info-circle missing'></i> " . self::translate('invalid', $factor) . "</h4>";
        }

        if (isset($data['http2_status']) && !empty($data['http2_status'])) {
            $html .= "<div class='row '>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('http2status', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['http2_status'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }
        
        if (isset($data['ssl'])) {
            $html .= "<div class='row '>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>SSL Enabled</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . (empty($data['ssl']) ? '<i style="color:#ED1111 !important;" class="fa fa-times-circle missing"></i>' : '<i class="fa fa-check-square-o green"></i>') . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }
     
        if (isset($data['ssl_status']) && !empty($data['ssl_status'])) {
            $html .= "<div class='row '>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('sslstatus', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['ssl_status'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }
        
        if (isset($data['ssl_certificate_valid'])) {
            $html .= "<div class='row '>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('certificate', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . (empty($data['ssl_certificate_valid']) ? '<i style="color:#ED1111 !important;" class="fa fa-times-circle missing"></i>' : '<i class="fa fa-check-square-o green"></i>') . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }


        if (isset($data['content-length']) && !empty($data['content-length'])) {
            $html .= "<div class='row '>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('total_length', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['content-length'] . " Bytes</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if (isset($data['server']) && !empty($data['server'])) {
            $html .= "<div class='row '>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('server', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['server'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }
        $statusString = '';
        $colorStatus = '';


        $text = 'Unknown http status code';
        if (isset($data['status_code']) && !empty($data['status_code'])) {

            if ($data['status_code'] <= 299) {
                $colorStatus = "style='color:green;'";
            }
            if ($data['status_code'] >= 299) {
                $colorStatus = "style='color:#E7711B;'";
            }
            if ($data['status_code'] >= 399) {
                $colorStatus = "style='color:#ED1111;'";
            }

            switch ($data['status_code']) {
                case 100: $text = 'Continue';
                    break;
                case 101: $text = 'Switching Protocols';
                    break;
                case 200: $text = 'OK';
                    break;
                case 201: $text = 'Created';
                    break;
                case 202: $text = 'Accepted';
                    break;
                case 203: $text = 'Non-Authoritative Information';
                    break;
                case 204: $text = 'No Content';
                    break;
                case 205: $text = 'Reset Content';
                    break;
                case 206: $text = 'Partial Content';
                    break;
                case 300: $text = 'Multiple Choices';
                    break;
                case 301: $text = 'Moved Permanently';
                    break;
                case 302: $text = 'Moved Temporarily';
                    break;
                case 303: $text = 'See Other';
                    break;
                case 304: $text = 'Not Modified';
                    break;
                case 305: $text = 'Use Proxy';
                    break;
                case 400: $text = 'Bad Request';
                    break;
                case 401: $text = 'Unauthorized';
                    break;
                case 402: $text = 'Payment Required';
                    break;
                case 403: $text = 'Forbidden';
                    break;
                case 404: $text = 'Not Found';
                    break;
                case 405: $text = 'Method Not Allowed';
                    break;
                case 406: $text = 'Not Acceptable';
                    break;
                case 407: $text = 'Proxy Authentication Required';
                    break;
                case 408: $text = 'Request Time-out';
                    break;
                case 409: $text = 'Conflict';
                    break;
                case 410: $text = 'Gone';
                    break;
                case 411: $text = 'Length Required';
                    break;
                case 412: $text = 'Precondition Failed';
                    break;
                case 413: $text = 'Request Entity Too Large';
                    break;
                case 414: $text = 'Request-URI Too Large';
                    break;
                case 415: $text = 'Unsupported Media Type';
                    break;
                case 500: $text = 'Internal Server Error';
                    break;
                case 501: $text = 'Not Implemented';
                    break;
                case 502: $text = 'Bad Gateway';
                    break;
                case 503: $text = 'Service Unavailable';
                    break;
                case 504: $text = 'Gateway Time-out';
                    break;
                case 505: $text = 'HTTP Version not supported';
                    break;
                default: $text = 'Unknown http status code';
                    break;
            }
        }




        if (isset($data['status_code']) && !empty($data['status_code'])) {
            $html .= "<div class='row '>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('status', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span $colorStatus>" . $data['status_code'] . " - " . $text . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }


        if (isset($data['url']) && !empty($data['url'])) {
            $html .= "<div class='row '>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>URL</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span><a href='" . $data['url'] . "' target='_blank'>" . $data['url'] . "</a></span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if (isset($data['protocol']) && !empty($data['protocol'])) {
            $html .= "<div class='row '>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('protocol', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['protocol'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if (isset($data['host_name']) && !empty($data['host_name'])) {
            $html .= "<div class='row '>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('host_name', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['host_name'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if (isset($data['content_type']) && !empty($data['content_type'])) {
            $html .= "<div class='row '>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('content_type', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['content_type'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }


        if (isset($data['client_real_ip']) && !empty($data['client_real_ip'])) {
            $html .= "<div class='row'>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('client_real_ip', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['client_real_ip'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if (isset($data['client_ip']) && !empty($data['client_ip'])) {
            $html .= "<div class='row'>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('client_ip', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['client_ip'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if (isset($data['host_ip']) && !empty($data['host_ip'])) {
            $html .= "<div class='row'>";
            $html .= "<div class='col-md-4'>";
            $html .= "<span><strong>" . self::translate('host_ip', $factor) . "</strong></span>";
            $html .= "</div>";
            $html .= "<div class='col-md-8'>";
            $html .= "<span>" . $data['host_ip'] . "</span>";
            $html .= "</div>";
            $html .= "</div>";
        }

        if ($data === null) {
            $html .= "<h4><i class='fa fa-times missing'></i> " . self::translate('model_red', $factor) . "</h4>";
        }

        return $html;
    }

    public static function guiSpeedanalysis($model, $data, $report, $factor) {
        if (empty($data)) {
            return $model;
        }
        if (!isset($data['grades']) || empty($data['grades'])) {
            return self::translate('Speed Anlysis failed to run');
        }
        $factors_labels = array(
            'numreq' => self::translate('numreq', $factor),
            'expires' => self::translate('expires', $factor),
            'jsbottom' => self::translate('jsbottom', $factor),
            'xhr' => self::translate('xhr', $factor),
            'compress' => self::translate('compress', $factor),
            'favicon' => self::translate('favicon', $factor),
            'csstop' => self::translate('csstop', $factor),
            'dns' => self::translate('dns', $factor),
            'mindom' => self::translate('mindom', $factor),
            'cdn' => self::translate('cdn', $factor),
            'cookiefree' => self::translate('cookiefree', $factor),
            'emptysrc' => self::translate('emptysrc', $factor),
            'imgnoscale' => self::translate('imgnoscale', $factor),
            'redirects' => self::translate('redirects', $factor),
            'dupes' => self::translate('dupes', $factor),
            'no404' => self::translate('no404', $factor),
            'xhrmethod' => self::translate('xhrmethod', $factor),
            'mincookie' => self::translate('mincookie', $factor),
            'etags' => self::translate('etags', $factor),
        );


        $statsNames = array(
            'doc' => 'HTML',
            'js' => 'JavaScript',
            'css' => 'CSS',
            'image' => 'Image',
            'json' => 'Json',
            'redirect' => 'Redirect'
        );

        //style='width: 50%; height: 300px;margin: 0 auto; float: left;'
        $html = "
            
            <h4 class='marginbottom0'>" . self::translate('overallscore', $factor) . ": <b>" . $data['score'] . "</b> " . self::translate('outof', $factor) . " 100</h4>
            <p>" . self::translate('pagetotalof', $factor) . " <b>" . $data['requests'] . "</b> " . self::translate('httprequest', $factor) . " <b>" . round($data['size'] / 1024) . "Kb</b> " . self::translate('withemptycache', $factor) . "</p>

            <div class='row' id='speedanalysispiegroup'>
                <div id='speedanalysispiechartsrequest' class='col-xs-12 col-sm-12 col-md-6 col-lg-6 nopaddingleft' data-chartready='false'></div>
                <div id='speedanalysispiechartsweight' class='col-xs-12 col-sm-12 col-md-6 col-lg-6 nopaddingleft' data-chartready='false'></div>
            </div><!-- #speedanalysispiegroup -->
            
            <script type='text/javascript'>

                function speedanalysispiechartsweight() {
                
                jQuery('#speedanalysispiechartsweight[data-chartready=\"false\"]').highcharts({
                        chart: {
                            animation: false,
                            plotBackgroundColor: 'transparent',
                            plotBorderWidth: null,
                            plotShadow: false,
                            backgroundColor: 'transparent'
                        },
                        title: {
                            text: 'Requests Size',
                            margin: 0
                        },
                        colors: ['#FF9000', '#0281C4', '#04B974',  '#F45B5B', '#444444', '#5F65E0'],
                        tooltip: {
                            pointFormat: '{series.name}: <b>{point.y}Kb ({point.percentage:.1f}%)</b>'
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            layout: 'vertical',
                            align: 'right',
                            verticalAlign: 'bottom',                            
                            enabled: false
                        },
                        exporting:{
                            enabled: false
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b>: {point.y}Kb',
                                    color: 'white',
                                    distance: 20,
                                    color: 'black'
                                }
                            }
                        },
                        series: [{
                            type: 'pie',
                            name: 'Request Size',
                            showInLegend: true,
                            data: [";

        $countStats = 0;
        foreach ($statsNames as $statKey => $statName) {
            if (!isset($data['stats']) || !isset($data['stats'][$statKey]) || !isset($data['stats'][$statKey]['w'])) {
                continue;
            }
            $statValue = max(0, round($data['stats'][$statKey]['w'] / 1024));
            if ($statValue == 0) {
                continue;
            }
            if ($countStats > 0) {
                $html .= ",";
            }

            $html .= "{name: '$statName', y: " . $statValue . ", sliced: " . ($countStats > 0 ? "false" : "true") . ", selected: " . ($countStats > 0 ? "false" : "true") . " }";
            $countStats++;
        }

        $html .= "
                            ]
                        }]
                    });  
                    
                     jQuery('#speedanalysispiechartsweight').attr('data-chartready', 'true');
                }

                function speedanalysispiechartsrequest() {
                    jQuery('#speedanalysispiechartsrequest[data-chartready=\"false\"]').highcharts({
                        chart: {
                            animation: false,
                            plotBackgroundColor: 'transparent',
                            plotBorderWidth: null,
                            plotShadow: false,
                            backgroundColor: 'transparent'
                        },
                        title: {
                            text: 'HTTP Requests',
                            margin: 0
                        },
                        colors: ['#FF9000', '#0281C4', '#04B974',  '#F45B5B', '#444444', '#5F65E0'],
                        tooltip: {
                            pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            layout: 'vertical',
                            align: 'right',
                            verticalAlign: 'bottom',                            
                            enabled: false
                        },
                        exporting:{
                            enabled: false
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b>: {point.y}',
                                    color: 'white',
                                    distance: 20,
                                    color: 'black'
                                }
                            }
                        },
                        series: [{
                            type: 'pie',
                            name: 'HTTP Requests',
                            showInLegend: true,
                            data: [";

        $countStats = 0;
        foreach ($statsNames as $statKey => $statName) {
            if (!isset($data['stats']) || !isset($data['stats'][$statKey]) || !isset($data['stats'][$statKey]['r'])) {
                continue;
            }
            $statValue = max(0, round($data['stats'][$statKey]['r']));
            if ($statValue == 0) {
                continue;
            }
            if ($countStats > 0) {
                $html .= ",";
            }

            $html .= "{name: '$statName', y: " . $statValue . ", sliced: " . ($countStats > 0 ? "false" : "true") . ", selected: " . ($countStats > 0 ? "false" : "true") . " }";
            $countStats++;
        }

        $html .= "
                            ]
                        }]
                    });  
                     jQuery('#speedanalysispiechartsrequest').attr('data-chartready', 'true');
                }
             </script>";







        foreach ($data['grades'] as $label => $grade) {
            $invgrade = min(98, max(0, 100 - $grade));  //grade is inversed


            if ($grade < 31) {
                $color = "#FE0000";
                $icon = 'fa-times';
            } else {
                if ($grade < 71) {
                    $icon = 'fa-minus';
                    $color = "#FF9000";
                } else {
                    $icon = 'fa-check';
                    $color = "#04B974";
                }
            }

            $html .= '<div class="row">';
            $html .= '<div class="col-sm-12 col-md-5 speed-label">' . $factors_labels[$label] . '</div>'
                    . '<div class="col-sm-12 col-md-7 speed-progress">' . '<i class="fa ' . $icon . '" style="background-color: ' . $color . '"></i>'
                    . '<div class="progress-wrapper" style="background-color: ' . $color . '"><div class="load-progress-grade" style="width:' . $invgrade . '%">&nbsp;</div></div>'
                    . '<small>' . $grade . '%</small></div>';
            //. '<div class="col can-float speed-grade" style="color: ' . $color . '" >' . $grade . '%</div>';
            $html .= '<div class="clearfix"></div>'
                    . '</div>';
        }
        return $html;
    }

    public static function guiHtmlvalidity($endModel, $data, $report, $factor) {
        $out = '<div class="row">';

        if (!empty($data)) {
            if (!empty($data['error'])) {
                $out .= '<div class="col-xs-12 col-sm-3 col-md-1 col-lg-2 nopaddingleft">' . html_entity_decode(self::translate("error_text", $factor)) . '</div>'
                        . '<div class="col-xs-12 col-sm-9 col-md-11 col-lg-10 nopaddingleft">' . $data['error'] . '<br /></div>';
            }

            if (!empty($data['warning'])) {
                $out .= '<div class="col-xs-12 col-sm-3 col-md-1 col-lg-2 nopaddingleft">' . html_entity_decode(self::translate("warning_text", $factor)) . '</div>'
                        . '<div class="col-xs-12 col-sm-9 col-md-11 col-lg-10 nopaddingleft">' . $data['warning'] . '<br /></div>';
            }

            if (!empty($data['info'])) {
                $out .= '<div class="col-xs-12 col-sm-3 col-md-1 col-lg-2 nopaddingleft">' . html_entity_decode(self::translate("info_text", $factor)) . '</div>'
                        . '<div class="col-xs-12 col-sm-9 col-md-11 col-lg-10 nopaddingleft">' . $data['info'] . '<br /></div>';
            }

            if (!empty($data['url'])) {
                $z = stripslashes(html_entity_decode(str_replace('%link_text', eRankerCommons::fixURL($data['url']) != false ? eRankerCommons::fixURL($data['url']) : $data['url'], self::translate("link_text", $factor))));
                $q = explode('<a href', $z);
                $a1 = $q[0];
                $a2 = isset($q[1]) ? '<a href' . $q[1] : '';
//                $out .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingleft">'
//                            .stripslashes(html_entity_decode(str_replace('%link_text', eRankerCommons::fixURL($data['url']) != false ? eRankerCommons::fixURL($data['url']) : $data['url'], self::translate("link_text", $factor))))
//                        .'</div>';
                $out .= '<div class="col-xs-12 col-sm-3 col-md-1 col-lg-2 nopaddingleft">' . $a1 . '</div>'
                        . '<div class="col-xs-12 col-sm-9 col-md-11 col-lg-10 nopaddingleft">' . $a2 . '</div>';
            }
        }

        $out .= '</div>';

        return (!empty($data)) ? $out : $endModel;
    }

    public static function guiSitemap($endModel, $data, $report, $factor) {
        $out = '';

        if (!empty($data)) {
            if ($data['status']) {
                if (!isset($_COOKIE['detectedLanguage']) || (isset($_COOKIE['detectedLanguage']) && $_COOKIE['detectedLanguage'] === "en")) {
                    $out .= self::translate("model_green", $factor) . '<br /><br />';
                } else if (isset($_COOKIE['detectedLanguage']) && $_COOKIE['detectedLanguage'] !== "en") {
                    $out .= '<span>' . (is_null($endModel) ? $data['status'] : $endModel) . '</span><br /><br />';
                } else {
                    $out .= '<span>' . (is_null($endModel) ? $data['status'] : $endModel) . '</span><br /><br />';
                }

                $out .= '<div class="trickydiv"><ul class="sitemaptoggle sitemaptoggledown">';

                $count = 0;

                foreach ($data['sitemap'] as $value) {
                    $count ++;

                    if ($count == 5) {
                        $out .= '<li class="lastnotoggle">';
                    } else {
                        $out .= '<li>';
                    }

                    $out .= '<a href="' . (eRankerCommons::fixURL($value) !== false ? eRankerCommons::fixURL($value) : $value) . '" target="_blank">' . $value . '</a>';
                    $out .= '</li>';
                }

                $out .= '</ul></div>';

                if ($count > 5) {
                    $out .= '<a class="showmoresitemap" href="javascript:void(0);" onclick="if(jQuery(\'.sitemaptoggle\').hasClass(\'sitemaptoggledown\')){sitemapToggle(\' Show less\');}else if(jQuery(\'.sitemaptoggle\').hasClass(\'sitemaptoggleup\')){sitemapToggle(\' Show more\')}">'
                            . 'Show more</a>';
                }
            } else {
                $out .= self::translate("model_neutral", $factor);
            }
        } else {
            $out .= self::translate("model_red", $factor);
        }

        return $out;
    }

    public static function guiImgemptyalt($endModel, $data, $report, $factor) {
        $out = '';
        if (!empty($data)) {

            if (!empty($data['total'])) {
                $out .= '<div>' . str_replace('%total', $data['total'], eRankerCommons::translate("model_orange", $factor)) . '</div>';

                $url_href = '';
                foreach ($data['image'] as $value) {
                    if (strpos($value, "://")) {
                        $domain = explode('/', $value);
                        $url_href .= $domain[2];
                        break;
                    }
                }

                if ($url_href === '') {
                    $url_href = $report->url;
                }

                $out .= '<div class="trickydiv"><ul style="text-overflow: ellipsis;white-space: nowrap; max-width: 90%;" class="imgalttoggle imgalttoggledown">';

                if (!empty($data['image'])) {
                    $count = 0;
                    foreach ($data['image'] as $value) {
                        $count ++;
                        if ($count == 5) {
                            $out .= '<li class="lastnotoggle">';
                        } else {
                            $out .= '<li>';
                        }

                        if (strpos($value, "://") === false) {
                            $url_href = rtrim($url_href, '/');
                            $value = ltrim($value, '/');
                            $url = $url_href . '/' . $value;
                        } else {
                            $url = $value;
                        }

                        $out .= '<a href="' . (eRankerCommons::fixURL($url) !== false ? eRankerCommons::fixURL($url) : $url) . '" target="_blank">' . $value . '</a>';
                        $out .= '</li>';
                    }
                }

                $out .= '</ul></div>';

                if ($count > 5) {
                    $out .= '<a class="showmoreimgalt" href="javascript:void(0);" onclick="if(jQuery(\'.imgalttoggle\').hasClass(\'imgalttoggledown\')){imgAltToggle(\' Show less\');}else if(jQuery(\'.imgalttoggle\').hasClass(\'imgalttoggleup\')){imgAltToggle(\' Show more\')}">'
                            . 'Show more</a>';
                }
            } else {
                $out .= $endModel;
            }
        }

        return ((!empty($out) && !isset($_COOKIE['detectedLanguage'])) || (isset($_COOKIE['detectedLanguage']) && $_COOKIE['detectedLanguage'] === "en")) ? $out : $endModel;
    }

    public static function guiCitations($endModel, $data, $report, $factor) {
        $out = '';
        if (!empty($data)) {
            if (!empty($data['citations'])) {
                $out .= '<div>' . $endModel . '</div>';
                if (!empty($data['link'])) {
                    $out .= '<div style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden; max-width: 90%;"><a href="' . $data['link'] . '" target="_blank">' . $data['link'] . '</a></div>';
                }
            } else {
                $out = '<div>' . $endModel . '</div>';
            }
        }

        return (!empty($out)) ? $out : $endModel;
    }

    public static function guiUrl($endModel, $data, $report, $factor) {
        $out = '';

        if (!empty($data)) {

            $out .= '<a href="' . $data . '" target="_blank">' . $data . '</a>';
        } else {
            $out = $endModel;
        }


        return $out;
    }

}
