<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password reset</title>
</head>
<body>
    <form method="POST" action="{{route('post.reset.password')}}">
        <input type="text" name="password" placeholder="new password">
    </form>
</body>
</html>