<?php require_once('../fragments/header.php'); ?>
<div class="container">
    <main class="align-self-center mx-auto">
        <h1>Przekierowanie do strony płatności...</h1>
    </main>
</div>

<script>
    window.setTimeout(function () {
        window.location.href = "success.php"
    }, 2000)
</script>
<?php require_once("../fragments/footer.php"); ?>
