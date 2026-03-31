<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    /** @use HasFactory<\Database\Factories\EvidenceFactory> */
    use HasFactory;

    protected $table = 'evidences';
    
    protected $fillable = [
        'path',
        'overtime_id',
    ];

    public function overtime()
    {
        return $this->belongsTo(Overtime::class);
    }
}
