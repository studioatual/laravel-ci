<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;
    protected $table = 'persons';
    protected $fillable = [
        'group_id',
        'code',
        'company',
        'name',
        'cpf_cnpj',
        'rg_ie'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
