<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Page not found</title>
    <link href="/css/app.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font: 110%/1.5 "Nunito", sans-serif;
            background: #ffc107;
            color: black;
            height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 2rem;
        }
        main {
            max-width: 350px;
        }
        a {
            color: #0d6efd;
        }
    </style>
</head>
<body>
<main>
    <h1 data-test-id="text-404">403 | Unauthorized</h1>
    <p>Unfortunately you don't have access to this page.</p>
    <p>Please double check your spelling, or the link that sent you here. We've logged the error, in case it was our fault.</p>
    <p><a href="/" class="btn btn-primary">Go to the homepage</a></p>
</main>
</body>
</html>
