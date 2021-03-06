<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Kanban\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Kanban\Models;

use Modules\Admin\Models\AccountMapper;
use Modules\Tag\Models\TagMapper;
use phpOMS\DataStorage\Database\DataMapperAbstract;

/**
 * Mapper class.
 *
 * @package Modules\Kanban\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class KanbanBoardMapper extends DataMapperAbstract
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    protected static array $columns = [
        'kanban_board_id'            => ['name' => 'kanban_board_id',         'type' => 'int',      'internal' => 'id'],
        'kanban_board_name'          => ['name' => 'kanban_board_name',       'type' => 'string',   'internal' => 'name'],
        'kanban_board_desc'          => ['name' => 'kanban_board_desc',       'type' => 'string',   'internal' => 'description'],
        'kanban_board_descraw'       => ['name' => 'kanban_board_descraw',       'type' => 'string',   'internal' => 'descriptionRaw'],
        'kanban_board_status'        => ['name' => 'kanban_board_status',     'type' => 'int',      'internal' => 'status'],
        'kanban_board_order'         => ['name' => 'kanban_board_order',      'type' => 'int',      'internal' => 'order'],
        'kanban_board_style'         => ['name' => 'kanban_board_style',       'type' => 'string',   'internal' => 'style'],
        'kanban_board_created_by'    => ['name' => 'kanban_board_created_by', 'type' => 'int',      'internal' => 'createdBy', 'readonly' => true],
        'kanban_board_created_at'    => ['name' => 'kanban_board_created_at', 'type' => 'DateTimeImmutable', 'internal' => 'createdAt', 'readonly' => true],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    protected static array $hasMany = [
        'columns' => [
            'mapper'       => KanbanColumnMapper::class,
            'table'        => 'kanban_column',
            'self'         => 'kanban_column_board',
            'external'     => null,
        ],
        'tags' => [
            'mapper'   => TagMapper::class,
            'table'    => 'kanban_board_tag',
            'self'     => 'kanban_board_tag_dst',
            'external' => 'kanban_board_tag_src',
        ],
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:string, external:string}>
     * @since 1.0.0
     */
    protected static array $belongsTo = [
        'createdBy' => [
            'mapper'     => AccountMapper::class,
            'external'   => 'kanban_board_created_by',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $table = 'kanban_board';

    /**
     * Created at.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $createdAt = 'kanban_board_created_at';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $primaryField = 'kanban_board_id';
}
