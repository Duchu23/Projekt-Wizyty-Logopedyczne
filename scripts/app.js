/*
 * app.js – cała logika AJAX aplikacji (jQuery).
 * Jeden plik dla wielu stron – każdą funkcję uruchamiamy tylko,
 * jeśli dany element istnieje na bieżącej stronie.
 */

$(function () {

    /* =========================================================
     * 1 – Polubienia (toggle ulubionych) – details.php
     * ========================================================= */
    if ($(".fav-toggle").length) {
        $(".fav-toggle").on("click", function () {
            const przycisk    = $(this);
            const cwiczenieId = przycisk.data("cwiczenie-id");

            $.post("toggle_fav.php", { id_cwiczenia: cwiczenieId }, function (odpowiedz) {
                if (odpowiedz === "dodano") {
                    przycisk.text("❤️ Usuń z ulubionych");
                    przycisk.data("liked", 1);
                } else if (odpowiedz === "usunieto") {
                    przycisk.text("🤍 Dodaj do ulubionych");
                    przycisk.data("liked", 0);
                } else {
                    console.log("Błąd polubienia:", odpowiedz);
                }
            });
        });
    }

    /* =========================================================
     * 2 – Wyszukiwanie na żywo – cwiczenia.php
     * ========================================================= */
    if ($("#poleWyszukiwania").length) {
        let timer = null; // do techniki "debounce"

        $("#poleWyszukiwania").on("keyup", function () {
            const fraza = $(this).val();

            if (fraza.length < 2) {
                $("#wyniki-szukania").html("");
                return;
            }

            // DEBOUNCE: czekamy 300 ms po ostatnim klawiszu zanim wyślemy żądanie.
            clearTimeout(timer);
            timer = setTimeout(function () {
                $.get("search_ajax.php", { fraza: fraza }, function (odpowiedz) {
                    $("#wyniki-szukania").html(odpowiedz);
                });
            }, 300);
        });
    }

    /* =========================================================
     * 3 – Usuwanie komentarza – my_comments.php
     * (delegacja zdarzeń na kontenerze nadrzędnym)
     * ========================================================= */
    if ($("#lista-komentarzy").length) {
        $("#lista-komentarzy").on("click", ".delete-review", function () {
            const reviewId = $(this).data("review-id");

            if (!confirm("Czy na pewno usunąć ten komentarz?")) {
                return;
            }

            $.post("delete_comment_ajax.php", { id: reviewId }, function (odpowiedz) {
                if (odpowiedz === "ok") {
                    $("#komentarz-" + reviewId).fadeOut(400, function () {
                        $(this).remove();
                    });
                } else {
                    alert("Nie udało się usunąć komentarza.");
                }
            });
        });
    }

    /* =========================================================
     * 4 – Sprawdzanie wolnych terminów – umow_wizyte.php
     * Po wyborze specjalisty i daty pytamy serwer, które godziny
     * są zajęte, i wyłączamy je na liście godzin.
     * ========================================================= */
    if ($("#pole-godzina").length) {

        function sprawdzTerminy() {
            const idSpecjalisty = $("#pole-specjalista").val();
            const data          = $("#pole-data").val();

            // Bez daty nie ma czego sprawdzać.
            if (!data) {
                return;
            }

            $.get("wolne_terminy_ajax.php",
                  { id_specjalisty: idSpecjalisty, data: data },
                  function (zajete) {
                // zajete to tablica godzin, np. ["09:00","11:00"] (JSON)
                $("#pole-godzina option").each(function () {
                    const godzina = $(this).val();
                    if (zajete.indexOf(godzina) !== -1) {
                        // Godzina zajęta – wyłączamy ją i oznaczamy.
                        $(this).prop("disabled", true).text(godzina + " (zajęte)");
                    } else {
                        // Godzina wolna – przywracamy normalny stan.
                        $(this).prop("disabled", false).text(godzina);
                    }
                });

                if (zajete.length > 0) {
                    $("#info-terminy").text("Godziny oznaczone jako „zajęte" są już zarezerwowane.");
                } else {
                    $("#info-terminy").text("Wszystkie godziny w tym dniu są wolne.");
                }
            }, "json");
        }

        // Sprawdzamy przy każdej zmianie specjalisty lub daty.
        $("#pole-specjalista, #pole-data").on("change", sprawdzTerminy);
    }

    /* =========================================================
     * 5 – Odwoływanie wizyty – moje_wizyty.php
     * (delegacja zdarzeń, jak przy usuwaniu komentarza)
     * ========================================================= */
    if ($("#lista-wizyt").length) {
        $("#lista-wizyt").on("click", ".anuluj-wizyte", function () {
            const wizytaId = $(this).data("wizyta-id");

            if (!confirm("Czy na pewno odwołać tę wizytę?")) {
                return;
            }

            $.post("anuluj_wizyte_ajax.php", { id: wizytaId }, function (odpowiedz) {
                if (odpowiedz === "ok") {
                    $("#wizyta-" + wizytaId).fadeOut(400, function () {
                        $(this).remove();
                    });
                } else {
                    alert("Nie udało się odwołać wizyty.");
                }
            });
        });
    }

});
