<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statu extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public function getTableTypes( $databaseTableFieldname = '') {
        if( !empty( $databaseTableFieldname ) ){
            return $this->getConnection()->getDoctrineColumn($this->getTable(), $databaseTableFieldname)->getType()->getName();
        }
    }
}
