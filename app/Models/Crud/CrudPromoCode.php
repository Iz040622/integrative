<?php

namespace App\Models\Crud;

use App\Models\Crud\CrudTemplate;
use Illuminate\Support\Facades\Validator;

class CrudPromoCode extends CrudTemplate
{
    protected function validate(array $data)
    {
        Validator::make($data, [
            'promoteCode' => 'required|unique:promo_code,promoteCode',
            'description' => 'required',
            'discount' => 'required|numeric|min:0|max:100',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ])->validate();
    }


    protected function save(array $data)
    {
        return \App\Models\PromoCode::create($data);
    }

    protected function validateUpdate(array $data, $id)
    {
        Validator::make($data, [
            'promoteCode' => 'required|unique:promo_code,promoteCode,' . $id,
            'description' => 'required',
            'discount' => 'required|numeric|min:0|max:100',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ])->validate();
    }

    protected function saveUpdate(array $data, $id)
    {
        $promo = \App\Models\PromoCode::findOrFail($id);
        $promo->update($data);
        return $promo;
    }
}
