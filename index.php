<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Simple Chatbot</title>

  <style type="text/css">
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .chat-container {
      width: 400px;
      border: 1px solid #ccc;
      border-radius: 8px;
      overflow: hidden;
    }

    .chat-header {
      background-color: #4CAF50;
      color: white;
      padding: 10px;
      text-align: center;
    }

    .chat-box {
      height: 300px;
      overflow-y: scroll;
      padding: 10px;
      display: flex;
      flex-direction: column; /* Display messages from top to bottom */
    }

    .message {
      margin: 10px;
      padding: 8px;
      border-radius: 8px;
      max-width: 70%;
      opacity: 0;
      transform: translateX(-20px);
      transition: opacity 0.3s, transform 0.3s;
    }

    .user-message {
      background-color: #4CAF50;
      color: white;
      align-self: flex-end;
      padding: 10px;
      border-radius: 10px;
      margin: 7.5px;
    }

    .bot-message {
      background-color: #ddd;
      color: #555;
      align-self: flex-start;
      padding: 10px;
      border-radius: 10px;
    }

    #user-input {
      width: 80%;
      padding: 8px;
      margin: 10px;
    }

    button {
      width: 18%;
      padding: 8px;
      margin: 10px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    button:hover {
      background-color: #45a049;
    }
  </style>

</head>
<body>
  <div class="chat-container">
    <div class="chat-header">Chatbot</div>
    <div class="chat-box" id="chat-box">
      <!-- Example messages -->
      <div class="message user-message">Hello, how can I help you?</div>
      <div class="message bot-message">Hi there! I'm a simple chatbot.</div>
    </div>
    <input type="text" id="user-input" placeholder="Type your message...">
    <button onclick="sendMessage()">Send</button>
  </div>

  <script type="text/javascript">
    class Chatbot {
      constructor() {
        this.customReplies = [];
      }

      async init() {
        this.customReplies = await this.loadCustomReplies();
        this.displayMessage("Hello! How can I help you today?");
      }

      async loadCustomReplies() {
        try {
          const response = await fetch('data/chat.json');
          const data = await response.json();
          console.log('Loaded custom replies:', data);
          return data;
        } catch (error) {
          console.error('Error loading custom replies:', error);
          return [];
        }
      }

      async generateReply(userInput) {
        const lowerCaseInput = userInput.toLowerCase();

        for (const customReply of this.customReplies) {
          if (lowerCaseInput.includes(customReply.message.toLowerCase())) {
            if (lowerCaseInput.includes("query1")) {
              await this.executeQuery1WithInput();
              return;
            } else if (lowerCaseInput.includes("query2")) {
              await this.executeQuery2WithInput();
              return;
            } else if (lowerCaseInput.includes("query3")) {
              this.displayMessage("Bot: Executing Query 3...");
              return;
            } else {
              this.displayMessage(`Bot: ${customReply.response}`);
              return;
            }
          }
        }

        this.displayMessage("Bot: I'm a simple chatbot. How can I assist you?");
      }

      async executeQuery1WithInput() {
        const additionalInput = prompt("Please provide additional input:");
        try {
          const result = await this.ajaxRequest('product.php', `input=${additionalInput}`);
          const productName = result.length > 0 ? result[0].p_name : 'No product found';
          this.displayMessage(`Bot: ${productName}`);
        } catch (error) {
          this.displayMessage(`Bot: Error executing Query 1 - ${error}`);
        }
      }

      async executeQuery2WithInput() {
        const additionalInput = prompt("Please provide additional input:");
        try {
          const result = await this.ajaxRequest('list.php', `input=${additionalInput}`);
          const productNames = result.map(product => product.p_name).join(', ');
          this.displayMessage(`Bot: ${productNames}`);
        } catch (error) {
          this.displayMessage(`Bot: Error executing Query 2 - ${error}`);
        }
      }

      async ajaxRequest(url, params) {
        return new Promise((resolve, reject) => {
          const xhr = new XMLHttpRequest();
          xhr.onreadystatechange = function () {
            if (xhr.readyState == 4) {
              console.log('Server response:', xhr.responseText);
              if (xhr.status == 200) {
                try {
                  resolve(JSON.parse(xhr.responseText));
                } catch (error) {
                  reject('Error parsing server response.');
                }
              } else {
                reject(`Error: ${xhr.status} - ${xhr.statusText}`);
              }
            }
          };
          xhr.open('POST', url, true);
          xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
          xhr.send(params);
        });
      }

      displayMessage(message) {
        const chatBox = document.getElementById("chat-box");
        const messageElement = document.createElement("div");
        messageElement.textContent = message;

        messageElement.classList.add(message.includes("You:") ? "user-message" : "bot-message");

        messageElement.style.opacity = 0;
        messageElement.style.transform = "translateX(-20px)";
        messageElement.style.transition = "opacity 0.3s, transform 0.3s";

        chatBox.appendChild(messageElement);

        messageElement.offsetHeight;

        messageElement.style.opacity = 1;
        messageElement.style.transform = "translateX(0)";

        chatBox.scrollTop = chatBox.scrollHeight;
      }
    }

    const chatbot = new Chatbot();

    document.addEventListener("DOMContentLoaded", () => chatbot.init());

    function sendMessage() {
      const userInput = document.getElementById("user-input").value.trim();

      if (userInput !== "") {
        chatbot.displayMessage(`You: ${userInput}`);
        chatbot.generateReply(userInput);
      }

      document.getElementById("user-input").value = "";
    }
  </script>
</body>
</html>
