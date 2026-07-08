const sqlite3 = require('sqlite3').verbose();
const db = new sqlite3.Database('./database.sqlite');

db.serialize(() => {
    // Users table
    db.run(`CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        email TEXT UNIQUE,
        password TEXT
    )`);

    // Businesses table
    db.run(`CREATE TABLE IF NOT EXISTS businesses (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        name TEXT,
        category TEXT,
        description TEXT,
        phone TEXT,
        email TEXT,
        address TEXT,
        logo TEXT,
        slug TEXT UNIQUE,
        theme TEXT DEFAULT 'modern',
        FOREIGN KEY (user_id) REFERENCES users (id)
    )`);

    // Products table
    db.run(`CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        business_id INTEGER,
        name TEXT,
        price TEXT,
        description TEXT,
        image TEXT,
        FOREIGN KEY (business_id) REFERENCES businesses (id)
    )`);
});

module.exports = db;
