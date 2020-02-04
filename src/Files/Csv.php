<?php


namespace QuinenLib\Files;


use Cake\ORM\Entity;
use QuinenLib\Arrays\MapTrait;

class Csv
{
    const BOM = "\xef\xbb\xbf";

    use MapTrait;

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
            'isHeader' => true
        ];
        $f = fopen('php://memory', 'rb+');

        $fputcsv = function ($data) use ($f, $options) {
            return fputcsv($f, $data, $options['delimiter'], $options['enclosure'], $options['escapeChar']);
        };
        collection($data)->each(
            function ($item, $index) use ($f, $options, $fputcsv) {
                /** @var Entity $item * */
                if ($item instanceof Entity) {
                    if ($index === 0 && $options['isHeader']) {
                        $fields = $item->getVisible();
                        return $fputcsv($fields);
                    }
                    $item = $item->toArray();
                }

                return $fputcsv($item);
            }
        );
        rewind($f);
        return stream_get_contents($f);
    }

    /*
     *
     * */
    public static function fromMap($data, $maps, $options = [])
    {
        //TODO a implementer
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
