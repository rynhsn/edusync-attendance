<?php

namespace App\Libraries;

use RuntimeException;
use SimpleXMLElement;

class StudentSpreadsheet
{
    private const MAX_ARCHIVE_SIZE = 10 * 1024 * 1024;
    private const MAX_ENTRY_SIZE = 5 * 1024 * 1024;
    private const MAIN_NS = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    private const REL_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    private const PACKAGE_REL_NS = 'http://schemas.openxmlformats.org/package/2006/relationships';

    public function download(string $filename, array $rows): \CodeIgniter\HTTP\ResponseInterface
    {
        $contents = $this->toXlsx($rows);

        return service('response')
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody($contents);
    }

    public function toXlsx(array $rows): string
    {
        $entries = [
            '[Content_Types].xml' => $this->contentTypesXml(),
            '_rels/.rels' => $this->rootRelationshipsXml(),
            'xl/workbook.xml' => $this->workbookXml(),
            'xl/_rels/workbook.xml.rels' => $this->workbookRelationshipsXml(),
            'xl/worksheets/sheet1.xml' => $this->worksheetXml($rows),
        ];

        return $this->createZip($entries);
    }

    public function read(string $path): array
    {
        $entries = $this->readZip($path);
        $workbook = $this->parseXml($entries['xl/workbook.xml'] ?? null, 'Workbook');
        $relationships = $this->parseXml($entries['xl/_rels/workbook.xml.rels'] ?? null, 'Workbook relationships');
        $sheetNodes = $workbook->children(self::MAIN_NS)->sheets->children(self::MAIN_NS)->sheet;
        if (count($sheetNodes) === 0) {
            throw new RuntimeException('Workbook contains no worksheet.');
        }

        $relationshipId = (string) $sheetNodes[0]->attributes(self::REL_NS)['id'];
        $target = null;
        foreach ($relationships->children(self::PACKAGE_REL_NS)->Relationship as $relationship) {
            $attributes = $relationship->attributes();
            if ((string) $attributes['Id'] === $relationshipId) {
                $target = (string) $attributes['Target'];
                break;
            }
        }
        if ($target === null || $target === '') {
            throw new RuntimeException('The first worksheet could not be found.');
        }

        $sheetPath = $this->resolveWorkbookTarget($target);
        $sheet = $this->parseXml($entries[$sheetPath] ?? null, 'Worksheet');
        $sharedStrings = [];
        if (isset($entries['xl/sharedStrings.xml'])) {
            $sharedXml = $this->parseXml($entries['xl/sharedStrings.xml'], 'Shared strings');
            foreach ($sharedXml->children(self::MAIN_NS)->si as $item) {
                $sharedStrings[] = $this->textFromRichString($item);
            }
        }

        return $this->worksheetRows($sheet, $sharedStrings);
    }

