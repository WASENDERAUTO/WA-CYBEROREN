<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo asset('assets'); ?>/" data-template="vertical-menu-template">

<head>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="base_url" content="<?php echo e(asset('')); ?>">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>WALazy - <?php echo e(\Str::ucfirst(\Str::lower($__env->yieldContent('title')))); ?></title>
    <link rel="icon" type="image/x-icon" href="<?php echo asset('assets'); ?>/img/favicon.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo asset('assets'); ?>/vendor/fonts/tabler-icons.css" />
    <link rel="stylesheet" href="<?php echo asset('assets'); ?>/vendor/css/rtl/core.css?v=12" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?php echo asset('assets'); ?>/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?php echo asset('assets'); ?>/css/demo.css" />
    <link rel="stylesheet" href="<?php echo asset('assets'); ?>/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?php echo asset('assets'); ?>/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="<?php echo asset('assets'); ?>/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="<?php echo asset('assets'); ?>/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="<?php echo asset('assets'); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="<?php echo asset('assets'); ?>/vendor/libs/spinkit/spinkit.css" />
    <?php echo $__env->yieldPushContent('cssvendor'); ?>
    <script src="<?php echo asset('assets'); ?>/vendor/js/helpers.js"></script>
    <script src="<?php echo asset('assets'); ?>/vendor/js/template-customizer.js"></script>
    <script src="<?php echo asset('assets'); ?>/js/config.js"></script>
    <?php echo $__env->yieldPushContent('css'); ?>
    <style>
        .menu-icon {
            font-size: 1.475rem;
        }

        /* width */
        ::-webkit-scrollbar {
            width: 4px;
            height: 3px;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #888;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
    <script src="<?php echo asset('assets/libvelixs/velixs.js'); ?>"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php echo $__env->make('dash.layouts.menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="layout-page">
                <?php echo $__env->make('dash.layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="content-wrapper">
                    <div class="container-fluid flex-grow-1 container-p-y">
                        <?php echo $__env->yieldContent('content'); ?>
                    </div>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

    <script src="<?php echo asset('assets'); ?>/vendor/libs/jquery/jquery.js"></script>
    <script src="<?php echo asset('assets'); ?>/vendor/libs/popper/popper.js"></script>
    <script src="<?php echo asset('assets'); ?>/vendor/js/bootstrap.js"></script>
    <script src="<?php echo asset('assets'); ?>/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?php echo asset('assets'); ?>/vendor/libs/node-waves/node-waves.js"></script>
    <script src="<?php echo asset('assets'); ?>/vendor/libs/hammer/hammer.js"></script>
    <script src="<?php echo asset('assets'); ?>/vendor/libs/i18n/i18n.js"></script>
    <script src="<?php echo asset('assets'); ?>/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="<?php echo asset('assets'); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <?php echo $__env->yieldPushContent('jsvendor'); ?>
    <script src="<?php echo asset('assets'); ?>/vendor/js/menu.js"></script>
    <script src="<?php echo asset('assets'); ?>/js/main.js"></script>
    <script>
        $(".main-device").on('change', function(e) {
            var device = $(this).val();
            new velixs().ajax({
                url: "<?php echo e(route('ajax.change_device')); ?>",
                data: {
                    device: device,
                },
                addons_success: function({
                    swal
                }) {
                    swal.then(() => {
                        location.reload();
                    })
                },
            })
        });

        function main_device() {
            new velixs().ajax({
                url: "<?php echo e(route('ajax.main_device')); ?>",
                data: {
                    main_device_id: '<?php echo e($main_device ? $main_device->id : null); ?>',
                },
                success: function(data) {
                    $(".main-device").html(data);
                },
                beforeSend: function() {
                    $(".main-device").html('<option value="">Loading...</option>');
                },
            })
        }

        main_device();
    </script>
    <?php if($errors->any()): ?>
        <script>
            (function() {
                Swal.fire({
                    text: '<?php echo e($errors->first()); ?>',
                    icon: 'error',
                    showClass: {
                        popup: 'animate__animated animate__shakeX'
                    },
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                })
            })()
        </script>
    <?php endif; ?>
    <?php echo $__env->yieldPushContent('js'); ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\WHATSAPP-AUTO-SENDER\core\resources\views/dash/layouts/app.blade.php ENDPATH**/ ?>