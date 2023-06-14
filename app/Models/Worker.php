<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Worker extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['first_name', 'last_name'];

    public function rules() {
        return [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:120',
            'image' => 'required|file|mimes:jpeg,jpg,png',
        ];
    }

    public function feedback() {
        return [
            'required' => 'The :attribute is required',
            'image' => 'Worker photo should be an image file'
        ];
    }

}
