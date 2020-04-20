<?php


namespace QuinenLib\Files;


use Cake\ORM\Entity;
use Cake\Utility\Hash;
use QuinenLib\Arrays\MapTrait;

class Csv
{
    const BOM = "\xef\xbb\xbf";

    use MapTrait;

    /**
     * @param $fileString
     * @param array $options
     * @return array
     * @deprecated use filenameToArray, it's faster !!!
     */
    public static function fileToArray($fileString, $options = [])
    {
        $options += [
            'separator' => ";",
            'enclose' => '"',
        ];

        $fileLines = preg_split('/\r\n|\r|\n/', $fileString);

        return collection($fileLines)->map(
            function ($fileLine) use ($options) {
                $lineArray = explode($options['separator'], $fileLine);
                return collection($lineArray)->map(
                    function ($column) use ($options) {
                        return trim($column, $options['enclose']);
                    }
                )->toArray();
            }
        )->toArray();
    }

    /**
     * @param $filename
     * @param array $options
     * @return array|bool
     */
    public static function filenameToArray($filename, $options = [])
    {
        $options += [
            'length' => 0,
            'delimiter' => ",",
            'enclosure' => '"',
            'escapeString' => "\\"
        ];

        if (($f = fopen($filename, 'r')) && $f) {
            $lines = [];

            if (fgets($f, 4) !== self::BOM) {
                // BOM not found - rewind pointer to start of file.
                rewind($f);
            }

            while (($line = fgetcsv(
                    $f,
                    $options['length'],
                    $options['delimiter'],
                    $options['enclosure'],
                    $options['escapeString']
                )) && $line) {
                $lines[] = $line;
            }

            fclose($f);
            return $lines;
        }
        return false;
    }

    /**
     * take an array or collection and return a csv string
     * @param $data
     * @param array $options
     * @return false|string
     */
    public static function fromData($data, $options = [])
    {
        $options += [
            'delimiter' => ',',
            'enclosure' => '"',
            'escapeChar' => "\\",
            'isHeader' => true,
            // eliminate virtuals fields
            'virtuals' => [],

        ];
        $f = fopen('php://memory', 'rb+');

        $fputcsv = function ($data) use ($f, $options) {
            return fputcsv($f, $data, $options['delimiter'], $options['enclosure'], $options['escapeChar']);
        };
        collection($data)->each(
            function ($item, $index) use ($f, $options, $fputcsv) {

                $string = '';
                /** @var Entity $item * */
                if ($item instanceof Entity) {
                    // on elimine les champs virtuels inutiles
                    $item->setVirtual($options['virtuals']);
                    $itemArray = $item->toArray();
                    if ($index === 0 && $options['isHeader']) {
                        $fields = $item->getVisible();
                        $string .= $fputcsv($fields);
                    }
                    $item = $itemArray;
                }
                $string .= $fputcsv($item);
                return $string;
            }
        );
        rewind($f);
        return stream_get_contents($f);
    }

    /*
     *
     * */
    public static function fromMiniMap($data, $maps, $options = [])
    {
        $options += [
            'delimiter' => ',',
            'enclosure' => '"',
            'escapeChar' => "\\",
            'isHeader' => true,
            'addBom' => false
        ];
        $f = fopen('php://memory', 'rb+');
//$options['enclosure'] = '';
        $fputcsv = function ($data) use ($f, $options) {
            //return fputcsv($f, array_map(function($v){return "\"$v\"";},$data), $options['delimiter'], $options['enclosure'], $options['escapeChar']);
            return fputcsv($f, $data, $options['delimiter'], $options['enclosure'], $options['escapeChar']);
        };
        collection($data->disableBufferedResults())->each(
            function ($item, $index) use ($f, $options, $fputcsv, $maps) {
                //debug($item);
                $string = '';
                /** @var Entity $item * */
                if ($item instanceof Entity) {
                    if ($index === 0 && $options['addBom']) {
                        $string .= self::BOM;
                    }
                    if ($index === 0 && $options['isHeader']) {
                        $fields = array_keys($maps);
                        $string .= $fputcsv($fields);
                    }

                    $itemArray = collection($maps)->map(function ($v, $k) use ($item) {
                        if (is_callable($v)) {
                            return $v($item);
                        } else if (empty($v)) {
                            return $v;
                        }
                        return Hash::get($item, $v);
                    })->toArray();
                    $item = $itemArray;
                }
                $string .= $fputcsv($item);
                return $string;
            }
        );
        rewind($f);
        return stream_get_contents($f);
    }

    public function getMapFormat($map)
    {
        return $map;
    }

    public function getMapLabel($field)
    {
        return $field;
    }
}
