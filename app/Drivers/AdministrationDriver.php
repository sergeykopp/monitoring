<?php

namespace Kopp\Drivers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdministrationDriver
{
    // Экпорт базы данных в XML файл
    public static function exportToXML()
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        // Создание корневого элемента DOM
        $dataBase = $dom->createElement('database');
        $attribute = $dom->createAttribute('name');
        $attribute->value = config('database.connections.' . config('database.default') . '.database');
        $dataBase->appendChild($attribute);
        $attribute = $dom->createAttribute('xmlns:xsi');
        $attribute->value = 'http://www.w3.org/2001/XMLSchema-instance';
        $dataBase->appendChild($attribute);
        $attribute = $dom->createAttribute('xsi:schemaLocation');
        $attribute->value = config('settings.backup_schema_file_name');
        $dataBase->appendChild($attribute);
        // Добавление DOM таблиц
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            foreach ($table as $key => $tableName) {
                if (!substr_count($tableName, 'view')) { // Кроме представлений
                    $dataBase->appendChild(self::createTable($dom, $tableName));
                }
            }
        }
        $dom->appendChild($dataBase);
        // Запись основного бэкапа
        try {
            $dom->save(config('settings.backup_path') . '/' . config('settings.backup_file_name'));
        } catch (\Exception $e) {
            return 'Ошибка записи файла ' . config('settings.backup_file_name');
        }
        // Если нет папки ТЕКУЩИЙ ГОД, то создать её
        if (false == in_array(strftime("%Y"), Storage::disk('backup')->directories(''))) {
            Storage::disk('backup')->makeDirectory(strftime("%Y"));
        }
        // Запись копии бэкапа в папку ТЕКУЩИЙ ГОД
        try {
            $dom->save(config('settings.backup_path') . '/' . strftime("%Y") . '/backup_' . strftime("%y%m%d") . '.xml');
        } catch (\Exception $e) {
            return 'Ошибка записи файла ' . strftime("%Y") . '/backup_' . strftime("%y%m%d") . '.xml';
        }
        return true;
    }

    // Создание DOM таблицы
    private static function createTable(\DOMDocument $dom, $tableName)
    {
        // Создание корневого элемента таблицы
        $table = $dom->createElement('table');
        $attribute = $dom->createAttribute('name');
        $attribute->value = $tableName;
        $table->appendChild($attribute);
        // Добавление информации о полях таблицы
        $query = "DESCRIBE $tableName";
        $columnsInfo = DB::select($query); // Информация о полях таблицы
        $columns = $dom->createElement('columns');
        foreach ($columnsInfo as $columnInfo) {
            $column = $dom->createElement('column');
            foreach ($columnInfo as $name => $value) {
                $elem = $dom->createElement(strtolower($name));
                $text = $dom->createTextNode($value);
                $elem->appendChild($text);
                $column->appendChild($elem);
            }
            $columns->appendChild($column);
        }
        $table->appendChild($columns);
        // Добавление содержимого таблицы
        $records = $dom->createElement('records');
        $query = "SELECT * FROM $tableName";
        $recordsInfo = DB::select($query);
        foreach ($recordsInfo as $info) {
            $record = $dom->createElement('record');
            foreach ($info as $key => $value) {
                $elem = $dom->createElement('rec');
                $attribute = $dom->createAttribute('name');
                $attribute->value = $key;
                $elem->appendChild($attribute);
                if (null === $value) {
                    $value = 'NULL';
                }
                $text = $dom->createTextNode($value);
                $elem->appendChild($text);
                $record->appendChild($elem);
            }
            $records->appendChild($record);
        }
        $table->appendChild($records);
        return $table;
    }

    // Импорт базы данных из XML файла
    public static function importFromXML()
    {
        $dom = new \DOMDocument();
        try {
            $dom->load(config('settings.backup_path') . '/' . config('settings.backup_file_name'));
        } catch (\Exception $e) {
            return 'Ошибка чтения файла ' . config('settings.backup_file_name');
        }
        // Пересоздание базы данных
        //$dataBase = $dom->getElementsByTagName('database');
        //$dataBaseName = $dataBase->item(0)->getAttribute('name');
        //DB::statement('DROP DATABASE IF EXISTS ' . $dataBaseName);
        //DB::statement('CREATE DATABASE ' . $dataBaseName . ' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci');
        // Удаление таблиц
        $tables = DB::select('SHOW TABLES');
        if (0 < count($tables)) {
            foreach ($tables as $table) {
                foreach ($table as $key => $tableName) {
                    DB::statement("DROP TABLE $tableName");
                    //var_dump("DROP TABLE $tableName <br /><br />");
                }
            }
        }
        // Чтение информации о таблицах из файла
        $tables = $dom->getElementsByTagName('table');
        foreach ($tables as $table) {
            $fields = []; // Массив полей
            $values = []; // Массив значений
            $tableName = $table->getAttribute('name');
            $childs_1 = $table->childNodes;
            foreach ($childs_1 as $child_1) { // columns, records
                $childs_2 = $child_1->childNodes;
                foreach ($childs_2 as $child_2) { // column, record
                    $childs_3 = $child_2->childNodes;
                    if ('column' == $child_2->nodeName) { // Разбор полей таблицы
                        $field = [];
                        foreach ($childs_3 as $child_3) {
                            switch ($child_3->nodeName) {
                                case 'field':
                                    $field[] = "`" . $child_3->textContent . "`";
                                    break;
                                case 'type':
                                    $field[] = $child_3->textContent;
                                    break;
                                case 'null':
                                    if ('NO' == $child_3->textContent) {
                                        $field[] = 'NOT NULL';
                                    }
                                    break;
                                case 'key' :
                                    if ('PRI' == $child_3->textContent) {
                                        $field[] = 'PRIMARY KEY';
                                    }
                                    if ('UNI' == $child_3->textContent) {
                                        $field[] = 'UNIQUE KEY';
                                    }
                                    break;
                                case 'default':
                                    if ('' != $child_3->textContent) {
                                        $field[] = "DEFAULT '" . $child_3->textContent . "'";
                                    } else {
                                        if (!in_array('NOT NULL', $field)) {
                                            $field[] = "NULL DEFAULT NULL";
                                        }
                                    }
                                    break;
                                case 'extra':
                                    if ('' != $child_3->textContent) {
                                        $field[] = strtoupper($child_3->textContent);
                                    }
                                    break;
                                default :
                                    break;
                            }
                        }
                        $fields[] = implode(' ', $field);
                    } else { // Разбор содержимого таблицы
                        $values_2 = [];
                        foreach ($childs_3 as $child_3) {
                            if ('NULL' == $child_3->textContent) {
                                $values_2[$child_3->getAttribute('name')] = null;
                            } else {
                                $values_2[$child_3->getAttribute('name')] = $child_3->textContent;
                            }
                        }
                        $values[] = $values_2;
                    }
                }
            }
            // Создание таблиц
            $fields_str = implode(',', $fields);
            DB::statement("CREATE TABLE `$tableName` ($fields_str) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
            //var_dump("CREATE TABLE `$tableName` ($fields_str) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci <br /><br />");
            // Наполнение таблиц
            foreach ($values as $value) {
                $_columns = [];
                $_values = [];
                foreach ($value as $key => $val) {
                    $_columns[] = "`" . $key . "`";
                    if (null === $val) {
                        $_values[] = 'NULL';
                    } else {
                        $_values[] = DB::connection()->getPdo()->quote($val);
                    }
                }
                $_columns_s = implode(',', $_columns);
                $_values_s = implode(',', $_values);
                DB::insert("INSERT INTO `$tableName` ($_columns_s) VALUES ($_values_s)");
                //var_dump("INSERT INTO `$tableName` ($_columns_s) VALUES ($_values_s) <br /><br />");
            }
        }
        // Пересоздание представлений
        //$this->dBase->viewsRebuild();
        return true;
    }
}