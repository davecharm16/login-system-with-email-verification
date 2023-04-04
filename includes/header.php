<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
    <title><?php if(isset($page_title)) { echo "$page_title"; } ?></title>

    <style>
        .navbar-light .navbar-nav .nav-link{
            color: white;
            transition: all 0.5 ease-in;
        }

        .navbar-light .navbar-nav .nav-link :hover{
            color: #1E90FF;
        }

        .nav-item {
            margin: 0 10px;
        }

        .px-12{
            padding-left:5rem;
            padding-right:5rem;
        }
        #reg_form h4, #login-form h4{
            color : red;
            font-size: 15px;
        }
    </style>
</head>
<body>