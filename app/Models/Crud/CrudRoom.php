<?php

namespace App\Models\Crud;

use App\Models\Crud\CrudTemplate;
use Illuminate\Support\Facades\Validator;

class CrudRoom extends CrudTemplate
{
    protected function validate(array $data)
    {
        $validator = Validator::make($data, [
            'roomNo' => 'required|unique:rooms,roomNo',
            'room_type_id' => 'required|exists:room_types,id',
        ])->validate();;
    }

    protected function save(array $data)
    {
        $data['availability'] = 1;
        return \App\Models\Room::create($data);
    }

    protected function validateUpdate(array $data, $id)
    {
        $validator = Validator::make($data, [
            'roomNo' => 'required|unique:rooms,roomNo,' . $id,
            'availability' => 'required|in:0,1',
            'room_type_id' => 'required|exists:room_types,id',
        ])->validate();;
    }

    protected function saveUpdate(array $data, $id)
    {
        $room = \App\Models\Room::findOrFail($id);
        $room->update($data);
        return $room;
    }
}
