<?php
    header('HTTP/1.1 503 Service Temporarily Unavailable');
    header('Status: 503 Service Temporarily Unavailable');
    header('Retry-after: 300');
    header('Expires: Thu, 01 Dec 1994 16:00:00 GMT');
    header('Last modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
    header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Probíhá údržba</title>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
        <style>
            body {
                font-family: 'Open sans', Arial, sans-serif;
                color: #333;
            }
            .web {
                width: 980px;
                margin: 0 auto;
                max-width: 100%;
                padding: 20px;

                text-align: center;
            }
            .web h1 {
                margin-bottom: 80px;
                line-height: 40px;

                font-size: 32px;
                font-weight: 600;
            }
            .web p {
                line-height: 22px;

                font-size: 16px;
            }
            .web img {
                margin-bottom: 40px;
            }
        </style>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        <div class="web">
            <h1>Probíhá údržba</h1>
            <img src="/assets/frontend/images/maintenance.png" alt="Probíhá údržba">
            <p>
                Zkuste to prosím znovu za pár minut.
            </p>
        </div>
    </body>
</html>
