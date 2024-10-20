# deepseek-wordpress-
这是一个轻便的插件，您只需要一个自己的deepseek AP Ikey 即可实现在wordpress上轻松部署自己的聊天机器人
这段代码是一个 WordPress 插件，名为 "DeepSeek Chatbot"。它创建了一个自定义的聊天机器人，该机器人与 DeepSeek API 集成，允许用户在网站上与聊天机器人进行交互。以下是对代码的详细解释：

### 1. 插件头部信息
```php
<?php
/*
Plugin Name: DeepSeek Chatbot
Description: A custom chatbot plugin that integrates with DeepSeek API.
Version: 1.0
Author: Your Name
*/
```
这部分代码定义了插件的基本信息，包括插件名称、描述、版本号和作者。这些信息会在 WordPress 插件管理界面中显示。

### 2. 加载 JavaScript 文件
```php
function enqueue_deepseek_chatbot_script() {
    wp_enqueue_script('deepseek-chatbot', plugin_dir_url(__FILE__) . 'js/chatbot.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_deepseek_chatbot_script');
```
这段代码定义了一个函数 `enqueue_deepseek_chatbot_script`，用于在 WordPress 前端页面加载时加载一个名为 `chatbot.js` 的 JavaScript 文件。`wp_enqueue_script` 函数用于将脚本文件添加到页面中，`plugin_dir_url(__FILE__)` 获取插件目录的 URL。

### 3. 创建短代码
```php
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
                    'Authorization': 'Bearer Your API KEY' // 在这里填入您的 API Key
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
```
这段代码定义了一个名为 `deepseek_chatbot_shortcode` 的函数，用于生成一个聊天机器人的短代码。短代码可以在 WordPress 页面或文章中使用，以显示聊天机器人窗口。

- **HTML 部分**:
  - 创建了一个固定在页面右下角的聊天窗口，包含一个标题栏、聊天内容区域、输入框和发送按钮。
  - 标题栏有一个点击事件，用于显示或隐藏聊天窗口。
  - 输入框用于输入用户消息，发送按钮用于发送消息。

- **JavaScript 部分**:
  - `toggleChatWindow` 函数用于切换聊天窗口的显示状态。
  - `addMessage` 函数用于在聊天内容区域添加用户或助手的消息。
  - `sendMessage` 函数用于将用户消息发送到 DeepSeek API，并处理 API 的响应，将助手的回复添加到聊天内容区域。

- **API 请求**:
  - `fetch` 函数用于向 DeepSeek API 发送 POST 请求，请求中包含用户消息和 API Key。API 返回的响应包含助手的回复，该回复会被显示在聊天窗口中。

### 4. 注册短代码
```php
add_shortcode('deepseek_chatbot', 'deepseek_chatbot_shortcode');
```
这行代码将 `deepseek_chatbot_shortcode` 函数注册为名为 `deepseek_chatbot` 的短代码。用户可以在 WordPress 编辑器中使用 `[deepseek_chatbot]` 短代码来插入聊天机器人窗口。

### 总结
这个插件通过短代码的方式在 WordPress 网站上嵌入了一个聊天机器人窗口，用户可以与 DeepSeek API 进行交互。插件的核心功能包括加载必要的 JavaScript 文件、生成聊天窗口的 HTML 结构、处理用户输入并通过 API 请求获取助手的回复。
