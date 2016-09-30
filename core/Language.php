<?php

/* * ********************************************************************************
 * (c) 2011-15 GÉANT on behalf of the GN3, GN3plus and GN4 consortia
 * License: see the LICENSE file in the root directory
 * ********************************************************************************* */
?>
<?php

/**
 * 
 * 
 *  This is the definition of the CAT class
 * @author Stefan Winter <stefan.winter@restena.lu>
 * @author Tomasz Wolniewicz <twoln@umk.pl>
 *
 * @package Developer
 */
/**
 * necessary includes
 */
require_once("Logging.php");
require_once(dirname(__DIR__) . "/config/_config.php");

/**
 * Define some variables which need to be globally accessible
 * and some general purpose methods
 *
 * @author Stefan Winter <stefan.winter@restena.lu>
 * @author Tomasz Wolniewicz <twoln@umk.pl>
 *
 * @package Developer
 */
class Language {
    
    /**
     * 
     * @var string
     */
    private $LANG = '';
    
    /**
     * language display name for the language set by the constructor
     */
    public $locale;

    /**
     *  Constructor sets the language by calling set_lang 
     *  and stores language settings in object properties
     *  additionally it also sets static variables $laing_index and $root
     */
    public function __construct() {
        $language = $this->setLang();
        $this->LANG = $language[0];
        $this->locale = $language[1];
    }

    /**
     * Sets the gettext domain
     *
     * @param string $domain
     * @return string previous seting so that you can restore it later
     */
    public function setTextDomain($domain) {
        $loggerInstance = new Logging();
        $olddomain = textdomain(NULL);
        $loggerInstance->debug(4, "set_locale($domain)\n");
        $loggerInstance->debug(4, ROOT . "\n");
        textdomain($domain);
        bindtextdomain($domain, ROOT . "/translation/");
        return $olddomain;
    }

    /**
     * set_lang does all language setting magic
     * checks if lang has been declared in the http call
     * if not, checks for saved lang in the SESSION
     * or finally checks browser properties.
     * Only one of the supported langiages can be set
     * if a match is not found, the default langiage is used
     * @param $hardsetlang - this is currently not used but
     * will allow to forst lang setting if this was ever required
     */
    private function setLang($hardSetLang = 0) {
        // $langConverted will contain candidates for the language setting in the order
        // of prefference
        $langConverted = [];
        if ($hardSetLang !== 0) {
            $langConverted[] = $hardSetLang;
        } 
        if (! empty($_REQUEST['lang'])) {
            $langConverted[] = $_REQUEST['lang'];
        }
        if (! empty($_SESSION['language'])) {
            $langConverted[] = $_SESSION['language'];
        }
        if (! empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $langs = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
            foreach ($langs as $lang) {
                $result = [];
                preg_match("/(.*);+.*/", $lang, $result);
                $langConverted[] = (empty($result[1]) ? $lang : $result[1]);
            }
        }
        $langIndex = CONFIG['APPEARANCE']['defaultlocale'];
        $theLocale = CONFIG['LANGUAGES'][$langIndex]['locale'];
        // always add configured default language as the last resort
        $langConverted[] = $langIndex;
        setlocale(LC_ALL, 0);
        foreach ($langConverted as $tryLang) {
            // madness! setlocale is completely unflexible. If $tryLang is "en"
            // it will fail, because it only knows en_US, en_GB a.s.o.
            // we need to map stuff manually
            $localeTmp = FALSE;

            // check if this language is supported by the CAT config
            foreach (CONFIG['LANGUAGES'] as $language => $value) {
                if (preg_match("/^" . $language . ".*/", $tryLang)) {
                    $localeTmp = $value['locale'];
                    $langIndex = $language; // ???
                    break;
                }
            }
            // make sure that the selected locale is actually instlled on this system
            // normally this should not be needed, but it is a safeguard agains misconfiguration
            if ($localeTmp) {
                if (setlocale(LC_ALL, $localeTmp)) {
                    $theLocale = $localeTmp;
                    break;
                }
            }
        }
        putenv("LC_ALL=" . $theLocale);
        $_SESSION['language'] = $langIndex;
        $loggerInstance = new Logging();
        $loggerInstance->debug(4, "selected lang:$langIndex:$theLocale\n");
        $loggerInstance->debug(4, print_r($langConverted, true));
        return([$langIndex, $theLocale]);
    }

    /**
     * gets the language setting in CAT
     */
    public function getLang() {
        return $this->LANG;
    }
}
