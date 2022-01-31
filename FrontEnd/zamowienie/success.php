<?php require_once('../fragments/header.php'); ?>

<div class="container">
    <main class="align-self-center ">

        <h1 class="text-center">Dziękujemy za złożenie zamówienia!</h1>
        <h4 class="text-center">Potwierdzenie zostało wysłane na maila!</h4>
        <h4 class="text-center">Szacowany czas oczekiwania: 2 dni robocze</h4>
        <div class="text-center">
            <button id="accept" type="submit" class="btn btn-primary align-self-end"
                    onclick="window.location.href = '../index.php'">
                Strona głowna
            </button>
        </div>

    </main>
</div>

<script>
    $(function () {
        Cookies.remove("delivery", "")
        Cookies.remove("delivery_method")
        Cookies.remove("basket_sum")
        Cookies.remove("payment_method")
    })
</script>

<?php require_once("../fragments/footer.php"); ?>
