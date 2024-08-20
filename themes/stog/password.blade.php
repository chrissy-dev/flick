<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Protected Album</title>
</head>

<body>
    <h1>Password Required</h1>
    @if (isset($error))
        <p style="color: red;">{{ $error }}</p>
    @endif
    <form method="POST" action="/album/{{ urlencode($albumName) }}">
        <label for="password">Enter Password:</label>
        <input type="password" name="password" id="password" required>
        <button type="submit">Submit</button>
    </form>
</body>

</html>
