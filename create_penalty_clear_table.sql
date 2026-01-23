-- SQL command to create penalty_clear_log table
CREATE TABLE IF NOT EXISTS penalty_clear_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    borrow_id INT NOT NULL,
    book_id INT NOT NULL,
    user_id INT NOT NULL,
    penalty_amount DECIMAL(10,2) NOT NULL,
    days_overdue INT NOT NULL,
    cleared_by INT NOT NULL,
    cleared_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (borrow_id) REFERENCES borrowed_lib_books(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES lib_books(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (cleared_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Add index for better performance
CREATE INDEX idx_penalty_clear_borrow_id ON penalty_clear_log(borrow_id);
CREATE INDEX idx_penalty_clear_cleared_at ON penalty_clear_log(cleared_at);
