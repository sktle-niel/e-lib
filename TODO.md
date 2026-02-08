# TODO: Add Month Filter to Returned Books History

## Backend Changes

- [x] Modify `getReturnedBooksHistory($limit, $offset, $month = null)` in `back-end/read/returnedBookHistory.php` to add optional `$month` parameter and filter by `MONTH(actual_return_date) = ?` if provided.
- [x] Modify `getReturnedBooksHistoryCount($month = null)` in `back-end/read/returnedBookHistory.php` to add optional `$month` parameter and filter by `MONTH(actual_return_date) = ?` if provided.

## Frontend Changes

- [x] Add a form with month select dropdown above the table in `public/librarian/links/history.php`.
- [x] Update PHP code in `public/librarian/links/history.php` to read `$_GET['month']` and pass it to backend functions.
- [x] Ensure pagination links in `public/librarian/links/history.php` include the month parameter if set.

## Testing

- [x] Test the filter: Select different months and verify only returns from that month are shown across all years.
- [x] Test pagination: Ensure page counts and navigation work with the filter applied.
- [x] Handle cases where no returns exist for a selected month.
