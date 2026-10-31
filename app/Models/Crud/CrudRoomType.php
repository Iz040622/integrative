<?php

namespace App\Models\Crud;

use App\Models\RoomType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CrudRoomType extends CrudTemplate
{
    protected function validate(array $data)
    {
        Validator::make($data, [
            'name'          => 'required|string|max:100|unique:room_types,name',
            'description'   => 'required|string|max:255',
            'roomAmenities' => 'nullable|string',
            'paxNo'         => 'required|integer|min:1',
            'price'         => 'required|numeric|min:0',
            'imageUrl' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ])->validate();
    }

    protected function save(array $data)
    {
        if (!empty($data['roomAmenities'])) {
            $data['roomAmenities'] = array_map('trim', explode(',', $data['roomAmenities']));
        }

        if (isset($data['imageUrl']) && $data['imageUrl'] instanceof \Illuminate\Http\UploadedFile) {
            // MIME check
            if (!in_array($data['imageUrl']->getMimeType(), ['image/jpeg', 'image/png'])) {
                throw new \Exception("Invalid image type");
            }

            $data['imageUrl'] = $data['imageUrl']->store('images', 'public');
        }

        $data['availability'] = 1;

        return RoomType::create($data);
    }

    protected function validateUpdate(array $data, $id)
    {
        Validator::make($data, [
            'name'          => 'required|string|max:100|unique:room_types,name,' . $id,
            'description'   => 'required|string|max:255',
            'roomAmenities' => 'nullable|string',
            'paxNo'         => 'required|integer|min:1',
            'price'         => 'required|numeric|min:0',
            'imageUrl' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'availability'  => 'required|in:0,1',
        ])->validate();
    }

    protected function saveUpdate(array $data, $id)
    {
        $roomType = RoomType::findOrFail($id);

        if (!empty($data['roomAmenities'])) {
            $data['roomAmenities'] = array_map('trim', explode(',', $data['roomAmenities']));
        }

        if (isset($data['imageUrl']) && $data['imageUrl'] instanceof \Illuminate\Http\UploadedFile) {
            // MIME check
            if (!in_array($data['imageUrl']->getMimeType(), ['image/jpeg', 'image/png'])) {
                throw new \Exception("Invalid image type");
            }

            // Delete old image safely
            if ($roomType->imageUrl) {
                Storage::disk('public')->delete($roomType->imageUrl);
            }
            // Store new image
            $data['imageUrl'] = $data['imageUrl']->store('images', 'public');
        } else {
            unset($data['imageUrl']);
        }

        $roomType->update($data);
        return $roomType;
    }
}
