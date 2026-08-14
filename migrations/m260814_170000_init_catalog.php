<?php

use yii\db\Migration;

class m260814_170000_init_catalog extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(64)->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%author}}', [
            'id' => $this->primaryKey(),
            'full_name' => $this->string(255)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'release_year' => $this->smallInteger()->notNull(),
            'description' => $this->text()->notNull(),
            'isbn' => $this->string(32)->notNull()->unique(),
            'cover_url' => $this->string(1024)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%book_author}}', [
            'book_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('pk-book_author', '{{%book_author}}', ['book_id', 'author_id']);
        $this->addForeignKey(
            'fk-book_author-book_id',
            '{{%book_author}}',
            'book_id',
            '{{%book}}',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-book_author-author_id',
            '{{%book_author}}',
            'author_id',
            '{{%author}}',
            'id',
            'CASCADE'
        );

        $this->createTable('{{%subscription}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'phone' => $this->string(32)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-author-full_name', '{{%author}}', 'full_name');
        $this->createIndex('idx-book-release_year', '{{%book}}', 'release_year');
        $this->createIndex('idx-book_author-author_id', '{{%book_author}}', 'author_id');
        $this->createIndex('idx-subscription-author-phone', '{{%subscription}}', ['author_id', 'phone'], true);
        $this->addForeignKey(
            'fk-subscription-author_id',
            '{{%subscription}}',
            'author_id',
            '{{%author}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%subscription}}');
        $this->dropTable('{{%book_author}}');
        $this->dropTable('{{%book}}');
        $this->dropTable('{{%author}}');
        $this->dropTable('{{%user}}');
    }
}
