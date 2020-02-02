<?php


namespace QuinenLib\Files;


class Csv
{
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
}
