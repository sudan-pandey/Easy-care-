const express = require('express');
const session = require('express-session');
const multer = require('multer');
const path = require('path');
const bcrypt = require('bcryptjs');
const db = require('./db');

const app = express();
const port = 3000;

// Middleware
app.set('view engine', 'ejs');
app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(express.static('public'));
app.use('/uploads', express.static('uploads'));
app.use(session({
    secret: 'business-builder-secret',
    resave: false,
    saveUninitialized: false
}));

// Multer Setup for File Uploads
const storage = multer.diskStorage({
    destination: './uploads/',
    filename: (req, file, cb) => {
        cb(null, Date.now() + path.extname(file.originalname));
    }
});
const upload = multer({ storage });

// Routes
app.get('/', (req, res) => {
    res.render('index', { user: req.session.user });
});

// Auth Routes
app.get('/register', (req, res) => res.render('register'));
app.post('/register', async (req, res) => {
    const { name, email, password } = req.body;
    const hashedPassword = await bcrypt.hash(password, 10);
    db.run('INSERT INTO users (name, email, password) VALUES (?, ?, ?)', [name, email, hashedPassword], (err) => {
        if (err) return res.send('Email already exists');
        res.redirect('/login');
    });
});

app.get('/login', (req, res) => res.render('login'));
app.post('/login', (req, res) => {
    const { email, password } = req.body;
    db.get('SELECT * FROM users WHERE email = ?', [email], async (err, user) => {
        if (user && await bcrypt.compare(password, user.password)) {
            req.session.user = user;
            res.redirect('/dashboard');
        } else {
            res.send('Invalid credentials');
        }
    });
});

app.get('/logout', (req, res) => {
    req.session.destroy();
    res.redirect('/');
});

// Dashboard & Business Management
app.get('/dashboard', (req, res) => {
    if (!req.session.user) return res.redirect('/login');
    db.get('SELECT * FROM businesses WHERE user_id = ?', [req.session.user.id], (err, business) => {
        if (business) {
            db.all('SELECT * FROM products WHERE business_id = ?', [business.id], (err, products) => {
                res.render('dashboard', { user: req.session.user, business, products });
            });
        } else {
            res.render('dashboard', { user: req.session.user, business: null, products: [] });
        }
    });
});

app.post('/business', upload.single('logo'), (req, res) => {
    if (!req.session.user) return res.redirect('/login');
    const { name, category, description, phone, email, address, slug } = req.body;
    const logo = req.file ? req.file.filename : null;

    db.get('SELECT id FROM businesses WHERE user_id = ?', [req.session.user.id], (err, row) => {
        if (row) {
            // Update
            db.run('UPDATE businesses SET name=?, category=?, description=?, phone=?, email=?, address=?, slug=?, logo=COALESCE(?, logo) WHERE user_id=?',
                [name, category, description, phone, email, address, slug, logo, req.session.user.id], () => res.redirect('/dashboard'));
        } else {
            // Insert
            db.run('INSERT INTO businesses (user_id, name, category, description, phone, email, address, slug, logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [req.session.user.id, name, category, description, phone, email, address, slug, logo], () => res.redirect('/dashboard'));
        }
    });
});

app.post('/product', upload.single('image'), (req, res) => {
    if (!req.session.user) return res.redirect('/login');
    const { name, price, description } = req.body;
    const image = req.file ? req.file.filename : null;

    db.get('SELECT id FROM businesses WHERE user_id = ?', [req.session.user.id], (err, business) => {
        if (!business) return res.status(403).send('No business found for this user');
        db.run('INSERT INTO products (business_id, name, price, description, image) VALUES (?, ?, ?, ?, ?)',
            [business.id, name, price, description, image], () => res.redirect('/dashboard'));
    });
});

app.post('/product/delete/:id', (req, res) => {
    if (!req.session.user) return res.redirect('/login');
    db.get('SELECT b.id FROM businesses b JOIN products p ON b.id = p.business_id WHERE b.user_id = ? AND p.id = ?',
        [req.session.user.id, req.params.id], (err, row) => {
            if (!row) return res.status(403).send('Unauthorized');
            db.run('DELETE FROM products WHERE id = ?', [req.params.id], () => res.redirect('/dashboard'));
        });
});

// Generated Website Route
app.get('/s/:slug', (req, res) => {
    db.get('SELECT * FROM businesses WHERE slug = ?', [req.params.slug], (err, business) => {
        if (!business) return res.status(404).send('Business not found');
        db.all('SELECT * FROM products WHERE business_id = ?', [business.id], (err, products) => {
            res.render('site', { business, products });
        });
    });
});

app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});

module.exports = { app, upload };