    private function worksheetXml(array $rows): string
    {
        $xmlRows = '';
        foreach ($rows as $rowIndex => $row) {
            $cells = '';
            foreach (array_values($row) as $columnIndex => $value) {
                $reference = $this->columnName($columnIndex + 1) . ($rowIndex + 1);
                $safeValue = $this->spreadsheetSafeValue((string) $value);
                $cells .= '<c r="' . $reference . '" t="inlineStr"><is><t xml:space="preserve">'
                    . htmlspecialchars($safeValue, ENT_XML1 | ENT_QUOTES, 'UTF-8')
                    . '</t></is></c>';
            }
            $xmlRows .= '<row r="' . ($rowIndex + 1) . '">' . $cells . '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="' . self::MAIN_NS . '"><sheetData>' . $xmlRows . '</sheetData></worksheet>';
    }

    private function worksheetRows(SimpleXMLElement $sheet, array $sharedStrings): array
    {
        $rows = [];
        foreach ($sheet->children(self::MAIN_NS)->sheetData->children(self::MAIN_NS)->row as $row) {
            $values = [];
            foreach ($row->children(self::MAIN_NS)->c as $cell) {
                $attributes = $cell->attributes();
                $reference = (string) $attributes['r'];
                if (preg_match('/^([A-Z]+)\d+$/i', $reference, $matches) !== 1) {
                    continue;
                }
                $columnIndex = $this->columnIndex(strtoupper($matches[1]));
                $type = (string) $attributes['t'];
                $cellChildren = $cell->children(self::MAIN_NS);
                if ($type === 'inlineStr') {
                    $value = isset($cellChildren->is) ? $this->textFromRichString($cellChildren->is) : '';
                } elseif ($type === 's') {
                    $sharedIndex = (int) ($cellChildren->v ?? -1);
                    $value = $sharedStrings[$sharedIndex] ?? '';
                } else {
                    $value = (string) ($cellChildren->v ?? '');
                }
                $values[$columnIndex] = $value;
            }
            if ($values === []) {
                $rows[] = [];
                continue;
            }
            ksort($values);
            $rows[] = array_replace(array_fill(0, max(array_keys($values)) + 1, ''), $values);
        }

        return $rows;
    }

    private function textFromRichString(SimpleXMLElement $string): string
    {
        $text = '';
        foreach ($string->xpath('.//*[local-name()="t"]') ?: [] as $textNode) {
            $text .= (string) $textNode;
        }

        return $text;
    }

    private function parseXml(?string $contents, string $label): SimpleXMLElement
    {
        if ($contents === null) {
            throw new RuntimeException($label . ' is missing.');
        }
        $xml = simplexml_load_string($contents, SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOBLANKS);
        if ($xml === false) {
            throw new RuntimeException($label . ' is invalid.');
        }

        return $xml;
    }

    private function readZip(string $path): array
    {
        $archive = file_get_contents($path);
        if ($archive === false || strlen($archive) > self::MAX_ARCHIVE_SIZE) {
            throw new RuntimeException('The workbook is unreadable or too large.');
        }

        $eocdOffset = strrpos($archive, "PK\x05\x06");
        if ($eocdOffset === false || strlen($archive) - $eocdOffset < 22) {
            throw new RuntimeException('The workbook is not a valid XLSX file.');
        }
        $end = unpack('vdisk/vcentralDisk/ventriesOnDisk/ventries/VcentralSize/VcentralOffset/vcommentLength', substr($archive, $eocdOffset + 4, 18));
        if ($end === false
            || $end['disk'] !== 0
            || $end['centralDisk'] !== 0
            || $end['entries'] > 100
            || $end['centralOffset'] + $end['centralSize'] > $eocdOffset) {
            throw new RuntimeException('The XLSX archive uses an unsupported layout.');
        }

        $entries = [];
        $offset = $end['centralOffset'];
        $totalUncompressedSize = 0;
        for ($index = 0; $index < $end['entries']; $index++) {
            if (substr($archive, $offset, 4) !== "PK\x01\x02") {
                throw new RuntimeException('The XLSX archive directory is corrupt.');
            }
            $header = unpack(
                'vversionMade/vversionNeeded/vflags/vmethod/vtime/vdate/Vcrc/Vcompressed/Vuncompressed/vnameLength/vextraLength/vcommentLength/vdiskStart/vinternal/Vexternal/VlocalOffset',
                substr($archive, $offset + 4, 42)
            );
            $name = substr($archive, $offset + 46, $header['nameLength']);
            $offset += 46 + $header['nameLength'] + $header['extraLength'] + $header['commentLength'];
            if (str_contains($name, '..')
                || str_starts_with($name, '/')
                || ($header['flags'] & 1) !== 0
                || $header['localOffset'] + 30 > strlen($archive)) {
                throw new RuntimeException('The XLSX archive contains an unsupported entry.');
            }
            if ($header['uncompressed'] > self::MAX_ENTRY_SIZE) {
                throw new RuntimeException('The XLSX archive contains an oversized entry.');
            }
            $totalUncompressedSize += $header['uncompressed'];
            if ($totalUncompressedSize > 20 * 1024 * 1024) {
                throw new RuntimeException('The XLSX archive expands beyond the allowed size.');
            }

            $localOffset = $header['localOffset'];
            if (substr($archive, $localOffset, 4) !== "PK\x03\x04") {
                throw new RuntimeException('An XLSX archive entry is corrupt.');
            }
            $local = unpack('vversion/vflags/vmethod/vtime/vdate/Vcrc/Vcompressed/Vuncompressed/vnameLength/vextraLength', substr($archive, $localOffset + 4, 26));
            $dataOffset = $localOffset + 30 + $local['nameLength'] + $local['extraLength'];
            if ($dataOffset + $header['compressed'] > strlen($archive)) {
                throw new RuntimeException('An XLSX archive entry is truncated.');
            }
            $compressed = substr($archive, $dataOffset, $header['compressed']);
            $contents = match ($header['method']) {
                0 => $compressed,
                8 => gzinflate($compressed),
                default => false,
            };
            if ($contents === false
                || strlen($contents) !== $header['uncompressed']
                || sprintf('%u', crc32($contents)) !== sprintf('%u', $header['crc'])) {
                throw new RuntimeException('An XLSX archive entry could not be decompressed.');
            }
            $entries[$name] = $contents;
        }

        return $entries;
    }

    private function createZip(array $entries): string
    {
        $localFiles = '';
        $centralDirectory = '';
        $offset = 0;
        $time = 0;
        $date = (1 << 5) | 1;

        foreach ($entries as $name => $contents) {
            $nameLength = strlen($name);
            $size = strlen($contents);
            $crc = (int) sprintf('%u', crc32($contents));
            $localFiles .= pack('VvvvvvVVVvv', 0x04034b50, 20, 0, 0, $time, $date, $crc, $size, $size, $nameLength, 0)
                . $name . $contents;
            $centralDirectory .= pack(
                'VvvvvvvVVVvvvvvVV',
                0x02014b50,
                20,
                20,
                0,
                0,
                $time,
                $date,
                $crc,
                $size,
                $size,
                $nameLength,
                0,
                0,
                0,
                0,
                0,
                $offset
            ) . $name;
            $offset = strlen($localFiles);
        }

        $entryCount = count($entries);
        return $localFiles . $centralDirectory . pack(
            'VvvvvVVv',
            0x06054b50,
            0,
            0,
            $entryCount,
            $entryCount,
            strlen($centralDirectory),
            strlen($localFiles),
            0
        );
    }

    private function resolveWorkbookTarget(string $target): string
    {
        if (str_starts_with($target, '/')) {
            return ltrim($target, '/');
        }

        return 'xl/' . ltrim($target, '/');
    }

    private function columnName(int $number): string
    {
        $name = '';
        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)) . $name;
            $number = intdiv($number, 26);
        }

        return $name;
    }

    private function columnIndex(string $name): int
    {
        $index = 0;
        foreach (str_split($name) as $character) {
            $index = ($index * 26) + ord($character) - 64;
        }

        return $index - 1;
    }

    private function spreadsheetSafeValue(string $value): string
    {
        return preg_match('/^[\s]*[=+\-@]/u', $value) === 1 ? "'" . $value : $value;
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '</Types>';
    }

    private function rootRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="' . self::PACKAGE_REL_NS . '">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="' . self::MAIN_NS . '" xmlns:r="' . self::REL_NS . '">'
            . '<sheets><sheet name="Data Siswa" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function workbookRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="' . self::PACKAGE_REL_NS . '">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>';
    }
}
