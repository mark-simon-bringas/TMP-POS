<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    @font-face {
      font-family: 'FeelingPassionate';
      src: url(../FeelingPassionateRegular-gxp34.ttf);
    }
    .feeling-passionate {
      font-family: 'FeelingPassionate', cursive;
      font-size: 100px;
    }
    .bg{
        background-color:#fbede7;
        background: radial-gradient(circle at top left, #fbede7, #f8d9da, #fff);
        background-attachment: fixed;
        background-size: cover;
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
    }
    .btn-color{
        background-color:#f8d9da;
    }
    </style>
</head>
<body class="bg">
    <section class="vh-100">
        <div class="container pt-2 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-auto d-flex flex-column align-items-center">
                    <h1 class="text-black text-center mt-3 mb-5 ms-2 me-2 feeling-passionate" ><p>the meeting place</p></h4>
                    <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
                        <h4 class="mb-4 text-center">Login</h4>
                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label for="uid" class="form-label">Username</label>
                                <input type="text" name="usern" id="usern" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="pass" class="form-label">Password</label>
                                <input type="password" name="pass" id="pass" class="form-control" required>
                            </div>
                            <button type="submit" name="submit" class="btn btn-color w-100 mb-3">Login</button>
                            <a href="register.php" class="btn btn-secondary w-100">Register</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
