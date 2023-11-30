<?php $__env->startSection('title', 'Files'); ?>


<?php $__env->startSection('content'); ?>
    <div id="body"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
    <script src="<?php echo asset('assets/libvelixs/ilsya.files.js'); ?>"></script>
    <script>
        var files = new FileManager({
            subfolder: "<?php echo e($auth->id); ?>",
            base_url: "<?php echo e(route('ilsya.files.index')); ?>"
        });

        files.init({
            body: "#body",
            ismain: true,
        })
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('dash.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\WHATSAPP-AUTO-SENDER\core\resources\views/dash/files.blade.php ENDPATH**/ ?>