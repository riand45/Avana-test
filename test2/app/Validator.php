<?php

namespace App;

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;

class Validator
{
    /**
     * @var ExcelValidator
     */
    private \App\ExcelValidator $validator;

    public function __construct(ExcelValidator $excelValidator)
    {
        $this->validator = $excelValidator;
    }

    /**
     * @throws ReaderNotOpenedException
     * @throws UnsupportedTypeException
     * @throws IOException
     */
    public function validate(): ExcelValidator
    {
        return $this->validator->validate();
    }
}
