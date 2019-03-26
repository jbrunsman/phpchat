<?php
$cookie_name = 'jchat';
$time_cookie_name = 'jchat_lastupdate';
$time = time();

if(isset($_COOKIE[$cookie_name])) {
    $user_id = $_COOKIE[$cookie_name];
} else {
    $user_id = substr($time, -5);
    setcookie($cookie_name, $user_id, time() + (86400 * 30), "/");
}
?>

<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <title>PHP AJAX Chatroom</title>
        
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    </head>
    <body>
        <div class="mx-auto container" id="chatbox">
            
            <div id="chat_group">

                <div class="rounded" id="textbox"></div>

                <div class="input-group" id="chatbar_group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><?php echo $user_id; ?></span>
                    </div>
                    <input type="text" class="form-control shadow-none" placeholder="Type message..." aria-label="Chat Message" aria-describedby="sendbutton" id="chatbar" name="message" autocomplete="off">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" id="sendbutton" onclick="sendMessage()">Send</button>
                    </div>
                </div>

            </div>

        </div>

        <script>
        
            xhr = new XMLHttpRequest();

            UpdateClock = {time : 0};
            var cookieTime = getCookie("<?php echo $time_cookie_name; ?>");

            if (cookieTime) {
                UpdateClock.time = cookieTime;
            } else {
                UpdateClock.time = Math.floor(Date.now() / 1000);
            }

            var input = document.getElementById("chatbar");
            input.addEventListener("keyup", function(event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    document.getElementById("sendbutton").click();
                }
            });

            function getCookie(name) {
                var value = "; " + document.cookie;
                var parts = value.split("; " + name + "=");
                if (parts.length == 2) return parts.pop().split(";").shift();
            }

            function sendMessage() {
                var username = <?php echo $user_id; ?>;
                var message = document.getElementById("chatbar").value;

                xhr.open("GET", "pusher.php?username=" + username + "&message=" + message);
                xhr.send();

                document.getElementById("chatbar").value = "";
            }

            function updateBox() {
                if (document.hasFocus()) { // the Ryan feature
                    var textBox = document.getElementById("textbox");
                    var updateTime = UpdateClock.time; // time of last update

                    xhr.open("GET","puller.php?id=" + updateTime);
                    xhr.send();

                    /*
                    var scrollDown = false;
                    if ((textBox.scrollTop + textBox.offsetHeight) == textBox.scrollHeight) {
                        scrollDown = true;
                    }
                    */

                    xhr.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {

                            var input = this.responseText;
                            // find first 10 digit number (aka, unix time stamp)
                            var timestampSearch = input.match(/\d{10}/);

                            // loop through the response string and replace each unix time stamp with browser's local time
                            while (timestampSearch) {
                                timestamp = new Date(timestampSearch[0] * 1000);
                                input = input.replace(timestampSearch[0], timestamp.toLocaleString());
                                var newUpdateTime = timestampSearch[0];
                                timestampSearch = input.match(/\d{10}/);
                            }

                            if (newUpdateTime) {
                                UpdateClock.time = newUpdateTime;
                            }

                            // display the modified response text on user's screen
                            textBox.innerHTML += input;
                            textBox.scrollTop = textBox.scrollHeight; // temp auto-scroll

                        }
                    }

                    /*
                    if (scrollDown) {
                        textBox.scrollTop = textBox.scrollHeight;
                    }
                    */
                }
            }

            setInterval(updateBox, 3000);
            window.onload = updateBox();

        </script>
    </body>
</html>