# TODO: Fix "Error marking book as returned" issue

## Completed Tasks

- [x] Identified that the error occurs in `public/librarian/links/borrowedList.php` (not bookList.php as mentioned by user)
- [x] Enhanced error handling in `back-end/update/markAsReturned.php` to provide more descriptive error messages
- [x] Updated JavaScript in `borrowedList.php` to parse and display specific error messages from the server

## Summary of Changes

- Modified `markAsReturned.php` to return detailed error messages instead of generic 'error'
- Updated the fetch response handler in `borrowedList.php` to display specific error messages to the user

## Next Steps

- Test the mark as returned functionality to ensure errors are now properly reported
- If issues persist, check database connection and table structures
