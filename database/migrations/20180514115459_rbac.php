<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class Rbac extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        $table = $this->table('roles',['comment'=>'角色表']);
        $table->addColumn('name', 'string', ['limit' => 20,'default'=>''])
            ->addColumn('description', 'string', ['null' => true])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->addIndex(['name'], ['unique' => true])
            ->save();

        $table = $this->table('role_user',['comment'=>'用户角色表']);
        $table->addColumn('user_id', 'integer', ['signed' => true])
            ->addColumn('role_id', 'integer', ['signed' => true])
            ->addForeignKey('user_id','admins','id',['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addForeignKey('role_id','roles','id',['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addIndex(['user_id','role_id'])
            ->save();

        $table = $this->table('permissions',['comment'=>'权限表']);
        $table->addColumn('name', 'string', ['default'=>''])
            ->addColumn('description', 'string', ['null' => true])
            ->addColumn('level_name', 'string', ['default'=>'','comment'=>'级别递归名称'])
            ->addColumn('level_id', 'string', ['default'=>'','comment'=>'级别递归id'])
            ->addColumn('icon', 'string', ['default'=>'','comment'=>'权限图标'])
            ->addColumn('level', 'integer', ['default'=>0,'comment'=>'级别'])
            ->addColumn('sort_order', 'integer', ['default'=>0,'comment'=>'排序'])
            ->addColumn('display_menu', 'integer', ['limit' => MysqlAdapter::INT_TINY,'default'=>0])
            ->addColumn('parent_id', 'integer',['default'=>0])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->addIndex(['name'], ['unique' => true])
            ->addIndex(['parent_id'])
            ->save();

        $table = $this->table('permission_role',['comment'=>'权限角色表']);
        $table->addColumn('permission_id', 'integer', ['signed' => true])
            ->addColumn('role_id', 'integer', ['signed' => true])
            ->addForeignKey('permission_id','permissions','id',['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addForeignKey('role_id','roles','id',['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addIndex(['permission_id','role_id'])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('role_user');
        $this->dropTable('permission_role');
        $this->dropTable('roles');
        $this->dropTable('permissions');

    }
}
