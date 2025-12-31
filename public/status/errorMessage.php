<style>
    .error-message {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
        color: white;
        border-radius: 5px;
        opacity: 0;
        transition: opacity 1s;
        font-size: 16px;
        z-index: 1000;
    }
</style>
<div id="error-message" class="error-message">
    <?php
    if (isset($_GET['error'])) {
        if ($_GET['error'] == 'password_mismatch') {
            echo "Passwords do not match.";
        } elseif ($_GET['error'] == 'username_taken') {
            echo "Username is already taken.";
        }
    }
    ?>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var msg = document.getElementById("error-message");
        msg.style.opacity = "1";
        setTimeout(function() {
            msg.style.opacity = "0";
            setTimeout(function() {
                msg.style.display = "none";
            }, 1000);
        }, 3000);
    });
</script>
