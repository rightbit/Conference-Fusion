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
            background: #0d6efd;
            color: white;
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
            color: #ffc107;
        }
    </style>
</head>
<body>
    <main>
        <h1 data-test-id="text-404">404 | Not Found</h1>
        <p>Unfortunately you've found a page that doesn't exist.</p>
        <p>Please double check your spelling, or the link that sent you here. We've logged the error, in case it was our fault.</p>
        <p><a href="/" class="btn btn-warning">Go to the homepage</a></p>
    </main>
</body>
</html>
