<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'dBug' . DIRECTORY_SEPARATOR . 'dBug.php';
//require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'URL2.php';

class Helper {

    /**
     * Прячет ссылки от поисковиков
     * @param string $str
     * @return string 
     */
    static public function hideLinks ($str)
    {
        return preg_replace('~<a (.*?)>(.*?)</a>~i', '<noindex><a rel="nofollow" \\1>\\2</a></noindex>', $str);
    }

    /**
     * Удаляет пробелы и переносы строк между тегами, в итоге получается весь текст в одну строку.
     * @param type $string
     * @return type 
     */
    static public function trimTags($string)
    {
        $string = preg_replace("~\r\n|\r|\n~","", $string);
        return preg_replace("~>\s+<~", "><", $string);
    }

    
    /**
     * Выводит дамп переданного значения
     * @param mixed $variable
     * @param string $forceType 
     */
    static public function p($variable, $forceType = '')
    {
        new dBug($variable, $forceType);
    }
    
    /**
    * Вычисляет абсолютный URL по атрибуту href HTML-ссылки и
    * URL'у текущей страницы сайта.
    *
    * @param string $baseAddr  - адрес страницы сайта, на которой находится ссылка
    * @param string $href      - атрибут href ссылки
    */
    function createUrlFromHref ($baseAddr, $href)
    {
        $baseUrlObject = new Net_URL2($baseAddr);
        return $baseUrlObject->resolve($href)->getURL();
    }

    /**
     * Возвращает путь для данного урла. 
     * Для example.com/some/path вернет /some/path 
     */
    function getPathFromUrl($url)
    {
        $baseUrlObject = new Net_URL2($url);
        return $baseUrlObject->getPath();
    }
    /**
     * возвращает содержимое узла XML
     * @param object $elem
     * @return string 
     */
    static public function getNodeInnerHTML($elem) {
        return simplexml_import_dom($elem)->asXML();
    }

    /**
     * Исправляет отображение кирилицы
     * @param string $string
     * @return string 
     */
    static public function fixEncoding($string)
    {
        return mb_convert_encoding( $string, 'HTML-ENTITIES', 'utf-8');
    }
    
    /**
     * Исправляет кривую разметку используя tidy
     * @param string $string
     * @return string 
     */
    static public function fixMarkup($string)
    {
        return tidy_repair_string($res, array('show-body-only'=>true), 'utf8');
    }

    /**
     * Исправляет битый UTF-8 — например, если кто-то сделал substr($title, 250)
     * @param string $string
     * @return string 
     */
    static public function fixUTF8String($string)
    {
        return iconv('UTF-8', 'UTF-8//IGNORE', $string);
    }

    /**
     * Хак для корректного определения кодировки в DOMDocument
     * @param string $html
     * @return DOMDocument 
     */
    static public function getUTF8DOM($html)
    {
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="UTF-8">' . $html);

        // dirty fix
        foreach ($doc->childNodes as $item)
            if ($item->nodeType == XML_PI_NODE)
                $doc->removeChild($item); // remove hack
        $doc->encoding = 'UTF-8'; // insert proper
        return $doc;
    }

    /**
     * Урлкодирует кривые урлы. То есть кодирует пробелы, кирилицу и др.
     * @param string $str Кодируемый урл
     * @param array $noEncode массив символов не подвергающихся урлкодированию
     * @return string
     */
    static public function urlencodePartial ($str, $noEncode = array ('-', '_', '.', '/', '?', '=', '&'))
    {
        $ret = '';
        if (preg_match ("#^(https?://[^/]+)(.*)#", $str, $regs))
        {
            $ret = $regs[1];
            $str = $regs[2];
        }
        $len = mb_strlen ($str);
        for ($i = 0; $i < $len; $i++)
        {
            $ch = mb_substr ($str, $i, 1);
            if (
                    ($ch >= 'a' and $ch <= 'z')
                or  ($ch >= 'A' and $ch <= 'Z')
                or  (in_array ($ch, $noEncode))
                or  ($ch >= '0' and $ch <= '9')
            )
            {
                $ret .= $ch;
            }
            elseif (strlen ($ch) == 1) // Обычный символ
                $ret .= sprintf ("%%%02X", ord($ch[0]));
            else // UNICODE-символ
                $ret .= sprintf ("%%%02X%%%02X", ord($ch[0]), ord($ch[1]));
        }
        return $ret;
    }

