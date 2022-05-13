<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>R-Kare</title>

    <link rel="stylesheet" href="<?php echo e(mix('css/app.css')); ?>">
</head>

<body>
    <div id="app"></div>

    <script>
        window.laravel = {
            "appUrl": "<?php echo e(config('app.url')); ?>",
        }
    </script>
    <script src="<?php echo e(mix('js/manifest.js')); ?>"></script>
    <script src="<?php echo e(mix('js/vendor.js')); ?>"></script>
    <script src="<?php echo e(mix('js/app.js')); ?>"></script>
</body>

</html>
<?php /**PATH /var/www/resources/views/welcome.blade.php ENDPATH**/ ?>