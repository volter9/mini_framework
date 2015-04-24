<!DOCTYPE html>
<html>
    <head>
        <title>Ошибка - Exception was thrown</title>
        <link href="<?php echo asset_url('css/main.css') ?>" rel="stylesheet" type="text/css"/>
    </head>
    
    <body>
        <?php view('blocks/header') ?>
        
        <article class="error">
            <div class="left">
                <?php echo $exception->getMessage() ?>
            </div>
            
            <div class="right">
                <p>Стек:</p>
            
                <ul>
                    <?php foreach ($exception->getTrace() as $trace): ?>
                        <?php if (isset($trace['file'], $trace['line'])): ?>
                        <li>
                            В файле <code><?php echo $trace['file'] ?></code> 
                            на линии <?php echo $trace['line'] ?>
                        </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </article>
        
        <?php view('blocks/footer') ?>
    </body>
</html>
<!-- templates/default/html/error.php -->
