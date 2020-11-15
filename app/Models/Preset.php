<?php

namespace App\Models;

use App\Contracts\InteractsWithSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preset extends Model
{
    use HasFactory;
    use InteractsWithSettings;
}
