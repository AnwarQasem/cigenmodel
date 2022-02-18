<?php
namespace Muravian\CiModelGen;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\API\ResponseTrait;

/**
*  A sample class
*
*  Use this section to define what this class is doing, the PHPDocumentator will use this
*  to automatically generate an API documentation using this information.
*
*  @author Anwar Subhi (Muravian)
*/
class CiModelGen{

    use ResponseTrait;

    /**
     * @var
     */
    protected $tables;

    /**
     * Running the script.
     */
    public function index()
    {

        $getTables = $this->get_tables();

        $exceptions = [
            'migrations',
        ];

        foreach ($getTables as $t => $table) {
            if (in_array($table, $exceptions)) {
                unset($getTables[$t]);
            }
        }

        $this->tables = $getTables;

        $this->generateModels();
    }

    /**
     * Generate Models
     */
    private function generateModels()
    {

        $model_template = file_get_contents(ROOTPATH  . "vendor/muravian/CiModelGen/src/templates/Model.txt");

        foreach ($this->tables as $table) {
            $model_file = APPPATH . "/Models/" . ucfirst($table) . "Model.php";
            if (!file_exists(ucfirst($model_file))) {
                $model = $model_template;
                $model = str_replace("{table}", $table, $model);
                $model = str_replace("{model}", ucfirst($table), $model);
                if($this->check_soft_delete($table)) {
                    $model = str_replace("{use_soft_delete}", "true", $model);
                } else {
                    $model = str_replace("{use_soft_delete}", "false", $model);
                }
                $model = str_replace("{allowed_fields}", $this->allowed_fields($table), $model);
                $fh    = fopen($model_file, 'w');
                fwrite($fh, $model);
                fclose($fh);
            }

        }
    }

    /**
     * Define Fields
     *
     * @param $table
     * @return array
     */
    private function defineFields($table)
    {
        $returned_fields = [];
        $db              = \Config\Database::connect();
        $fields          = $db->getFieldData($table);

        return $fields;
    }

    /**
     * Check for soft delete
     *
     * @param $table
     * @return bool
     */
    private function check_soft_delete($table)
    {
        $create = false;
        $update = false;
        $delete = false;
        $fields = $this->defineFields($table);
        foreach ($fields as $field) {
            if ($field->name == 'created_at') {
                $create = true;
            }
            if ($field->name == 'updated_at') {
                $update = true;
            }
            if ($field->name == 'deleted_at') {
                $delete = true;
            }
        }

        if ($create && $update && $delete) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Check allowed fields
     *
     * @param $table
     * @return string
     */
    private function allowed_fields($table) {
        $fields = $this->defineFields($table);

        $allowed_fields = [];
        foreach($fields as $field) {
            $allowed_fields[] = $field->name;
        }

        return "'" . implode("','", $allowed_fields) . "'";
    }

    /**
     * Get table list
     *
     * @return array|bool
     */
    private function get_tables()
    {
        $db     = \Config\Database::connect();
        $tables = $db->listTables();

        return $tables;
    }

}