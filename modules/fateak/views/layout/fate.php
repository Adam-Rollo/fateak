<html>
<head>
    <title>Fate</title>
    <?php echo Assets::all_css() ?>
    <?php echo Assets::head_js() ?>
</head>
<body>
    <div id='header-bar'>
        <div class='breadcrumb' style='margin-bottom: 5px'>
            <?php echo $breadcrumb->render() ?>
        </div>
    </div>
    <div id='content'>
        <div style='margin-top: -25px'><h3><?php echo $title ?></h3></div>
        <?php echo $content ?>
    </div>
    <?php echo Assets::body_js() ?>
</body>
</html>
