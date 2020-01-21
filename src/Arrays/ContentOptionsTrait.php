<?php
/**
 * @author Laurent DESMONCEAUX <laurent@quinen.net>
 * @created 06/04/2018 15:06
 * @version 1.0
 */

namespace QuinenLib\Arrays;

use Cake\Utility\Hash;

/**
 *
 */
trait ContentOptionsTrait
{
    /**
     * Transforme les données de type :
     * "toto"              => "toto"   ,[]
     * ["titi"]            => "titi"   ,[]
     * ["tata",['a'=>"b"]] => "tata"   ,['a'=>"b"]
     * [,['b'=>"c"]]       => null     ,['b'=> "c"]
     *
     * @param string|array $content content to set
     * @param array $contentDefault to set
     * @return array with 2 data
     */
    public function getContentOptions($content, $contentDefault = null)
    {
        $contentOptions = [];
        // if isset 0 if the content is already an array ... but data shoud be an option

        if (is_array($content) && isset($content[0])) {
            $contentOptions = Hash::get($content, '1', []);
            $content = Hash::get($content, '0', $contentDefault);
        }
        return [$content, $contentOptions];
    }

    /**
     * dans un array, extrait la clé du tableau et renvoi la cle + options restantes
     *
     * ['content'=>"content",'toto'=>"titi"]   =>  ["content",['toto'=>"titi"]]
     */
    public function extractContentOptions($contentOptions, $field = 'content', $defaultFieldValue = null)
    {
        $contentValue = $defaultFieldValue;
        if (is_string($contentOptions)) {
            $contentValue = $contentOptions;
            $contentOptions = [];
        } else {
            if (isset($contentOptions[$field])) {
                $contentValue = $contentOptions[$field];
                unset($contentOptions[$field]);
            }
        }
        return [$contentValue, $contentOptions];
    }

    public function checkContentOptions($array)
    {
        // conditions for true array of fields
        if (is_array($array) && !is_array($array[0]) && !is_array($array[1])) {
            throw new \Cake\Core\Exception\Exception(
                "2 strings = [[ 'double','crochet' ]] not " . var_export($array, true)
            );
        }
    }
}
