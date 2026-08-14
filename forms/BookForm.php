<?php

namespace app\forms;

use app\models\Author;
use app\models\Book;
use app\models\BookAuthor;
use app\services\S3Storage;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\web\UploadedFile;

class BookForm extends Model
{
    /** @var string */
    public $title = '';

    /** @var int|string|null */
    public $release_year = null;

    /** @var string */
    public $description = '';

    /** @var string */
    public $isbn = '';

    /** @var array */
    public $authorIds = [];

    public ?UploadedFile $coverFile = null;

    private Book $book;

    public function __construct(?Book $book = null, array $config = [])
    {
        $this->book = $book ?? new Book();

        if (!$this->book->isNewRecord) {
            $this->title = $this->book->title;
            $this->release_year = $this->book->release_year;
            $this->description = $this->book->description;
            $this->isbn = $this->book->isbn;
            $this->authorIds = array_map(static fn (Author $author): int => $author->id, $this->book->authors);
        }

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['title', 'description', 'isbn'], 'trim'],
            [['title', 'release_year', 'description', 'isbn', 'authorIds'], 'required'],
            [['release_year'], 'integer', 'min' => 1000, 'max' => (int) date('Y') + 1],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 32],
            [['isbn'], 'unique', 'targetClass' => Book::class, 'filter' => $this->uniqueIsbnFilter()],
            [['authorIds'], 'each', 'rule' => ['integer']],
            [['authorIds'], 'validateAuthors'],
            [['coverFile'], 'file', 'extensions' => ['jpg', 'jpeg', 'png', 'webp'], 'maxSize' => 5 * 1024 * 1024, 'skipOnEmpty' => !$this->book->isNewRecord],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Название',
            'release_year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'authorIds' => 'Авторы',
            'coverFile' => 'Фото главной страницы',
        ];
    }

    /**
     * Проверяет, что выбраны существующие авторы.
     *
     * @param string $attribute
     * @return void
     */
    public function validateAuthors(string $attribute): void
    {
        $ids = $this->normalizedAuthorIds();
        $count = Author::find()->where(['id' => $ids])->count();

        if ($ids === [] || (int) $count !== count($ids)) {
            $this->addError($attribute, 'Выберите существующих авторов.');
        }
    }

    /**
     * Сохраняет книгу, обложку и связи с авторами.
     *
     * @param S3Storage $storage
     * @return Book|null
     * @throws Exception
     */
    public function save(S3Storage $storage): ?Book
    {
        $this->coverFile = UploadedFile::getInstance($this, 'coverFile');

        if (!$this->validate()) {
            return null;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $this->book->setAttributes([
                'title' => $this->title,
                'release_year' => $this->release_year,
                'description' => $this->description,
                'isbn' => $this->isbn,
            ]);

            if ($this->coverFile !== null) {
                $this->book->cover_url = $storage->upload($this->coverFile, 'covers');
            }

            if (!$this->book->save()) {
                $this->addErrors($this->book->getErrors());
                $transaction->rollBack();

                return null;
            }

            $this->syncAuthors();
            $transaction->commit();

            return $this->book;
        } catch (\Throwable $exception) {
            $transaction->rollBack();
            $this->addError('coverFile', $exception->getMessage());

            return null;
        }
    }

    /**
     * Возвращает редактируемую книгу.
     *
     * @return Book
     */
    public function getBook(): Book
    {
        return $this->book;
    }

    private function syncAuthors(): void
    {
        BookAuthor::deleteAll(['book_id' => $this->book->id]);
        $rows = array_map(
            fn (int $authorId): array => [$this->book->id, $authorId],
            $this->normalizedAuthorIds()
        );

        Yii::$app->db->createCommand()
            ->batchInsert(BookAuthor::tableName(), ['book_id', 'author_id'], $rows)
            ->execute();
    }

    private function normalizedAuthorIds(): array
    {
        $ids = array_map('intval', $this->authorIds);
        $ids = array_filter($ids, static fn (int $id): bool => $id > 0);

        return array_values(array_unique($ids));
    }

    private function uniqueIsbnFilter(): callable
    {
        return function ($query): void {
            if (!$this->book->isNewRecord) {
                $query->andWhere(['<>', 'id', $this->book->id]);
            }
        };
    }
}
