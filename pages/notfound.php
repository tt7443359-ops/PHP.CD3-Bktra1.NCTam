<?php
if (!isset($base_url)) {
    require_once __DIR__ . '/../includes/db.php';
}
if (!isset($errorMessage)) {
    $errorMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : null;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 0.95em;
            margin: 20px 30px;
            background-color: #fff;
            color: #222;
        }

        h1 {
            font-size: 2.0em;
            margin-bottom: 0.2em;
        }

        p {
            font-size: 1.0em;
            margin: 0.3em 0;
        }

        a {
            font-size: 0.93em;
            text-decoration: none;
        }

        a:link,
        a:visited {
            color: #000080;
            font-size: 15px;
        }

        a:hover {
            color: #FF0000;
        }

        .error-code {
            font-size: 0.8em;
            color: #000080;
            margin: 0.2em 0 0.8em;
        }

        .server-msg {
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-left: 3px solid #999;
            padding: 8px 12px;
            margin: 8px 0 12px;
            font-family: monospace;
            font-size: 0.88em;
            color: #444;
            white-space: pre-wrap;
            word-break: break-word;
            max-width: 600px;
        }

        .btn-reload {
            display: inline-flex;
            align-items: center;
            margin: 5px 0 5px;
            justify-content: center;
            background: #8ab4f8;
            color: #000;
            font-size: 0.88em;
            padding: 8px 22px;
            border-radius: 24px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            min-width: 120px;
        }

        .btn-reload:hover {
            background: #6b9cf1;
            color: #000;
        }

        .hero-image {
            display: block;
            margin-top: 5px;
            max-width: 1000px;
        }

        img {
            display: block;
            margin-top: 20px;
            max-width: 1000px;
        }

        .footer-links {
            margin-top: 20px;
            font-size: 0.9em;
        }

        .footer-links a {
            margin-right: 15px;
        }

        /* Speech Bubble Style */
        .speech-bubble {
            position: relative;
            background: #ffffff;
            border: 2px solid #d1d1d1;
            border-radius: 20px;
            padding: 12px 25px;
            margin: 15px 0 5px 5px;
            display: inline-block;
            max-width: 600px;
            font-style: italic;
            color: #444;
            line-height: 1.5;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }

        /* Bubble Tail */
        .speech-bubble::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 40px;
            border-width: 15px 15px 0 0;
            border-style: solid;
            border-color: #d1d1d1 transparent;
            display: block;
            width: 0;
        }

        .speech-bubble::before {
            content: '';
            position: absolute;
            bottom: -11px;
            left: 42px;
            border-width: 11px 11px 0 0;
            border-style: solid;
            border-color: #ffffff transparent;
            display: block;
            width: 0;
            z-index: 1;
        }
    </style>
</head>

<body>
    <h1>HTTP Error 404: Not Found</h1>
    <p class="error-code">The requested document could not be found. Please retry from the <a
            href="<?= $base_url ?>">index page</a>.</p>
    <?php if ($errorMessage): ?>
        <div class="server-msg"><?= $errorMessage ?></div>
    <?php endif; ?>
    <button class="btn-reload" onclick="history.back()">Quay về</button>
    <br>
    <div class="speech-bubble">
        "I'm sorry, I can't find that page for you. Just like I have no arms to hug you, this server has no data
        to show you right now."
    </div>
    <img class="hero-image" src="<?php echo $base_url; ?>public/assets/img/rin.jpg" alt="rin">
</body>

</html>