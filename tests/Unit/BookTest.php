<?php

namespace Tests\Unit;

use Tests\TestCase;

class BookTest extends TestCase
{
    /**
     * A basic unit test book list.
     *
     * @return void
     */
    public function test_book_list()
    {
        $response = $this->get('/books');

        $response->assertStatus(200);
    }
    
    /**
     * A basic unit test book create.
     *
     * @return void
     */
    public function test_book_create_form()
    {
        $response = $this->get('/books/create');

        $response->assertStatus(200);
    }
    
    /**
     * A basic unit test book edit.
     *
     * @return void
     */
    public function test_book_edit_form()
    {
        $response = $this->get('/books/edit');

        $response->assertStatus(200);
    }
    
    /**
     * A basic unit test book table.
     *
     * @return void
     */
    public function test_book_table()
    {
        $response = $this->get('/books/table');

        $response->assertStatus(200);
    }
    
    /**
     * A basic unit test book detail.
     *
     * @return void
     */
    public function test_book_detail()
    {
        $response = $this->get('/books/detail');

        $response->assertStatus(200);
    }
}
