<?php

class LI18nUtils {

    public static function getAvailableLanguages() {

        if (!isset($_SERVER['PROJECT_DIR'])) return array();
        
        $i18n_dir = LConfigReader::simple('/i18n/translations_root_folder');

        $lang_dir = $_SERVER['PROJECT_DIR'] . $i18n_dir;

        $elements = scandir($lang_dir);

        $result = [];

        foreach ($elements as $elem) {
            if ($elem != '.' && $elem != '..') {
                if (is_dir($lang_dir . $elem)) {
                    $result[] = $elem;
                }
            }
        }

        return $result;
    }

    public static function getCurrentLang() {
        
        if (LEnvironmentUtils::getEnvironment()=="script") return "default";
        
        if (isset($_SERVER['CAPTURE'])) $last_capture = $_SERVER['CAPTURE'];
        else $last_capture = null;
        
        $available_languages = self::getAvailableLanguages();

        //zero check, from the capture
        $capture_lang_variable = LConfigReader::simple('/i18n/capture_lang_variable');
        if ($last_capture && isset($last_capture[$capture_lang_variable]) && in_array($last_capture[$capture_lang_variable], $available_languages)) {
            return $last_capture[$capture_lang_variable];
        }
        
        //first check in session variable
        $session_variable_path = LConfigReader::simple('/i18n/session_lang_variable_path');

        if (isset($_SESSION)) {
            $t = new LTreeMap($_SESSION);
            $current_lang = $t->get($session_variable_path, null);

            if ($current_lang && in_array($current_lang, $available_languages))
                return $current_lang;
        }
        
        //second try with the lang cookie
        $cookie_lang_variable = LConfigReader::simple('/i18n/cookie_lang_variable');
        if (isset($_COOKIE) && isset($_COOKIE[$cookie_lang_variable]) && in_array($_COOKIE[$cookie_lang_variable], $available_languages)) {
            return $_COOKIE[$cookie_lang_variable];
        }

        //third check in browser languages
        $preferred_lang_array = LEnvironmentUtils::getPreferredLanguageArray();

        foreach ($preferred_lang_array as $preferred_lang) {
            if (in_array($preferred_lang, $available_languages))
                return $preferred_lang;
        }

        //fourth - try with prefix of languages
        foreach ($preferred_lang_array as $preferred_lang2) {
            $lang_prefix = explode('_', $preferred_lang2);
            if (in_array($lang_prefix, $available_languages))
                return $lang_prefix;
        }

        //fifth check the default language
        $default_lang = LConfigReader::simple('/i18n/default_language');
        if (in_array($default_lang, $available_languages))
            return $default_lang;

        //sixth pick the first available language
        if (isset($available_languages[0]))
            return $available_languages[0];

        //return empty string if no languages are available.
        return "";
    }

    public static function getCurrentLangData() {
        return self::getLangData(self::getCurrentLang());
    }

    private static function normalizeTranslationKey($key) {
        $key = str_replace('.', '/', $key);
        $key = str_replace('//', '/', $key);
        return $key;
    }

    public static function getLangData($lang) {

        $default_lang = LConfigReader::simple('/i18n/default_language');

        $lang_data = self::internalGetLangData($lang);

        if (LConfigReader::simple('/i18n/override_default_lang')) {
            if ($lang == $default_lang)
                return $lang_data;
            else {
                $default_lang_data = self::internalGetLangData($default_lang);

                $final_lang_data = array_replace_recursive($default_lang_data, $lang_data);

                return $final_lang_data;
            }
        } else {
            return $lang_data;
        }
    }

    private static function internalGetLangData($lang) {

        $base_folder = LEnvironmentUtils::getBaseDir();

        $source_factory_class = LConfigReader::simple('/template/source_factory_class');

        $my_factory = new $source_factory_class();

        $root_folder = $base_folder . LConfigReader::simple('/i18n/translations_root_folder');

        if (!is_dir($root_folder))
            return [];

        $root_lang_folder = $root_folder . $lang . '/';

        if (!is_dir($root_lang_folder))
            return [];

        $all_translations = self::recursiveScanDir($root_lang_folder);

        $all_template_translations = [];
        $all_plain_translations = [];
        //separating all plain translations from templates
        foreach ($all_translations as $key => $trans) {
            if ($my_factory->isTemplateSource($trans)) {
                $all_template_translations[$key] = $trans;
            } else {
                $all_plain_translations[$key] = $trans;
            }
        }

        //translations are separated, now create a string template source

        $cache_folder = LConfigReader::simple('/i18n/cache_folder');

        $lang_cache_folder = $cache_folder . $lang . '/';

        //getting a string template source using a cache dir depending by the language
        $template_source = $my_factory->createStringArrayTemplateSource($all_template_translations, $lang_cache_folder);

        $all_prepared_templates = [];

        foreach (array_keys($all_template_translations) as $key) {
            if (!$template_source->searchTemplate($key))
                throw new \Exception("Unable to find template : " . $key);

            $all_prepared_templates[$key] = $template_source->getTemplate($key)->getImplementationObject();
        }
        //merging the plain translations and the prepared templates
        $final_translations_array = array_merge_recursive($all_plain_translations, $all_prepared_templates);

        //putting all results in tree and getting the resulting data
        $t = new LTreeMap();
        foreach ($final_translations_array as $key => $value) {
            $t->set($key, $value);
        }

        return $t->getRoot();
    }

    private static function recursiveScanDir($folder) {

        $json_reader = new LJsonDataStorage();
        $json_reader->init($folder);
        $ini_reader = new LIniDataStorage();
        $ini_reader->init($folder);
        $xml_reader = new LXmlDataStorage();
        $xml_reader->init($folder);

        $result = [];
        $elements = scandir($folder);

        foreach ($elements as $elem) {
            if ($elem == '.' || $elem == '..')
                continue;

            if (is_dir($folder . $elem)) {
                $result = array_merge_recursive($result, self::recursiveScanDir($folder . $elem . '/'));
            }
            if (is_file($folder . $elem)) {
                //ini
                if ($ini_reader->isValidFilename($elem)) {
                    $raw_data_array = $ini_reader->loadArray($elem);
                    foreach ($raw_data_array as $key => $trans) {
                        $result[self::normalizeTranslationKey($key)] = $trans;
                    }
                }
                //xml
                if ($xml_reader->isValidFilename($elem)) {
                    $raw_data_array = $xml_reader->loadArray($elem);
                    foreach ($raw_data_array as $key => $trans) {
                        $result[self::normalizeTranslationKey($key)] = $trans;
                    }
                }
                //json
                if ($json_reader->isValidFilename($elem)) {
                    $raw_data_array = $json_reader->loadArray($elem);
                    foreach ($raw_data_array as $key => $trans) {
                        $result[self::normalizeTranslationKey($key)] = $trans;
                    }
                }

            }
        }

        return $result;
    }

}
