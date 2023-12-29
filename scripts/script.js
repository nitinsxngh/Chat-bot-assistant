    // Class to handle chatbot logic
    class Chatbot {
      constructor() {
        this.customReplies = [];
      }

      async init() {
        // Load custom replies from chat.json
        this.customReplies = await this.loadCustomReplies();
        // Initial greeting message
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
        // Convert user input to lowercase for case-insensitive matching
        const lowerCaseInput = userInput.toLowerCase();

        // Check if the user input matches a custom message
        for (const customReply of this.customReplies) {
          if (lowerCaseInput.includes(customReply.message.toLowerCase())) {
            // Check for specific keywords to run different queries
            if (lowerCaseInput.includes("query1")) {
              await this.executeQuery1WithInput();
              return;
            } else if (lowerCaseInput.includes("query2")) {
              await this.executeQuery2WithInput();
              return;
            } else if (lowerCaseInput.includes("query3")) {
              // Run your third query logic here
              this.displayMessage("Bot: Executing Query 3...");
              return;
            } else {
              // If no specific keyword matches, return the custom reply
              this.displayMessage(`Bot: ${customReply.response}`);
              return;
            }
          }
        }

        // If no specific message matches, return a default reply
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
    // Display all products instead of just the first one
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
        chatBox.appendChild(messageElement);

        // Scroll to the bottom of the chat box
        chatBox.scrollTop = chatBox.scrollHeight;
      }
    }

    // Create an instance of the Chatbot class
    const chatbot = new Chatbot();

    // Initialize the chatbot
    document.addEventListener("DOMContentLoaded", () => chatbot.init());

    // Function to send user message
    function sendMessage() {
      // Get user input
      const userInput = document.getElementById("user-input").value.trim();

      // Check if the input is not empty
      if (userInput !== "") {
        // Display user message
        chatbot.displayMessage(`You: ${userInput}`);

        // Process user input and generate a reply
        chatbot.generateReply(userInput);
      }

      // Clear user input
      document.getElementById("user-input").value = "";
    }