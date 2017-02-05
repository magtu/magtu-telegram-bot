<?php

namespace LArtie\MagtuTelegramBot\Core;

use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package LArtie\MagtuTelegramBot\Core
 * @property int $id
 * @property int $group_id
 * @property string $first_name
 * @property string $last_name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 **/
final class User extends Model
{
    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var string
     */
    protected $connection = 'magtu';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'group_id',
    ];
}