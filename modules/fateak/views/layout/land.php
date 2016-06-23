<html>
<head>
    <title><?php echo $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php echo Assets::all_css() ?>
    <?php echo Assets::head_js() ?>
</head>
<body>
    <?php echo $content ?>
    <?php echo Assets::body_js() ?>
</body>
</html>