  /**
   * Возвращает номер месяца по его названию
   */
    static public function monthToNumber($month){
        switch ($month)
        {
            case "Января": case "января": case "янв.": case "Янв.": case "январь": case "Январь": case "студзеня": case "студзень": case "Сту": case "Jan":
                $month='01';
                break;
            case "Февраля": case "февраля": case "фев.": case "Фев.": case "февраль": case "Февраль": case "лютага": case "люты": case "Лют": case "Feb":
                $month='02';
                break;
            case "Марта": case "марта": case "март": case "Март": case "мар": case "Мар": case "сакавіка": case "сакавік": case "Сак": case "Mar":
                $month='03';
                break;
            case "Апреля": case "апреля": case "Апр.": case "апр.": case "Апрель": case "апрель": case "красавіка": case "красавік": case "Кра": case "Apr":
                $month='04';
                break;
            case "Мая": case "мая": case "Май": case "май": case "мая": case "май": case "May":
                $month='05';
                break;
            case "Июня": case "июня": case "Июнь": case "июнь": case "чэрвеня": case "чэрвень": case "Чэр": case "Jun":
                $month='06';
                break;
            case "Июля": case "июля": case "Июль": case "июль": case "лiпеня": case "лiпень": case "Лiп": case "Jul":
                $month='07';
                break;
            case "Августа": case "августа": case "Авг.": case "авг.": case "Август": case "август": case "жніўня": case "жнівень": case "Жні": case "Aug":
                $month='08';
                break;
            case "Сентября": case "сентября": case "сент.": case "Сент.": case "сентябрь": case "Сентябрь": case "верасня": case "верасень": case "Вер": case "Sep":
                $month='09';
                break;
            case "Октября": case "октября": case "Окт.": case "окт.": case "Октябрь": case "октябрь": case "кастрычніка": case "кастрычнік": case "Кас": case "Oct":
                $month='10';
                break;
            case "Ноября": case "ноября": case "Ноя.": case "ноя.": case "Ноябрь": case "ноябрь": case "лістапада": case "лістапад": case "лiстапада": case "Лiс": case "Nov":
                $month='11';
                break;
            case "Декабря": case "декабря": case "Дек.": case "дек.": case "Декабрь": case "декабрь": case "снежня": case "снежань": case "Сне": case "Dec":
                $month='12';
                break;
            default :
                throw new Exception('Ошибка определения номера месяца (' .$month. ') по его названию');
                break;
        }
        return $month;
    }
    
    /**
     * Возвращает случайный UserAgent
     * @return string 
     */
    static public function getRandUserAgent()
    {
        //list of browsers
        $agentBrowser = array(
                'Firefox',
                'Safari',
                'Opera',
                'Flock',
                'Internet Explorer',
                'Seamonkey',
                'Konqueror',
                'GoogleBot'
        );
        //list of operating systems
        $agentOS = array(
                'Windows 3.1',
                'Windows 95',
                'Windows 98',
                'Windows 2000',
                'Windows NT',
                'Windows XP',
                'Windows Vista',
                'Redhat Linux',
                'Ubuntu',
                'Fedora',
                'AmigaOS',
                'OS 10.5'
        );
        //randomly generate UserAgent
        return $agentBrowser[rand(0,7)].'/'.rand(1,8).'.'.rand(0,9).' (' .$agentOS[rand(0,11)].' '.rand(1,7).'.'.rand(0,9).'; en-US;)';
    }
    
}

