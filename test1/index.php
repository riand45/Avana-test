<?php

function testPharenteses(string $data, int $index)
{
    $totalParanthesis = 0;

    for ($i = 0; $i < strlen($data); $i++) {

        if ($data[$i] === "(" && $i > $index) {
            $totalParanthesis++;
        }

        if ($data[$i] === ")" && $i > $index) {
            $totalParanthesis--;

            if ($totalParanthesis === -1) {
                return $i;
            }
        }
    }
};

echo testPharenteses("a (b c (d e (f) g) h) i (j k)", 2);