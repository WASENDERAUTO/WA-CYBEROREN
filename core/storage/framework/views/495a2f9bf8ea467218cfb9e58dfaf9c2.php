<?php $__env->startSection('title', 'DEVICE 404'); ?>


<?php $__env->startSection('content'); ?>

    <div class="card">
        <div class="card-body">
            <div class="text-center">
                <div class="misc-wrapper">
                    <h4 class="mb-1 mx-2">Select Device</h4>
                    <p class="mb-4 mx-2">Select the device first to continue</p>
                    <div class="row d-flex justify-content-center">
                        <div class="col-12 col-xl-6 col-lg-6">
                            <select class="form-control main-device" style="cursor: pointer;">
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('dash.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\WA-CYBEROREN\core\resources\views/nodevice.blade.php ENDPATH**/ ?>