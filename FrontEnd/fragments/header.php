<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Polwita</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/mystyles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
    <?php
    $IP_ADDRESS = "http://localhost:5000";
    $USER_ID = 2;
    include_once("error_modal.php")
    ?>

    <script>
        const ip_address = "<?php global $IP_ADDRESS; echo $IP_ADDRESS;?>";
        const user_id = "<?php global $USER_ID; echo $USER_ID;?>";
        window.user_found = function () {
        }
        $(function () {
            const ip_address = "<?php global $IP_ADDRESS; echo $IP_ADDRESS;?>";
            const user_id = "<?php global $USER_ID; echo $USER_ID;?>";

            $.ajax(ip_address + "/get_user/" + user_id).done(function (data) {
                if (data.length === 0) {
                    window.user = undefined
                    window.user_found()
                } else {
                    window.user = data["user"]
                    $("#name_to_set").text(window.user["Imie"] + " " + window.user["Nazwisko"])
                }
                window.user_found()
            }).fail(function (data) {
                window.user = undefined
                $("#name_to_set").text("Gość")
                $("#errorModalText").text("Wystąpił błąd połączenia!")
                $("#errorModal").modal("show");
                window.user_found()
            })
        })
        const currencyFormatter = new Intl.NumberFormat('pl-PL', {
            style: 'currency',
            currency: "PLN"
        });
    </script>
</head>
<body>

<nav class="navbar navbar-expand navbar-dark bg-primary ">
    <div class="container">
        <a class="navbar-brand" href="../index.php">
            <img src="../images/logo.png"/>
        </a>
        <div class="navbar-collapse collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active">
                        Przeglądaj
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link">
                        Pomoc
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link">
                        Kontakt
                    </a>
                </li>

            </ul>
            <ul class="navbar-nav my-2 my-md-0 align-items-center">
                <li class="nav-item text-white m-3">
                    Zalogowany jako
                    <div id="name_to_set"></div>
                </li>
                <li>
                    <img src="../images/basket.png" class="rounded img-thumbnail" style="max-width: 50px"/>
                </li>
            </ul>
        </div>
    </div>

</nav>

