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
            }
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

            $layouts[$category][$side][$field] = $entry;
        }

        return $layouts;
    }

    public function saveLayouts(array $layouts): void
    {
        dd($layouts);
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

                        $expected[$category][$side][$field] = [
                            'x' => isset($values['x']) ? round((float) $values['x'], 2) : 0.0,
                            'y' => isset($values['y']) ? round((float) $values['y'], 2) : 0.0,
                            'fontFamily' => $values['fontFamily'] ?? null,
                            'fontSize' => isset($values['fontSize']) ? round((float) $values['fontSize'], 2) : null,
                            'width' => isset($values['width']) ? round((float) $values['width'], 2) : null,
                            'height' => isset($values['height']) ? round((float) $values['height'], 2) : null,
                            'zoom' => isset($values['zoom']) ? round((float) $values['zoom'], 2) : 1.0,
                            'panX' => isset($values['panX']) ? round((float) $values['panX'], 2) : 0.0,
                            'panY' => isset($values['panY']) ? round((float) $values['panY'], 2) : 0.0,
                        ];

                        // $row = $this->db->table('student_id_card_layouts')
                        //     ->where('category', $category)
                        //     ->where('side', $side)
                        //     ->where('field_name', $field)
                        //     ->get()
                        //     ->getRowArray();

                        $row = $this->db->table('student_id_card_layouts')
                            ->select('*')
                            ->where('category', $category)
                            ->where('side', $side)
                            ->where('field_name', $field)
                            ->get()
                            ->getRowArray();

                        log_message('error', json_encode($payload));

                        $payload = [
                            'category' => $category,
                            'side' => $side,
                            'field_name' => $field,
                            'position_x' => isset($values['x']) ? round((float) $values['x'], 2) : 0,
                            'position_y' => isset($values['y']) ? round((float) $values['y'], 2) : 0,
                            'font_family' => $values['fontFamily'] ?? null,
                            'font_size' => isset($values['fontSize']) ? round((float) $values['fontSize'], 2) : null,
                            'width' => isset($values['width']) ? round((float) $values['width'], 2) : null,
                            'height' => isset($values['height']) ? round((float) $values['height'], 2) : null,
                            'zoom' => isset($values['zoom']) ? round((float) $values['zoom'], 2) : 1.00,
                            'pan_x' => isset($values['panX']) ? round((float) $values['panX'], 2) : 0,
                            'pan_y' => isset($values['panY']) ? round((float) $values['panY'], 2) : 0,
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
