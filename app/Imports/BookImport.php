<?php

namespace App\Imports;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookLocation;
use App\Models\BookStock;
use App\Models\BookType;
use App\Models\Publisher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BookImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // return new Book([
        //     'book_name' => $row['book_name'],
        //     'book_isbn' => $row['book_isbn'],
        //     'book_publish_year' => $row['book_publish_year'],
        //     'publisher_id' => $this->findPublisher($row['publisher_id']),
        //     'book_cover' => $row['book_cover'],
        // ]);
        
        $book = Book::firstOrCreate(
            [
                'book_name' => $row['book_name'],
                'book_isbn' => $row['book_isbn'],
                'book_publish_year' => $row['book_publish_year'],
                'publisher_id' => $this->findPublisher($row['publisher_id']),
            ],
            [
                'book_cover' => $row['book_cover'],
            ]
        );

        // 
        $explodeAuthor = explode("&", $row['author']);
        $authorIds = [];
        for ($i=0; $i < count($explodeAuthor); $i++) { 
            $author = Author::firstOrCreate(
                [
                    'author_name' => $row['author'],
                ]
            );

            $authorIds[] = $author->id;
        }

        $book->authors()->sync($authorIds);


        // 
        $explodeBookType = explode("&", $row['book_type']);
        $bookTypeIds = [];
        for ($i=0; $i < count($explodeBookType); $i++) { 
            $bookType = BookType::firstOrCreate(
                [
                    'book_type_name' => $row['book_type'],
                ]
            );

            $bookTypeIds[] = $bookType->id;
        }

        $book->bookTypes()->sync($bookTypeIds);

        // 
        $bookLocation = BookLocation::firstOrCreate(
            [
                'book_location_name' => $row['book_location_name'],
                'book_location_label' => $row['book_location_label'],
            ]
        );

        $bookStock = BookStock::firstOrCreate(
            [
                'book_location_id' => $bookLocation->id,
                'book_id' => $book->id,
                'qty' => $row['book_stock'],
            ]
        );


        return $book;
    }

    public function findPublisher($data){
        $returnData = Publisher::firstOrCreate(
            [
                'publisher_name' => $data,
            ]
        );

        return $returnData->id;
    }
}
