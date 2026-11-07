<?php

namespace App\Models;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;

class StudentIdCardLayoutModel
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function getLayouts(): array
    {
        $rows = $this->db->table('student_id_card_layouts')
            ->select([
                'category',
                'side',
                'field_name',
                'position_x',
                'position_y',
                'font_family',
                'font_size',
                'font_color',
                'width',
                'height',
                'zoom',
                'pan_x',
                'pan_y',
            ])
            ->get()
            ->getResultArray();

        $layouts = [];
        foreach ($rows as $row) {
            $category = (string) ($row['category'] ?? '');
            $side = (string) ($row['side'] ?? 'front');
            $field = (string) ($row['field_name'] ?? '');
            if ($category === '' || $field === '') {
                continue;
            }

            $entry = [
                'x' => isset($row['position_x']) ? (float) $row['position_x'] : null,
                'y' => isset($row['position_y']) ? (float) $row['position_y'] : null,
            ];
            if (is_numeric($row['font_size'] ?? null) && $row['font_family'] !== null) {
                $entry['fontFamily'] = (string) $row['font_family'];
                $entry['fontSize'] = (float) $row['font_size'];
                $entry['fontColor'] = ! empty($row['font_color']) ? (string) $row['font_color'] : '#102b58';
            }
            if ($field === 'photo') {
                if (is_numeric($row['width'] ?? null) && is_numeric($row['height'] ?? null)) {
                    $entry['width'] = (float) $row['width'];
                    $entry['height'] = (float) $row['height'];
                }
                if (is_numeric($row['zoom'] ?? null)) {
                    $entry['zoom'] = (float) $row['zoom'];
                }
                if (is_numeric($row['pan_x'] ?? null)) {
                    $entry['panX'] = (float) $row['pan_x'];
                }
                if (is_numeric($row['pan_y'] ?? null)) {
                    $entry['panY'] = (float) $row['pan_y'];
                }
                $entry['shape'] = in_array((string) ($row['font_family'] ?? ''), ['square', 'circle'], true) ? (string) $row['font_family'] : 'circle';
            }
            if ($field === 'code') {
                $entry['width'] = is_numeric($row['width'] ?? null) ? (float) $row['width'] : 50.0;
                $entry['height'] = is_numeric($row['height'] ?? null) ? (float) $row['height'] : 12.0;
                $entry['x'] = isset($entry['x']) && is_numeric($entry['x']) && $entry['x'] > 0 ? (float) $entry['x'] : 25.0;
                $entry['y'] = isset($entry['y']) && is_numeric($entry['y']) && $entry['y'] > 0 ? (float) $entry['y'] : 84.0;
                $entry['type'] = in_array((string) ($row['font_family'] ?? ''), ['barcode', 'qrcode', 'none'], true) ? (string) $row['font_family'] : 'barcode';
                $entry['showText'] = ! isset($row['font_size']) || (float) $row['font_size'] > 0;
            }

            $layouts[$category][$side][$field] = $entry;
        }

        return $layouts;
    }

    public function saveLayouts(array $layouts): void
    {
        $this->db->transBegin();

        try {
            $expected = [];
            foreach ($layouts as $category => $sides) {
                if (! is_array($sides)) {
                    continue;
                }

                foreach ($sides as $side => $fields) {
                    if (! is_array($fields)) {
                        continue;
                    }

                    foreach ($fields as $field => $values) {
                        if (! is_array($values)) {
                            continue;
                        }

                        $isPhoto = ($field === 'photo');
                        $isCode = ($field === 'code');

                        $fontFamilyVal = null;
                        $fontSizeVal = null;
                        $fontColorVal = null;
                        if ($isPhoto) {
                            $fontFamilyVal = in_array($values['shape'] ?? '', ['square', 'circle'], true) ? (string) $values['shape'] : 'circle';
                        } elseif ($isCode) {
                            $fontFamilyVal = in_array($values['type'] ?? '', ['barcode', 'qrcode', 'none'], true) ? (string) $values['type'] : 'barcode';
                            $fontSizeVal = ! empty($values['showText']) ? 1.0 : 0.0;
                        } else {
                            $fontFamilyVal = $values['fontFamily'] ?? null;
                            $fontSizeVal = isset($values['fontSize']) ? round((float) $values['fontSize'], 2) : null;
                            $fontColorVal = ! empty($values['fontColor']) && is_string($values['fontColor']) ? (string) $values['fontColor'] : '#102b58';
                        }

                        $expectedEntry = [
                            'x' => isset($values['x']) ? round((float) $values['x'], 2) : 0.0,
                            'y' => isset($values['y']) ? round((float) $values['y'], 2) : 0.0,
                        ];
                        if (! $isPhoto && ! $isCode) {
                            $expectedEntry['fontFamily'] = $fontFamilyVal;
                            $expectedEntry['fontSize'] = $fontSizeVal;
                            $expectedEntry['fontColor'] = $fontColorVal;
                        }
                        if ($isPhoto) {
                            $expectedEntry['width'] = isset($values['width']) ? round((float) $values['width'], 2) : null;
                            $expectedEntry['height'] = isset($values['height']) ? round((float) $values['height'], 2) : null;
                            $expectedEntry['zoom'] = isset($values['zoom']) ? round((float) $values['zoom'], 2) : 1.0;
                            $expectedEntry['panX'] = isset($values['panX']) ? round((float) $values['panX'], 2) : 0.0;
                            $expectedEntry['panY'] = isset($values['panY']) ? round((float) $values['panY'], 2) : 0.0;
                            $expectedEntry['shape'] = $fontFamilyVal;
                        }
                        if ($isCode) {
                            $codeWidth = isset($values['width']) && is_numeric($values['width']) ? round((float) $values['width'], 2) : 50.0;
                            $codeHeight = isset($values['height']) && is_numeric($values['height']) ? round((float) $values['height'], 2) : 12.0;
                            $codeX = isset($values['x']) && is_numeric($values['x']) && (float) $values['x'] > 0 ? round((float) $values['x'], 2) : 25.0;
                            $codeY = isset($values['y']) && is_numeric($values['y']) && (float) $values['y'] > 0 ? round((float) $values['y'], 2) : 84.0;
                            $expectedEntry['width'] = $codeWidth;
                            $expectedEntry['height'] = $codeHeight;
                            $expectedEntry['x'] = $codeX;
                            $expectedEntry['y'] = $codeY;
                            $expectedEntry['type'] = $fontFamilyVal;
                            $expectedEntry['showText'] = $fontSizeVal > 0;
                        }
                        $expected[$category][$side][$field] = $expectedEntry;

                        $row = $this->db->table('student_id_card_layouts')
                            ->select('*')
                            ->where('category', $category)
                            ->where('side', $side)
                            ->where('field_name', $field)
                            ->get()
                            ->getRowArray();

                        $posX = isset($values['x']) && is_numeric($values['x']) ? round((float) $values['x'], 2) : 0;
                        $posY = isset($values['y']) && is_numeric($values['y']) ? round((float) $values['y'], 2) : 0;
                        if ($isCode && $posX == 0) $posX = 25.0;
                        if ($isCode && $posY == 0) $posY = 84.0;

                        $payload = [
                            'category' => $category,
                            'side' => $side,
                            'field_name' => $field,
                            'position_x' => $posX,
                            'position_y' => $posY,
                            'font_family' => $fontFamilyVal,
                            'font_size' => $fontSizeVal,
                            'font_color' => $fontColorVal,
                            'width' => $isCode ? (isset($values['width']) && is_numeric($values['width']) ? round((float) $values['width'], 2) : 50.0) : ($isPhoto && isset($values['width']) ? round((float) $values['width'], 2) : null),
                            'height' => $isCode ? (isset($values['height']) && is_numeric($values['height']) ? round((float) $values['height'], 2) : 12.0) : ($isPhoto && isset($values['height']) ? round((float) $values['height'], 2) : null),
                            'zoom' => $isPhoto && isset($values['zoom']) ? round((float) $values['zoom'], 2) : 1.00,
                            'pan_x' => $isPhoto && isset($values['panX']) ? round((float) $values['panX'], 2) : 0,
                            'pan_y' => $isPhoto && isset($values['panY']) ? round((float) $values['panY'], 2) : 0,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];

                        if ($row !== null) {
                            $saved = $this->db->table('student_id_card_layouts')
                                ->where('id', $row['id'])
                                ->update($payload);
                        } else {
                            $payload['created_at'] = date('Y-m-d H:i:s');
                            $saved = $this->db->table('student_id_card_layouts')->insert($payload);
                        }

                        if ($saved === false) {
                            throw new DatabaseException('Could not save student ID card layout row: ' . $category . ':' . $side . ':' . $field);
                        }
                    }
                }
            }

            if ($this->db->transStatus() === false) {
                throw new DatabaseException('Student ID card layout transaction failed.');
            }
            if (! $this->db->transCommit()) {
                throw new DatabaseException('Could not commit student ID card layout transaction.');
            }
        } catch (DatabaseException $exception) {
            $this->db->transRollback();
            throw $exception;
        }

        $savedLayouts = $this->getLayouts();
        foreach ($expected as $category => $sides) {
            foreach ($sides as $side => $fields) {
                foreach ($fields as $field => $expectedValues) {
                    $savedValues = $savedLayouts[$category][$side][$field] ?? null;
                    if ($savedValues === null) {
                        throw new DatabaseException('Student ID card layout row was not persisted: ' . $category . ':' . $side . ':' . $field);
                    }

                    foreach ($expectedValues as $key => $expectedValue) {
                        if ($expectedValue === null) {
                            continue;
                        }
                        $savedValue = $savedValues[$key] ?? null;
                        if (is_float($expectedValue)) {
                            if (! is_numeric($savedValue) || abs((float) $savedValue - $expectedValue) > 0.01) {
                                throw new DatabaseException('Student ID card layout value was not persisted: ' . $category . ':' . $side . ':' . $field . ':' . $key);
                            }
                        } elseif ($savedValue !== $expectedValue) {
                            throw new DatabaseException('Student ID card layout value was not persisted: ' . $category . ':' . $side . ':' . $field . ':' . $key);
                        }
                    }
                }
            }
        }
    }
}
