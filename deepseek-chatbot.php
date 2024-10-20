<?php
/*
Plugin Name: DeepSeek Chatbot
Description: A custom chatbot plugin that integrates with DeepSeek API.
Version: 1.0
Author: Your Name
*/

function enqueue_deepseek_chatbot_script() {
    wp_enqueue_script('deepseek-chatbot', plugin_dir_url(__FILE__) . 'js/chatbot.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_deepseek_chatbot_script');

function deepseek_chatbot_shortcode() {
    ob_start();
    ?>
    <div id="chat-window" style="position: fixed; bottom: 20px; right: 20px; width: 300px; height: 400px; background-color: #fff; border: 1px solid #ccc; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); z-index: 1000; display: none;">
        <div style="background-color: #0073aa; color: #fff; padding: 10px; cursor: pointer;" onclick="toggleChatWindow()">Chat with us</div>
        <div id="chat-body" style="padding: 10px; height: calc(100% - 50px); overflow-y: auto;"></div>
        <input type="text" id="chat-input" style="width: calc(100% - 20px); padding: 10px; margin: 10px; border: 1px solid #ccc; border-radius: 5px;" placeholder="Type your message...">
        <button id="chat-button" style="padding: 10px; margin: 10px; border: none; background-color: #0073aa; color: #fff; border-radius: 5px; cursor: pointer;">Send</button>
    </div>
    <script>
        function toggleChatWindow() {
            const chatWindow = document.getElementById('chat-window');
            if (chatWindow.style.display === 'none') {
                chatWindow.style.display = 'block';
            } else {
                chatWindow.style.display = 'none';
            }
        }

        document.getElementById('chat-button').addEventListener('click', function() {
            const userMessage = document.getElementById('chat-input').value;
            if (userMessage) {
                addMessage('user', userMessage);
                document.getElementById('chat-input').value = '';
                sendMessage(userMessage);
            }
        });

        function addMessage(role, content) {
            const chatBody = document.getElementById('chat-body');
            const messageDiv = document.createElement('div');
            messageDiv.style.margin = '10px 0';
            messageDiv.style.padding = '10px';
            messageDiv.style.borderRadius = '5px';
            messageDiv.style.backgroundColor = role === 'user' ? '#f1f1f1' : '#e1f5fe';
            messageDiv.textContent = content;
            chatBody.appendChild(messageDiv);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function sendMessage(message) {
            fetch('https://api.deepseek.com/chat/completions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer YOUR API KEY' // 在这里填入您的 API Key
                },
                body: JSON.stringify({
                    'model': 'deepseek-chat',
                    'messages': [
                        {'role': 'system', 'content': 'You are a helpful assistant.'},
                        {'role': 'user', 'content': message}
                    ],
                    'stream': false
                })
            })
            .then(response => response.json())
            .then(data => {
                const assistantMessage = data.choices[0].message.content;
                addMessage('assistant', assistantMessage);
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('deepseek_chatbot', 'deepseek_chatbot_shortcode');
