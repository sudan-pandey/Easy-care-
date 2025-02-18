require('dotenv').config();
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');
const axios = require('axios');

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: "*"
    }
});

app.use(cors());
app.use(express.json());

// WebSocket Connection for Device Control
io.on('connection', (socket) => {
    console.log('A user connected');

    socket.on('toggleDevice', (data) => {
        console.log(`Toggling device: ${data.device}, Status: ${data.status}`);
        io.emit('deviceStatus', data);
    });

    socket.on('disconnect', () => {
        console.log('User disconnected');
    });
});

// AI Chatbot API
app.post('/chat', async (req, res) => {
    const userMessage = req.body.message;
    
    try {
        const response = await axios.post(
            'https://api.groq.com/v1/chat/completions',
            {
                model: "llama-3.3-70b-versatile",
                messages: [{ role: "user", content: userMessage }],
                max_tokens: 150
            },
            { headers: { Authorization: `Bearer ${process.env.GROQ_API_KEY}` } }
        );

        res.json({ reply: response.data.choices[0].message.content });
    } catch (error) {
        console.error('Chatbot API Error:', error);
        res.status(500).json({ reply: "Sorry, something went wrong." });
    }
});

const PORT = process.env.PORT || 5000;
server.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});
