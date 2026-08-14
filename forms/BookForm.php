<?php

declare(strict_types=1);

namespace app\forms;

use app\models\Author;
use app\models\Book;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Форма создания и редактирования книги.
 */
class BookForm extends Model
{
    public string $title = '';

    public int|string|null $release_year = null;

    public string $description = '';

    public string $isbn = '';

    /** @var list<int|string> */
    public array $authorIds = [];

    public ?UploadedFile $coverFile = null;

    private Book $book;

    /**
     * @param Book|null $book
     * @param array<string, mixed> $config
     */
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

    /**
     * Правила валидации.
     *
     * @return array<int, array<mixed>>
     */
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

    /**
     * Подписи полей.
     *
     * @return array<string, string>
     */
    public function attributeLabels(): array
    {
        return [
            'title' => Yii::t('app', 'Название'),
            'release_year' => Yii::t('app', 'Год выпуска'),
            'description' => Yii::t('app', 'Описание'),
            'isbn' => 'ISBN',
            'authorIds' => Yii::t('app', 'Авторы'),
            'coverFile' => Yii::t('app', 'Фото главной страницы'),
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
            $this->addError($attribute, Yii::t('app', 'Выберите существующих авторов.'));
        }
    }

    /**
     * Возвращает атрибуты книги для сохранения.
     *
     * @return array{title: string, release_year: int|string|null, description: string, isbn: string}
     */
    public function bookAttributes(): array
    {
        return [
            'title' => $this->title,
            'release_year' => $this->release_year,
            'description' => $this->description,
            'isbn' => $this->isbn,
        ];
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

    /**
     * Загружает файл обложки из текущего request.
     *
     * @return void
     */
    public function loadCoverFile(): void
    {
        $this->coverFile = UploadedFile::getInstance($this, 'coverFile');
    }

    /**
     * Возвращает нормализованный список ID авторов.
     *
     * @return list<int>
     */
    public function normalizedAuthorIds(): array
    {
        $ids = array_map('intval', $this->authorIds);
        $ids = array_filter($ids, static fn (int $id): bool => $id > 0);

        return array_values(array_unique($ids));
    }

    /**
     * Возвращает фильтр уникальности ISBN для текущей книги.
     *
     * @return callable
     */
    private function uniqueIsbnFilter(): callable
    {
        return function ($query): void {
            if (!$this->book->isNewRecord) {
                $query->andWhere(['<>', 'id', $this->book->id]);
            }
        };
    }
}
