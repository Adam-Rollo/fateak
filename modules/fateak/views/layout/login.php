<html>
<head>
    <title><?php echo $title ?></title>
    <?php echo Assets::all_css() ?>
    <?php echo Assets::head_js() ?>
</head>
<body>
    <?php include Kohana::find_file('views', 'login.tpl'); ?>
</body>
</html>
