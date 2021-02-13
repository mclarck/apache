<!DOCTYPE html>
<html>
  <head>
    <title>IM-COM | Socket.IO</title>
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      body {
        font: 13px Helvetica, Arial;
      }
      form {
        background: #000;
        padding: 3px;
        position: fixed;
        bottom: 0;
        width: 100%;
      }
      form input {
        border: 0;
        padding: 10px;
        width: 90%;
        margin-right: 0.5%;
      }
      form button {
        width: 9%;
        background: rgb(130, 224, 255);
        border: none;
        padding: 10px;
      }
      #messages {
        list-style-type: none;
        margin: 0;
        padding: 0;
      }
      #messages li {
        padding: 5px 10px;
      }
      #messages li:nth-child(odd) {
        background: #eee;
      }
    </style>
  </head>
  <body>
    <ul id="messages"></ul>
    <form action="">
      <input id="r" autocomplete="off" placeholder="event" />
      <input id="m" autocomplete="off" placeholder="payload" /><button>
        Send
      </button>
    </form>
    <script src="/socket.io/socket.io.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script>
      $(function () {
        var socket = io("ws://127.0.0.1:8080/chat");
        //var payload = { to: "", content: "", isBot?: false, from: "", company: "", room: ""};
        // { "to": "", "content": "", "isBot": false, "from": "", "company": "", "room": ""}
        socket.emit("join", { room: "master" });
        socket.emit("join", { room: "kioskito" });
        socket.emit("register", {
          content: { username: "master", phone: "master" },
        });
        $("form").submit(function (e) {
          e.preventDefault(); // prevents page reloading
          if ($("#m").val()) {
            socket.emit($("#r").val(), JSON.parse($("#m").val()));
          }
          $("#m").val(
            `{ "to": "", "content": "", "isBot": false, "from": "", "company": "", "room": ""}`
          );
          return false;
        });
        socket.on("join", function (msg) {
          $("#messages").append(
            $("<li>").text(JSON.stringify(msg) + " joined")
          );
        });
        socket.on("leave", function (msg) {
          $("#messages").append($("<li>").text(JSON.stringify(msg) + "left"));
        });
        socket.on("message", function (msg) {
          $("#messages").append($("<li>").text(JSON.stringify(msg)));
        });
        socket.on("last:users", function (msg) {
          $("#messages").append($("<li>").text(JSON.stringify(msg)));
        });
        socket.on("private:message", function (msg) {
          $("#messages").append($("<li>").text(JSON.stringify(msg)));
        });
      });
    </script>
  </body>
</html>
