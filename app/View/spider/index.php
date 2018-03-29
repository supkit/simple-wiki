<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>You-get</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #f1f3f5;
        }
        .container {
            margin: 200px auto 50px;
            width: 760px;
            position: relative;
        }
        .input input {
            padding: 13px 45px 13px 15px;
            width: 760px;
            box-shadow: 0 0 2px #ddd inset;
            border: 2px solid #ddd;
            border-radius: 4px;
            outline: none;
            margin-left: 3px;
            box-sizing: border-box;
            font-size: 18px;
            color: #444;
        }
        .input input:focus {
            border: 2px solid #4dadf7;
            transition: all .5s ease-in-out;
            box-shadow: 0 0 2px #ace8f7;
        }
        .input button {
            width: 40px;
            padding: 5px;
            position: absolute;
            right: 6px;
            top: 11px;
            background: transparent;
            border: 0;
        }
        .input button svg {
            width: 18px;
        }
        .search-icon {
            color: #ccc;
        }
        input::-webkit-input-placeholder{
            color: #dddddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="/spider/index/video" method="post">
            <div class="input"><input type="text" name="url" placeholder="Search video url" autocomplete="off"><button><svg aria-hidden="true" data-prefix="fas" data-icon="search" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="o-15 absolute center-v right-1 svg-inline--fa fa-search fa-w-16 fa-fw fa-lg" style=""><path fill="currentColor" d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z" class="search-icon"></path></svg></button></div>
        </form>
    </div>
</body>
</html>