<?php

use Phinx\Migration\AbstractMigration;

class Format extends AbstractMigration
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
    public function change()
    {
        $this->table("format")
            ->addColumn("name", "string", ["limit" => 100])
            ->addColumn("collection", "string", ["limit" => 255])
            ->addColumn("template_id", "integer")
            ->create();

        $this->table("template")
            ->addColumn("name", "string", ["limit" => 100])
            ->create();

        $this->table("field")
            ->addColumn("name", "string", ["limit" => 255])
            ->addColumn("field_name", "string", ["limit" => 255])
            ->addColumn("type", "string", ["limit" => 50])
            ->addColumn("format_id", "integer", ["null" => true])
            ->addColumn("template_id", "integer", ["null" => true])
            ->create();
    }
}
