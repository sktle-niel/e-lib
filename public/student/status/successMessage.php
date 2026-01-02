<style>
    .success-message {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        border-radius: 5px;
        opacity: 0;
        transition: opacity 1s;
        font-size: 16px;
        z-index: 1000;
    }
</style>
<div id="success-message" class="success-message">Account Created Successfully!</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var msg = document.getElementById("success-message");
        msg.style.opacity = "1";
        setTimeout(function() {
            msg.style.opacity = "0";
            setTimeout(function() {
                msg.style.display = "none";
            }, 1000);
        }, 3000);
    });
</script>
