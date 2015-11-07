# DataDo Data [![Latest Stable Version](https://poser.pugx.org/datado/data/v/stable)](https://packagist.org/packages/datado/data) [![Total Downloads](https://poser.pugx.org/datado/data/downloads)](https://packagist.org/packages/datado/data) [![Latest Unstable Version](https://poser.pugx.org/datado/data/v/unstable)](https://packagist.org/packages/datado/data) [![License](https://poser.pugx.org/datado/data/license)](https://packagist.org/packages/datado/data)
DataDo Data is a way of easily communicating with a database without having to write sql queries. 

# How?
DataDo Data works through repositories. This means that for a certain collection (usually a SQL table) you would create
a repository to interact with it. Now, I hear your concerns and you're asking yourself 'But if I do not have to write
sql, how do I get my data from the database?'. That's where the trick comes in.

# Getting Started

```php
    
    /**
     * This is the entity we are going to use in this example.
     */
    class File {
        public $id;
        public $filePath;
        public $size;
        public $fileName;
    }
    
    // First we will have to connect to out database
    $pdo = new PDO('mysql:host=localhost;dbname=demo', 'username', 'secret_password');
    
    /* Then we create the repository we mentioned earlier. To be able to do this we need 
     * some information:
     * 
     * - The class of the entity we want to store or get from this repository
     * - The PDO connection to the database
     * - The name of the property that identifies this entity. This will be used when 
     *     updating and inserting objects
     */
    $repository = new Repository(File::class, $pdo, 'id');
```

## Using the Analysis Tool
Of course right now this is not going to work. We didn't even create any tables in our database. To help us do that
the repository has a convenient tool. Run it like this:

```php

    $repository->checkDatabase();
```

If you view this output in your browser you should see an overview of the connection between your class and the database.
Most of the *Properties* rows are read because you haven't created the table or columns. You can use this overview as
a reference when doing that.

## Inserting Data
So now we've created our database and tables we're ready to insert some data.

```php

    // Let's create a file object for this current script
    $file = new File();
    $file->filePath = __FILE__;
    $file->size = filesize(__FILE__);
    $file->fileName = basename(__FILE__);
    
    // Now we can use the repository we created earlier to save this file to the database.
    $repository->save($file);
    
    // Now note that this file has been assigned an id
    echo $file->id;
```

## Getting Data
Getting data from the database is a little bit more tricky. However, this is also where the strength of the repository is.

### EQL
DataDo works using the **Easiest Querying Language** this is a simple querying mechanism that is developed for this
project. It works by calling methods on the repository with a certain name.

```php

    // Here's a simple example
    $myFile = $repository->getByFilePath(__FILE__);
    
    // Let's print the result
    var_dump($myFile);
```

If you ran the code above in the same file as the code from section *Inserting Data* then you should have received that
object you just created back from the database. The way this works is you can call non-existing methods on the repository
as long as you stick to a certain syntax: (get, find or delete), optional: (fields), optional: By(fields).

Let's take a look at some more examples.

```php

    // The 'find' query will return a list of all entities that match your query
    $files = $repository->findByFileName('index.php');
    
    /* You can also request only specific fields. You will then still get a File 
     * object but only the requested fields will be filled out.
     */
    $paths = $repository->findFilePathByFileName();
    
    /* The 'get' query if very much like the 'find' query except this query will 
     * only return the first match.
     */
    $myFile = $repository->getFileByFileSizeAndFileNameLike(1032, '%.php');
     
     /* Then finally there is the 'delete' query. This query does not accept the
      * selection fields. (The bit before the By keyword). It will delete all
      * matches of the query.
      * The return value of this query is the number of affected rows.
      */
     
     // Delete all files
     echo $repository->delete(); // Or $repository->deleteAll();
     
     // Delete specific files
     $repository->deleteById(235);
```

#### Keywords

 - **delete** - Create a query that will delete all matches
 - **find**   - Create a query that will return a list of matches
 - **get**    - Create a query that will return a single match
 - **By**     - Marks the start of the filter section
 - Filter: **And**    - Represents the boolean *and* operator for two filters
 - Filter: **Or**     - Represents the boolean *or*  operator for two filters
 - Filter: **Like**   - Represents the SQL   *LIKE*  operator. This takes an argument.

#### Property Projection
To only fetch certain properties you can follow the query mode (delete, find or get) by the properties you want to get
separated by the And keyword.

**Note that you must capitalize the first letter of the field names you need.**

```php

$repository->getIdAndFilePathAndFileSize();
```

# License
This project is licensed under the MIT license

    Copyright (c) 2015 Thomas Biesaart
    
    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:
    
    The above copyright notice and this permission notice shall be included in all
    copies or substantial portions of the Software.
    
    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
    SOFTWARE.