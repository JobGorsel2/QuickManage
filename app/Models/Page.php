<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    // use HasFactory;
    protected $table = 'pages';
     public function template()
    {
        return $this->belongsTo(Template::class, 'template_id', 'id');
    }
     public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id', 'id');
    }
}
