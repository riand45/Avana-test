<?php

namespace App;

use Box\Spout\Reader\ReaderInterface;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

abstract class ExcelValidator
{
    protected array $errors = [];

    protected array $headers = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setHeaders(array $data): void
    {
        foreach ($data as $index => $value) {
            $pattern = '/[^A-Za-z0-9\-_]/';
            $name = preg_replace($pattern, '', $value->getValue());

            if (strpos($value->getValue(), "*") !== false) {
                $this->headers[$index] = [
                    'type' => 'required',
                    'error_message' => 'Missing value in ' . $name
                ];
            }

            if (strpos($value->getValue(), "#") !== false) {
                $this->headers[$index] = [
                    'type' => 'whitespace',
                    'error_message' => $name . ' should not contain any space'
                ];
            }
        }
    }

    /**
     * @throws UnsupportedTypeException
     * @throws IOException
     */
    public function openExcel(): ReaderInterface
    {
        $fileName = (new \ReflectionClass($this))->getShortName();
        $reader = ReaderEntityFactory::createReaderFromFile(__DIR__ . '/../' . $fileName . '.xlsx');
        $reader->setShouldFormatDates(true);
        $reader->setShouldPreserveEmptyRows(false);
        $reader->open(__DIR__ . '/../' . $fileName . '.xlsx');
        return $reader;
    }

    public function checkingData(array $datas, int $indexRow)
    {
        foreach ($this->headers as $index => $header) {
            if (!isset($datas[$index])) {
                $data = null;
            } else {
                $data = $datas[$index]->getValue();
            }

            if ($header['type'] === 'required') {
                $string = preg_replace('/\s+/', '', (string) $data);

                if ($string === "" || $string === null || !isset($datas[$index])) {
                    if (!isset($this->errors[$indexRow]['error'])) {
                        $this->errors[$indexRow]['error'] = '';
                    }
                    $this->errors[$indexRow]['error'] .= $header['error_message'] . ', ';
                }
            }

            if ($header['type'] === 'whitespace' && !is_null($data)) {
                $string = preg_replace('/\s+/', '-', (string) $data);
                if (strpos($string, '-') !== false) {
                    if (!isset($this->errors[$indexRow]['error'])) {
                        $this->errors[$indexRow]['error'] = '';
                    }

                    $this->errors[$indexRow]['error'] .= $header['error_message'] . ', ';
                }
            }
        }
    }

    /**
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     * @throws IOException
     */
    public function validate(): self
    {
        $reader = $this->openExcel();

        foreach ($reader->getSheetIterator() as $sheet) {
            $rows = 0;
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->getCells();
                if ($rows === 0) {
                    $rows++;
                    $this->setHeaders($cells);
                    continue;
                }

                $this->checkingData($cells, $rows);
                $rows++;
            }
        }
        $reader->close();
        return $this;
    }
}
