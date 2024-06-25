<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Embedded Power BI Report</title>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/powerbi-client/2.18.6/powerbi.min.js" integrity="sha512-+/ER3lQ/2iaiHYCW+ada5W7p+YoA8ZTdTzDtWqRQA1IXpDPKY5V2+fvsQIk8DZr42FTLxcBnoY/qAb77gd+uTQ==" crossorigin="anonymous"></script>
</head>
<body>
    <header class="embed-container col-lg-12 col-md-12 col-sm-12 shadow">
        <p>Power BI Embedded Sample</p>
    </header>
    <main class="row">
        <section id="text-container" class="embed-container col-lg-4 col-md-4 col-sm-4 mb-5 ml-5 mt-5">
            <div>
                <p>This sample is developed using PHP and Power BI Client API.
                    <br> The report is embedded using Power BI JavaScript API.
                </p>
            </div>
        </section>
        <section id="report-container" class="embed-container">
        </section>

        <section class="error-container m-5"></section>
    </main>
    <footer class="embed-container col-lg-12 col-md-12 col-sm-12 mb-0 mt-4">
        <p class="text-center">
            For Live demo and more code samples please visit <a href="https://aka.ms/pbijs">https://aka.ms/pbijs</a>
            <br> For JavaScript API, please visit <a href="https://aka.ms/PowerBIjs">https://aka.ms/PowerBIjs</a>
        </p>
    </footer>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (window["powerbi-client"] && window["powerbi-client"].models) {
                let models = window["powerbi-client"].models;
                let reportContainer = document.getElementById("report-container");

                powerbi.bootstrap(reportContainer, { type: "report" });

                let filter = {
                    $schema: "http://powerbi.com/product/schema#basic",
                    target: {
                        table: "Transacciones x Año", 
                        column: "id_empresa"  
                    },
                    operator: "In",
                    values: [133]
                };
                let filter2 = {
                    $schema: "http://powerbi.com/product/schema#basic",
                    target: {
                        table: "Transacciones x Mes", 
                        column: "id_empresa"  
                    },
                    operator: "In",
                    values: [133]
                };

                let reportLoadConfig = {
                        type: "report",
                        tokenType: models.TokenType.Embed,
                        accessToken: "<?php echo $accessToken; ?>",
                        embedUrl: "<?php echo $embedUrl; ?>",
                        filters: [filter,filter2],
                        settings: {
                                    panes: {
                                        filters: {
                                            expanded: false,
                                            visible: false
                                        }
                                    }
                                }
                    };

                let tokenExpiry = "{{ $expiry }}";

                let report = powerbi.embed(reportContainer, reportLoadConfig);

                report.off("loaded");
                report.on("loaded", function () {
                    console.log("Report load successful");
                });

                report.off("rendered");
                report.on("rendered", function () {
                    console.log("Report render successful");
                });

                report.off("error");
                report.on("error", function (event) {
                    let errorMsg = event.detail;
                    console.error(errorMsg);
                    return;
                });
            } else {
                console.error("Power BI client library not loaded");
            }
        });
    </script>
</body>
</html>
