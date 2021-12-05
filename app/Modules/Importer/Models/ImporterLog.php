<?php

namespace App\Modules\Importer\Models;

use App\Core\Old\TableFixTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property BigInteger           id
 * @property string               type
 * @property dateTime             run_at
 * @property integer              entries_processed
 * @property integer              entries_created
 */

class ImporterLog extends Model
{
    protected $table = 'importer_log';
    public $timestamps = false;

    protected $fillable = [];
}
