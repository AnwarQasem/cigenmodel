<?php

namespace Muravian\CiModelGen\Models;

use CodeIgniter\Model;

/**
 * @author Anwar Subhi <anwar.subhi@gmail.com>
 */
class BaseModel extends Model
{
    protected $table = '';
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;


    // DEFINE COLUMNS
    protected $allowedFields = [];
    protected $removeFields = ['deleted_at'];

    // DEFINE FILE TYPES
    protected $fieldType = [];

    // ENCRYPT FIELD or SHOW EMPTY; Mostly used for Passwords.
    protected $saveEncrypted = [];
    protected $showEmptyFields = [];

    // TIMESTAMP
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // VALIDATION
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = false;

    // CALLBACKS
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = ['callback_after_find'];
    protected $beforeDelete = [];
    protected $afterDelete = [];


    // SET ACTIONS ON READ / INSERT / UPDATE

    /**
     * Callback used to remove $removeFields and empty values of $showEmptyFields
     *
     * @param $row
     * @return mixed
     */
    public function callback_after_find($row)
    {
        if ($row['method'] === 'first') {
            foreach ($row['data'] as $key => $value) {

                if (in_array($key, $this->removeFields)) {
                    unset($row['data']->$key);
                }

                if (in_array($key, $this->showEmptyFields)) {
                    $row['data']->$key = '';
                }
            }
        }

        if ($row['method'] !== 'first') {
            for ($i = 0; $i < count($row['data']); $i++) {
                foreach ($row['data'][$i] as $key => $value) {

                    if (in_array($key, $this->removeFields)) {
                        unset($row['data'][$i]->$key);
                    }

                    if (in_array($key, $this->showEmptyFields)) {
                        $row['data'][$i]->$key = '';
                    }
                }
            }
        }

        return $row;
    }

    /**
     * Callback used to encrypt data from $saveEncrypted
     *
     * @param $row
     * @return array
     */
    public function callback_before_insert($row): array
    {
        foreach ($row['data'] as $key => $value) {
            if (in_array($key, $this->saveEncrypted)) {
                $row['data'][$key] = password_hash($value, PASSWORD_DEFAULT);
            }
        }

        return $row;
    }

    /**
     * Callback used to encrypt data from $saveEncrypted and remove Empty values
     *
     * @param $row
     * @return array
     */
    public function callback_before_update($row): array
    {
        foreach ($row['data'] as $key => $value) {
            if (in_array($key, $this->saveEncrypted)) {
                $row['data'][$key] = password_hash($value, PASSWORD_DEFAULT);
            }

            if (strlen($value) === 0) {
                unset($row['data'][$key]);
            }
        }

        return $row;
    }

    /**
     * Get Table columns Data
     *
     * @return array
     */
    public function getFieldData(): array
    {
        $fields = $this->db->getFieldData($this->table);

        foreach ($fields as $key => $value) {
            if (in_array($value->name, $this->removeFields)) {
                unset($fields[$key]);
            }
        }

        foreach ($fields as $key => $value) {
            if (isset($this->fieldType[$value->name])) {
                $value->type = $this->fieldType[$value->name];
            }
        }

        return array_values($fields);
    }

    // SET TABLE DETAILS


    /**
     * Set Table to be used
     *
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * Set $returnType
     *
     * @param string $returnType
     */
    public function setReturnType(string $returnType): void
    {
        $this->returnType = $returnType;
    }

    /**
     * Set usage of Soft Deletes
     *
     * @param bool $useSoftDeletes
     */
    public function setUseSoftDeletes(bool $useSoftDeletes): void
    {
        $this->useSoftDeletes = $useSoftDeletes;
    }

    /**
     * Set Timestamps usage
     *
     * @param bool $useTimestamps
     */
    public function setUseTimestamps(bool $useTimestamps): void
    {
        $this->useTimestamps = $useTimestamps;
    }

    /**
     * Set created_at field
     *
     * @param string $createdField
     */
    public function setCreatedField(string $createdField): void
    {
        $this->createdField = $createdField;
    }

    /**
     * Set primaryKey field
     *
     * @param string $primaryKey
     */
    public function setPrimaryKey(string $primaryKey): void
    {
        $this->primaryKey = $primaryKey;
    }

