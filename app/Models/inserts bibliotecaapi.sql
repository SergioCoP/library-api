{
    "isbn":"  613615 25043  ",
    "title":"Programming from scratch",
    "description":"Programming book",
    "category":{
        "id":1
    },
    "editorial":{
        "id":1
    },
    "authors":[
        {
            "id":1
        },
        {
            "id":4
        }
    ]
}

  {
      "id": 1,
      "name": "Harvey",
      "firstname": "Deitel",
      "surname": null
    }

use biblioteca;
INSERT INTO editorials(id,name) VALUES(1,"Pearson");
INSERT INTO editorials(id,name) VALUES(2,"Alfaomega");
INSERT INTO editorials(id,name) VALUES(3,"McGraw Hill");

INSERT INTO categories(id,name) VALUES(1,"Computing");

INSERT INTO books(id,isbn,title,description,published_date,
category_id,editorial_id)
VALUES
(1,"0136152503","How to program C++","Programming book","2005-12-21",1,1),
(2,"0136152921","Metodología de la programación","Programming book","2001-04-12",1,2),
(3,"0136136791","Fundamentos de programación","Programming book","2008-04-12",1,3);

INSERT INTO authors(id,name,firstname,surname)
VALUES
(1,"Harvey","Deitel","Deitel"),
(2,"Paul","Deitel","Deitel"),
(3,"Osvaldo","Cairó","Cairó"),
(4,"Luis","Joyanes","Joyanes"),
(5,"Ignacio","Zahonero","Zahonero");

INSERT INTO authors_books(authors_id,books_id) VALUES(1,1);
INSERT INTO authors_books(authors_id,books_id) VALUES(2,1);
INSERT INTO authors_books(authors_id,books_id) VALUES(3,2);
INSERT INTO authors_books(authors_id,books_id) VALUES(4,3);
INSERT INTO authors_books(authors_id,books_id) VALUES(5,3);

INSERT INTO book_downloads(total_downloads,book_id) VALUES(26,1);
INSERT INTO book_downloads(total_downloads,book_id) VALUES(6,1);
INSERT INTO book_downloads(total_downloads,book_id) VALUES(15,1);
