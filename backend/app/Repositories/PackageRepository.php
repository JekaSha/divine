<?php

namespace App\Repositories;

use App\Models\Package;

class PackageRepository
{
    public function get(array $filter = [])
    {
        $query = Package::query();

        foreach ($filter as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query->get();
    }


    public function getById($id)
    {
        return Package::find($id);
    }
}
