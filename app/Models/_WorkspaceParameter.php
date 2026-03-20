<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkspaceParameter extends Model
{
    protected $table = 'workspaceparameters';

     public function page()
    {
        return $this->belongsTo(Page::class, 'page_id', 'id');
    }
}
