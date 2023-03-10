<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Response;

class Report extends Model
{
    use HasFactory;
    protected $fillable = [
        'nik',
        'nama',
        'no_telp',
        'pengaduan',
        'foto',
    ];
    // hasOne : one to one
    // table yang berperan sebagai PK (PrimaryKey)
    // nama fungsi == nama model FK (ForeignKey)
    public function response() 
    {
        return $this->hasOne(response::class);
    }
}
