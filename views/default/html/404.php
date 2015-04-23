<!DOCTYPE html>
<html>
    <head>
        <title>404 - Не найдено</title>
        <link href="<?php echo asset_url('css/main.css') ?>" rel="stylesheet" type="text/css"/>
    </head>
    
    <body>
        <?php view('blocks/header') ?>
        
        <article class="error">
            <p>Данная страница небыла найдена. Проверьте URL. Данная страница могла быть удалена с данного сайта.</p>
        </article>
        
        <?php view('blocks/footer') ?>
    </body>
</html>
<!-- templates/default/html/404.php -->
