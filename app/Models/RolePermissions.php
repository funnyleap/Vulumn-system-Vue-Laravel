<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;

use App\Traits\GlobalRelations;


/**
 * @property bigIncrements $id 
 * @property unsignedBigInteger $permission_id 
 * @property unsignedBigInteger $role_id 

 */
class RolePermissions extends Model
{
    use Authenticatable, Authorizable, HasFactory;
    use SoftDeletes, GlobalRelations;

    /**
     * Table Configuration
     * @var string
     */
    protected $table = 'role_permissions';
    protected $primaryKey = 'id';

    /**
     * List of allowed column to insert / update 
     * @var array
     */
    protected $fillable = [
        'permission_id', 
        'role_id', 
        'created_by', 
        'updated_by', 
        'deleted_by'
    ];

    // disabled timestamps data
    public $timestamps = true;

    // disable update col id
    protected $guarded = ['id'];

    protected $casts = [ 
        'permission_id' => 'integer',
        'role_id' => 'integer',

    ];

    public function Columns() {
        return $this->fillable;
    }

    public function Permissions()
    {
        return $this->belongsTo('App\Models\Permissions', 'permission_id', 'id');
    }

}        
        