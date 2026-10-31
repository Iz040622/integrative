<?php

namespace App\Models\Crud;

abstract class CrudTemplate {
    public function process(array $data) {
        $this->validate($data);
        return $this->save($data);
    }

    public function processUpdate(array $data, $id) {
        $this->validateUpdate($data, $id);
        return $this->saveUpdate($data, $id);
    }

    // Abstract methods
    abstract protected function validate(array $data);
    abstract protected function save(array $data);

    abstract protected function validateUpdate(array $data, $id);
    abstract protected function saveUpdate(array $data, $id);
}