/**
 * Более продвинутый аналог strip_tags() для корректного вырезания тагов из html кода.
 * Функция strip_tags(), в зависимости от контекста, может работать не корректно.
 * Возможности:
 *   - корректно обрабатываются вхождения типа "a < b > c"
 *   - корректно обрабатывается "грязный" html, когда в значениях атрибутов тагов могут встречаться символы < >
 *   - корректно обрабатывается разбитый html
 *   - вырезаются комментарии, скрипты, стили, PHP, Perl, ASP код, MS Word таги, CDATA
 *   - автоматически форматируется текст, если он содержит html код
 *   - защита от подделок типа: "<<fake>script>alert('hi')</</fake>script>"
 *
 * @param   string  $s
 * @param   array   $allowable_tags     Массив тагов, которые не будут вырезаны
 * @param   bool    $is_format_spaces   Форматировать пробелы и переносы строк?
 *                                      Вид текста на выходе (plain) максимально приближеется виду текста в браузере на входе.
 *                                      Другими словами, грамотно преобразует text/html в text/plain.
 * @param   array   $pair_tags   массив имён парных тагов, которые будут удалены вместе с содержимым
 *                               см. значения по умолчанию
 * @param   array   $para_tags   массив имён парных тагов, которые будут восприниматься как параграфы (если $is_format_spaces = true)
 *                               см. значения по умолчанию
 * @return  string
 *
 * @license  http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @author   Nasibullin Rinat <n a s i b u l l i n  at starlink ru>
 * @charset  ANSI
 * @version  4.0.8
 */
