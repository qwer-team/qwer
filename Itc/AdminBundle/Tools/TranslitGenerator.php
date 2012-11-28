<?php
namespace Itc\AdminBundle\Tools;

class TranslitGenerator {
    /**
     *
     * @param type $string 
     * 
     */
    public static function getTranslit( $string )
    {

        $string = ucfirst( $string );

        $tr = array(
            "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
            "Д"=>"D","Е"=>"E","Ё"=>"Yo","Ж"=>"J","З"=>"Z","И"=>"I",
            "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
            "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
            "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"Ch",
            "Ш"=>"Sh","Щ"=>"Sch","Ъ"=>"","Ы"=>"Yi","Ь"=>"",
            "Э"=>"E","Ю"=>"Yu","Я"=>"Ya","а"=>"a","б"=>"b",
            "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"yo","ж"=>"j",
            "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
            "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
            "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
            "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
            "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
            " "=>"_",","=>"","."=>"","/"=>"","\\"=>"","|"=>"",
            "!"=>"","&"=>"","^"=>"","@"=>"","#"=>"","$"=>"","%"=>"",
            "*"=>"","("=>"",")"=>"","{"=>"","}"=>"","["=>"","]"=>"",
            "?"=>"","<"=>"",">"=>"","+"=>"","="=>"","~"=>"","`"=>"",
            ":"=>"",";"=>"","№"=>"",
        );
        return strtr( $string, $tr );
    }
}