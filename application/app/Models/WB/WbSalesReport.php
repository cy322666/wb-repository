<?php

namespace App\Models\WB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WbSalesReport extends Model
{
    use HasFactory;

    protected $table = 'general';

    public $timestamps = false;
}
