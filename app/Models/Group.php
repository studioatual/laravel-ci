<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $table = 'groups';
    protected $fillable = [
        'code',
        'name',
        'cnpj',
        'type',
        'active'
    ];

    public function persons()
    {
        return $this->hasMany(Person::class);
    }
}
