# TODO: Add Borrower Full Name to Returned Books History

- [x] Update `getReturnedBooksHistory` function in `back-end/read/returnedBookHistory.php` to join with `users` table and select full name as `borrower_name`
- [x] Add "Borrower Name" column to table header in `public/librarian/links/history.php`
- [x] Display borrower name in table rows in `public/librarian/links/history.php`
- [x] Update colspan for "No returned books history found" message to 8
