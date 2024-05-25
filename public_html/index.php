<?php
define('APP_INIT', true);
require __DIR__.'/../app_start.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?=BASE_URL?>assets/css/index.css">
  <title><?=$title?></title>
</head>
<body>
  <header>
    <div class="logo">
      <a href="/">OZZ LOGO</a>
    </div>
    <nav>
      <ul>
        <li><a href="/">Home</a></li>
        <li><a href="/contact">Contact</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="page-container">
    <?php require $view; ?>
    </div>
  </main>
<script src="<?=BASE_URL?>assets/js/app.js"></script>
</body>
</html>
<?php require __DIR__.'/../app_end.php';?>