    /**
     * Set allowed Fields
     *
     * @param array $allowedFields
     */
    public function setAllowedFields(array $allowedFields): void
    {
        $this->allowedFields = $allowedFields;
    }

    /**
     * Set remove Fields
     *
     * @param $removeFields
     * @return void
     */
    public function setRemoveFields($removeFields): void
    {
        if (is_array($removeFields)) {
            $this->removeFields = $removeFields;
        } else {
            $this->removeFields[] = $removeFields;
        }
    }

    /**
     * Set field Type
     *
     * @param array $fieldType
     */
    public function setFieldType(array $fieldType): void
    {
        $this->fieldType = $fieldType;
    }

    /**
     * Set updated_at field
     *
     * @param string $updatedField
     */
    public function setUpdatedField(string $updatedField): void
    {
        $this->updatedField = $updatedField;
    }

    /**
     * Set deleted_at field
     *
     * @param string $deletedField
     */
    public function setDeletedField(string $deletedField): void
    {
        $this->deletedField = $deletedField;
    }

    /**
     * Set fields to be saved encrypted (mostly passwords)
     *
     * @param array $saveEncrypted
     */
    public function setSaveEncrypted(array $saveEncrypted): void
    {
        $this->saveEncrypted = $saveEncrypted;
    }

    /**
     * Set fields that will return an empty value even if they are not empty in db.
     *
     * @param array $showEmptyFields
     */
    public function setShowEmptyFields(array $showEmptyFields): void
    {
        $this->showEmptyFields = $showEmptyFields;
    }

    /**
     * Set to skip Validation
     *
     * @param bool $skipValidation
     */
    public function setSkipValidation(bool $skipValidation): void
    {
        $this->skipValidation = $skipValidation;
    }

    /**
     * Set validation Rules
     *
     * @param array $validationRules
     */
    public function setValidationRules(array $validationRules): void
    {
        $this->validationRules = $validationRules;
    }

    /**
     * Set validation Messages
     *
     * @param array $validationMessages
     */
    public function setValidationMessages(array $validationMessages): void
    {
        $this->validationMessages = $validationMessages;
    }

    /**
     * Clear validation Rules
     *
     * @param bool $cleanValidationRules
     */
    public function setCleanValidationRules(bool $cleanValidationRules): void
    {
        $this->cleanValidationRules = $cleanValidationRules;
    }

    /**
     * Set to allow Callbacks hooks
     *
     * @param bool $allowCallbacks
     */
    public function setAllowCallbacks(bool $allowCallbacks): void
    {
        $this->allowCallbacks = $allowCallbacks;
    }

    /**
     * Before Insert Callback
     *
     */
    public function setBeforeInsert($beforeInsert)
    {
        $this->beforeInsert[] = $beforeInsert;
    }

    /**
     * After Insert Callback
     *
     * @param array $afterInsert
     */
    public function setAfterInsert(string $afterInsert): void
    {
        $this->afterInsert[] = $afterInsert;
    }

    /**
     * Before Update Callback
     *
     * @param array $beforeUpdate
     */
    public function setBeforeUpdate(string $beforeUpdate): void
    {
        $this->beforeUpdate[] = $beforeUpdate;
    }

    /**
     * After Update Callback
     *
     * @param array $afterUpdate
     */
    public function setAfterUpdate(string $afterUpdate): void
    {
        $this->afterUpdate[] = $afterUpdate;
    }

    /**
     * Before Find Callback
     *
     * @param array $beforeFind
     */
    public function setBeforeFind(string $beforeFind): void
    {
        $this->beforeFind[] = $beforeFind;
    }

    /**
     * After Find Callback
     *
     * @param string[] $afterFind
     */
    public function setAfterFind(string $afterFind): void
    {
        $this->afterFind[] = $afterFind;
    }

    /**
     * Before Delete Callback
     *
     * @param array $beforeDelete
     */
    public function setBeforeDelete(string $beforeDelete): void
    {
        $this->beforeDelete[] = $beforeDelete;
    }

    /**
     * After Delete Callback
     *
     * @param array $afterDelete
     */
    public function setAfterDelete(string $afterDelete): void
    {
        $this->afterDelete[] = $afterDelete;
    }


}
