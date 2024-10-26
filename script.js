// script.js

document.addEventListener('DOMContentLoaded', function() {
    // Получаем ссылки на элементы
    const chatList = document.querySelector('#chat-list ul');
    const chatContent = document.querySelector('#chat-content');
    const chatForm = document.querySelector('#chat-form');

    // Функция для обновления списка чатов
    function updateChatList() {
        fetch('chats.php')
            .then(response => response.json())
            .then(data => {
                chatList.innerHTML = ''; // Очищаем список
                data.forEach(chat => {
                    const chatItem = document.createElement('li');
                    chatItem.textContent = chat.other_user;
                    chatItem.dataset.chatId = chat.id; // Сохраняем ID чата в атрибуте data-chat-id
                    chatList.appendChild(chatItem);

                    // Добавляем обработчик события клика для каждого чата
                    chatItem.addEventListener('click', () => {
                        loadChat(chat.id);
                    });
                });
            })
            .catch(error => {
                console.error('Ошибка получения данных о чатах:', error);
            });
    }

    // Функция для загрузки сообщений чата
    function loadChat(chatId) {
        fetch(`chats.php?chat_id=${chatId}`)
            .then(response => response.json())
            .then(data => {
                chatContent.innerHTML = `
                    <h2>Чат с ${data.other_user}</h2>
                    <div id="chat-messages">
                        <!-- Сообщения будут добавлены здесь -->
                    </div>
                    <form id="chat-form" method="post">
                        <input type="hidden" name="chat_id" value="${chatId}">
                        <input type="text" name="message" placeholder="Введите сообщение...">
                        <button type="submit">Отправить</button>
                    </form>
                `;
                // Добавляем сообщения в chat-messages
                const chatMessages = chatContent.querySelector('#chat-messages');
                data.messages.forEach(message => {
                    const messageItem = document.createElement('div');
                    messageItem.classList.add('message', message.is_sent ? 'sent' : 'received');
                    messageItem.innerHTML = `
                        <span class="username">${message.username}</span>: 
                        <span class="content">${message.content}</span>
                        <span class="timestamp">${message.timestamp}</span>
                    `;
                    chatMessages.appendChild(messageItem);
                });
                // Добавляем обработчик отправки формы
                chatForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    const message = this.querySelector('input[name="message"]').value;
                    if (message.trim() !== "") {
                        fetch('messages.php', {
                            method: 'POST',
                            body: JSON.stringify({
                                chat_id: chatId,
                                message: message
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                // Добавляем новое сообщение
                                const messageItem = document.createElement('div');
                                messageItem.classList.add('message', 'sent');
                                messageItem.innerHTML = `
                                    <span class="username">${data.username}</span>: 
                                    <span class="content">${data.message}</span>
                                    <span class="timestamp">${data.timestamp}</span>
                                `;
                                chatMessages.appendChild(messageItem);
                                // Очищаем поле ввода
                                this.querySelector('input[name="message"]').value = '';
                            })
                            .catch(error => {
                                console.error('Ошибка отправки сообщения:', error);
                            });
                    }
                });
            })
            .catch(error => {
                console.error('Ошибка получения данных о чате:', error);
            });
    }

    // Обновляем список чатов при загрузке страницы
    updateChatList();
});
