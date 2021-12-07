# php-mysql-multi-threading
 
This code implements true _multi-threading of MySQL queries in PHP_. By adding the MySQL queries to `$queries` they will be executed with `db_thread()`. This will parallelize the query execution and finish the moment the longest query has been finalized. Afterwards the query results are stored in `$db_threads` and might be used.

## Example ##

Sequential execution (total 12s):

1. Query 1 (4s)
2. Query 2 (2s)
3. Query 3 (6x)

Parallelized execution (total 6s):

1. Query 1 + Query 2 + Query 3 (6s)