function strip_tags_smart(
    /*string*/ $s,
    array $allowable_tags = null,
    /*boolean*/ $is_format_spaces = true,
    array $pair_tags = array('script', 'style', 'map', 'iframe', 'frameset', 'object', 'applet', 'comment', 'button'),
    array $para_tags = array('p', 'td', 'th', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'form', 'title', 'pre', 'textarea')
)
{
    //return strip_tags($s);
    static $_callback_type  = false;
    static $_allowable_tags = array();
    static $_para_tags      = array();
    #регулярное выражение для атрибутов тагов
    #корректно обрабатывает грязный и битый HTML в однобайтовой или UTF-8 кодировке!
    static $re_attrs_fast_safe =  '(?![a-zA-Z\d])  #утверждение, которое следует сразу после тага
                                   #правильные атрибуты
                                   (?>
                                       [^>"\']+
                                     | (?<=[\=\x20\r\n\t]|\xc2\xa0) "[^"]*"
                                     | (?<=[\=\x20\r\n\t]|\xc2\xa0) \'[^\']*\'
                                   )*
                                   #разбитые атрибуты
                                   [^>]*';

    if (is_array($s))
    {
        if ($_callback_type === 'strip_tags')
        {
            $tag = strtolower($s[1]);
            if ($_allowable_tags &&
                (array_key_exists($tag, $_allowable_tags) || array_key_exists('<' . trim(strtolower($s[0]), '< />') . '>', $_allowable_tags))
                ) return $s[0];
            if ($tag == 'br') return "\r\n";
            if ($_para_tags && array_key_exists($tag, $_para_tags)) return "\r\n\r\n";
            return '';
        }
        if ($_callback_type === 'strip_spaces')
        {
            if (substr($s[0], 0, 1) === '<') return $s[0];
            return ' ';
        }
        trigger_error('Unknown callback type "' . $_callback_type . '"!', E_USER_ERROR);
    }

    if (($pos = strpos($s, '<')) === false || strpos($s, '>', $pos) === false)  #оптимизация скорости
    {
        #таги не найдены
        return $s;
    }

    #непарные таги (открывающие, закрывающие, !DOCTYPE, MS Word namespace)
    $re_tags = '/<[\/\!]? ([a-zA-Z][a-zA-Z\d]* (?>\:[a-zA-Z][a-zA-Z\d]*)?)' . $re_attrs_fast_safe . '>/sx';

    $patterns = array(
        '/<([\?\%]) .*? \\1>/sx',     #встроенный PHP, Perl, ASP код
        '/<\!\[CDATA\[ .*? \]\]>/sx', #блоки CDATA
        #'/<\!\[  [\x20\r\n\t]* [a-zA-Z] .*?  \]>/sx',  #:DEPRECATED: MS Word таги типа <![if! vml]>...<![endif]>

        '/<\!--.*?-->/s', #комментарии

        #MS Word таги типа "<![if! vml]>...<![endif]>",
        #условное выполнение кода для IE типа "<!--[if expression]> HTML <![endif]-->"
        #условное выполнение кода для IE типа "<![if expression]> HTML <![endif]>"
        #см. http://www.tigir.com/comments.htm
        '/<\! (?:--)?
              \[
              (?> [^\]"\']+ | "[^"]*" | \'[^\']*\' )*
              \]
              (?:--)?
         >/sx',
    );
    if ($pair_tags)
    {
        #парные таги вместе с содержимым:
        foreach ($pair_tags as $k => $v) $pair_tags[$k] = preg_quote($v, '/');
        #[22-May-2008 17:33:51] PHP Fatal error:  Allowed memory size of 33554432 bytes exhausted (tried to allocate 406901857 bytes) in /server/new.avtorif.ru/cms/func/strip_tags_smart.php on line 102
        $patterns[] = '/<((?i:' . implode('|', $pair_tags) . '))' . $re_attrs_fast_safe . '> .*? <\/(?i:\\1)' . $re_attrs_fast_safe . '>/sx';
    }
    #d($patterns);

    $i = 0; #защита от зацикливания
    $max = 99;
    while ($i < $max)
    {
        $s2 = preg_replace($patterns, '', $s);
        if (preg_last_error() !== PREG_NO_ERROR)
        {
            $i = 999;
            break;
        }

        if ($i == 0)
        {
            $is_html = ($s2 != $s || preg_match($re_tags, $s2));
            if (preg_last_error() !== PREG_NO_ERROR)
            {
                $i = 999;
                break;
            }
            if ($is_html)
            {
                if ($is_format_spaces)
                {
                    #В библиотеке PCRE для PHP \s - это любой пробельный символ, а именно класс символов [\x09\x0a\x0c\x0d\x20\xa0] или, по другому, [\t\n\f\r \xa0]
                    #Если \s используется с модификатором /u, то \s трактуется как [\x09\x0a\x0c\x0d\x20]
                    #Браузер не делает различия между пробельными символами,
                    #друг за другом подряд идущие символы воспринимаются как один
                    #$s2 = str_replace(array("\r", "\n", "\t"), ' ', $s2);
                    #$s2 = strtr($s2, "\x09\x0a\x0c\x0d", '    ');
                    $_callback_type = 'strip_spaces';
                    $s2 = preg_replace_callback('/  [\x09\x0a\x0c\x0d]+
                                                  | <((?i:pre|textarea))' . $re_attrs_fast_safe . '>
                                                    .+?
                                                    <\/(?i:\\1)' . $re_attrs_fast_safe . '>
                                                 /sx', __FUNCTION__, $s2);
                    $_callback_type = false;
                    if (preg_last_error() !== PREG_NO_ERROR)
                    {
                        $i = 999;
                        break;
                    }
                }

                #массив тагов, которые не будут вырезаны
                if ($allowable_tags) $_allowable_tags = array_flip($allowable_tags);

                #парные таги, которые будут восприниматься как параграфы
                if ($para_tags) $_para_tags = array_flip($para_tags);
            }
        }#if

        #обработка тагов
        if ($is_html)
        {
            $_callback_type = 'strip_tags';
            $s2 = preg_replace_callback($re_tags, __FUNCTION__, $s2);
            $_callback_type = false;
            if (preg_last_error() !== PREG_NO_ERROR)
            {
                $i = 999;
                break;
            }
        }

        if ($s === $s2) break;
        $s = $s2; $i++;
    }#while
    if ($i >= $max) $s = strip_tags($s); #too many cycles for replace...

    if ($is_format_spaces)
    {
        #вырезаем дублирующие пробелы
        $s = preg_replace('/\x20\x20+/s', ' ', trim($s));
        #вырезаем пробелы в начале и в конце строк
        $s = str_replace(array("\r\n\x20", "\x20\r\n"), "\r\n", $s);
        #заменяем 2 и более переносов строк на 2 переноса строк
        $s = preg_replace('/\r\n[\r\n]+/s', "\r\n\r\n", $s);
    }
    return $s;
}
