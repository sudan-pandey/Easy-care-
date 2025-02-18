// Update and display current time
function updateTime() {
    const now = new Date();
    document.getElementById('dateWeather').innerText = now.toLocaleString();
}
setInterval(updateTime, 1000);
updateTime();

// Toggle function for devices
function toggle(id) {
    const toggleOn = document.getElementById(`toggle-on-${id}`);
    const toggleOff = document.getElementById(`toggle-off-${id}`);
    let valueToSend;

    if (toggleOn.style.display === 'inline') {
        toggleOn.style.display = 'none';
        toggleOff.style.display = 'inline';
        valueToSend = id === 'bed' ? '0' : id === 'kitchen' ? '00' : '000';
    } else {
        toggleOn.style.display = 'inline';
        toggleOff.style.display = 'none';
        valueToSend = id === 'bed' ? '1' : id === 'kitchen' ? '11' : '111';
    }

    document.getElementById('messageInput').value = valueToSend;
    document.getElementById('sendButton').click();
}

// Fetch and display weather data
function processWeatherData(response) {
    const weatherInfo = document.getElementById('weatherInfo');
    const currentConditions = response.currentConditions;
    weatherInfo.innerText = `Temperature: ${currentConditions.temp}°F \n Condition: ${currentConditions.conditions}`;
}

fetch("https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/kathmandu?unitGroup=us&key=W33RTSCK5XCBJK2XKTBAMETW5&contentType=json")
    .then(response => response.ok ? response.json() : Promise.reject(response))
    .then(processWeatherData)
    .catch(error => console.error('Weather fetch error:', error));

// Chatbot Functionality
document.addEventListener('DOMContentLoaded', () => {
    const messagesContainer = document.getElementById('messages');
    const typingIndicator = document.getElementById('typing');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const voiceButton = document.getElementById('voiceButton');
    
    async function fetchAIResponse(userMessage) {
        const API_URL = "https://api.your-backend.com/chat"; // Replace with your **backend endpoint**
        
        const requestBody = {
            message: userMessage
        };
        
        try {
            const response = await fetch(API_URL, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                throw new Error(`API Error: ${response.status}`);
            }

            const data = await response.json();
            return data.reply || "Sorry, I couldn't understand that.";
        } catch (error) {
            console.error("AI API Error:", error);
            return "Sorry, an error occurred while fetching the response.";
        }
    }
    
    async function sendMessage() {
        const message = messageInput.value.trim();
        if (message) {
            addMessage(message, 'user');
            messageInput.value = '';
            typingIndicator.style.display = 'block';
            
            const botResponse = await fetchAIResponse(message);
            
            typingIndicator.style.display = 'none';
            addMessage(botResponse, 'bot');
        }
    }
    
    function addMessage(text, sender) {
        const messageElement = document.createElement('p');
        messageElement.classList.add('message', sender);
        messageElement.textContent = text;
        messagesContainer.appendChild(messageElement);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    sendButton.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
    
    // Speech Recognition Setup
    let recognition;
    let isRecognitionActive = false;
    
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        recognition = new (window.webkitSpeechRecognition || window.SpeechRecognition)();
        recognition.lang = 'en-US';
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;
        recognition.continuous = false; // Disable auto-restart
        
        recognition.onstart = () => {
            isRecognitionActive = true;
            voiceButton.classList.add('active');
        };
        
        recognition.onresult = (event) => {
            const speechResult = event.results[event.resultIndex][0].transcript;
            messageInput.value += speechResult + ' ';
            sendButton.click();
        };
        
        recognition.onerror = (event) => {
            console.error("Speech recognition error:", event.error);
            isRecognitionActive = false;
            voiceButton.classList.remove('active');
        };
        
        recognition.onend = () => {
            voiceButton.classList.remove('active');
        };
    }
    
    voiceButton.onclick = () => {
        if (!recognition) return;
        
        if (!isRecognitionActive) {
            try {
                recognition.start();
                isRecognitionActive = true;
            } catch (error) {
                console.error("Error starting recognition:", error);
            }
        } else {
            isRecognitionActive = false;
            recognition.stop();
        }
    };
});
